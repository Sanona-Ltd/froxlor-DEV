<?php

namespace App\Services\Webserver;

use App\Models\Domain;

class ApacheConfigService implements WebserverInterface
{
    public function generateConfig(Domain $domain): string
    {
        $docRoot   = $domain->resolvedDocumentRoot();
        $ssl       = $domain->ssl_enabled;
        $phpSocket = $this->phpFpmSocket($domain->php_version);

        $config  = "<VirtualHost *:80>\n";
        $config .= "    ServerName {$domain->name}\n";
        $config .= "    DocumentRoot {$docRoot}\n\n";
        $config .= "    <Directory {$docRoot}>\n";
        $config .= "        AllowOverride All\n";
        $config .= "        Require all granted\n";
        $config .= "    </Directory>\n\n";

        if ($phpSocket) {
            $config .= "    <FilesMatch \\.php\$>\n";
            $config .= "        SetHandler \"proxy:unix:{$phpSocket}|fcgi://localhost\"\n";
            $config .= "    </FilesMatch>\n\n";
        }

        if ($ssl) {
            $config .= "    Redirect permanent / https://{$domain->name}/\n";
        }

        $config .= "    ErrorLog \${APACHE_LOG_DIR}/{$domain->name}-error.log\n";
        $config .= "    CustomLog \${APACHE_LOG_DIR}/{$domain->name}-access.log combined\n";
        $config .= "</VirtualHost>\n";

        if ($ssl) {
            $config .= "\n<VirtualHost *:443>\n";
            $config .= "    ServerName {$domain->name}\n";
            $config .= "    DocumentRoot {$docRoot}\n\n";
            $config .= "    SSLEngine on\n";
            $config .= "    SSLCertificateFile /etc/letsencrypt/live/{$domain->name}/fullchain.pem\n";
            $config .= "    SSLCertificateKeyFile /etc/letsencrypt/live/{$domain->name}/privkey.pem\n\n";
            $config .= "    <Directory {$docRoot}>\n";
            $config .= "        AllowOverride All\n";
            $config .= "        Require all granted\n";
            $config .= "    </Directory>\n\n";

            if ($phpSocket) {
                $config .= "    <FilesMatch \\.php\$>\n";
                $config .= "        SetHandler \"proxy:unix:{$phpSocket}|fcgi://localhost\"\n";
                $config .= "    </FilesMatch>\n\n";
            }

            $config .= "    ErrorLog \${APACHE_LOG_DIR}/{$domain->name}-ssl-error.log\n";
            $config .= "    CustomLog \${APACHE_LOG_DIR}/{$domain->name}-ssl-access.log combined\n";
            $config .= "</VirtualHost>\n";
        }

        return $config;
    }

    public function getConfigPath(Domain $domain): string
    {
        return "/etc/apache2/sites-available/{$domain->name}.conf";
    }

    private function phpFpmSocket(?string $phpVersion): ?string
    {
        if (!$phpVersion) {
            return null;
        }

        return "/run/php/php{$phpVersion}-fpm.sock";
    }
}
