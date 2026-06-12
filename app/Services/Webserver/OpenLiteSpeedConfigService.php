<?php

namespace App\Services\Webserver;

use App\Models\Domain;

class OpenLiteSpeedConfigService implements WebserverInterface
{
    public function generateConfig(Domain $domain): string
    {
        $docRoot = $domain->resolvedDocumentRoot();
        $ssl     = $domain->ssl_enabled;

        $config  = "virtualHost {$domain->name} {\n";
        $config .= "    vhRoot           {$docRoot}/\n";
        $config .= "    configFile       \$SERVER_ROOT/conf/vhosts/{$domain->name}/vhconf.conf\n";
        $config .= "    allowSymbolLink  1\n";
        $config .= "    enableScript     1\n";
        $config .= "    restrained       0\n";
        $config .= "    setUIDMode       2\n";
        $config .= "}\n\n";

        $config .= "listener HTTP {\n";
        $config .= "    address          *:80\n";
        $config .= "    secure           0\n";
        $config .= "    map              {$domain->name} {$domain->name}\n";
        $config .= "}\n";

        if ($ssl) {
            $config .= "\nlistener HTTPS {\n";
            $config .= "    address          *:443\n";
            $config .= "    secure           1\n";
            $config .= "    keyFile          /etc/letsencrypt/live/{$domain->name}/privkey.pem\n";
            $config .= "    certFile         /etc/letsencrypt/live/{$domain->name}/fullchain.pem\n";
            $config .= "    map              {$domain->name} {$domain->name}\n";
            $config .= "}\n";
        }

        return $config;
    }

    public function getVhostConfig(Domain $domain): string
    {
        $docRoot = $domain->resolvedDocumentRoot();

        $config  = "docRoot              \$VH_ROOT/\n";
        $config .= "vhDomain             {$domain->name}\n\n";
        $config .= "index  {\n";
        $config .= "    useServer        0\n";
        $config .= "    indexFiles       index.php, index.html\n";
        $config .= "}\n\n";
        $config .= "rewrite  {\n";
        $config .= "    enable           1\n";
        $config .= "    autoLoadHtaccess 1\n";
        $config .= "}\n\n";
        $config .= "context / {\n";
        $config .= "    type             NULL\n";
        $config .= "    location         {$docRoot}\n";
        $config .= "    allowBrowse      1\n";
        $config .= "}\n";

        return $config;
    }

    public function getConfigPath(Domain $domain): string
    {
        return "/usr/local/lsws/conf/httpd_config.conf";
    }

    public function getVhostConfigPath(Domain $domain): string
    {
        return "/usr/local/lsws/conf/vhosts/{$domain->name}/vhconf.conf";
    }
}
