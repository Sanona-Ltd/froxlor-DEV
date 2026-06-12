<?php

namespace App\Services\Webserver;

use App\Models\Domain;

class NginxConfigService implements WebserverInterface
{
    public function generateConfig(Domain $domain): string
    {
        $docRoot   = $domain->resolvedDocumentRoot();
        $ssl       = $domain->ssl_enabled;
        $phpSocket = $this->phpFpmSocket($domain->php_version);

        if ($ssl) {
            $config  = "server {\n";
            $config .= "    listen 80;\n";
            $config .= "    listen [::]:80;\n";
            $config .= "    server_name {$domain->name};\n";
            $config .= "    return 301 https://\$host\$request_uri;\n";
            $config .= "}\n\n";

            $config .= "server {\n";
            $config .= "    listen 443 ssl http2;\n";
            $config .= "    listen [::]:443 ssl http2;\n";
        } else {
            $config  = "server {\n";
            $config .= "    listen 80;\n";
            $config .= "    listen [::]:80;\n";
        }

        $config .= "    server_name {$domain->name};\n";
        $config .= "    root {$docRoot};\n";
        $config .= "    index index.php index.html;\n\n";

        if ($ssl) {
            $config .= "    ssl_certificate /etc/letsencrypt/live/{$domain->name}/fullchain.pem;\n";
            $config .= "    ssl_certificate_key /etc/letsencrypt/live/{$domain->name}/privkey.pem;\n";
            $config .= "    ssl_protocols TLSv1.2 TLSv1.3;\n";
            $config .= "    ssl_ciphers HIGH:!aNULL:!MD5;\n\n";
        }

        $config .= "    location / {\n";
        $config .= "        try_files \$uri \$uri/ /index.php?\$query_string;\n";
        $config .= "    }\n\n";

        if ($phpSocket) {
            $config .= "    location ~ \\.php\$ {\n";
            $config .= "        fastcgi_pass unix:{$phpSocket};\n";
            $config .= "        fastcgi_index index.php;\n";
            $config .= "        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;\n";
            $config .= "        include fastcgi_params;\n";
            $config .= "    }\n\n";
        }

        $config .= "    access_log /var/log/nginx/{$domain->name}-access.log;\n";
        $config .= "    error_log  /var/log/nginx/{$domain->name}-error.log;\n";
        $config .= "}\n";

        return $config;
    }

    public function getConfigPath(Domain $domain): string
    {
        return "/etc/nginx/sites-available/{$domain->name}.conf";
    }

    private function phpFpmSocket(?string $phpVersion): ?string
    {
        if (!$phpVersion) {
            return null;
        }

        return "/run/php/php{$phpVersion}-fpm.sock";
    }
}
