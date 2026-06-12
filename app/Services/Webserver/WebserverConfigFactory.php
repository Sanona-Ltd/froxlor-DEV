<?php

namespace App\Services\Webserver;

use App\Enums\Webserver;
use InvalidArgumentException;

class WebserverConfigFactory
{
    public static function make(string|Webserver $webserver): WebserverInterface
    {
        $type = $webserver instanceof Webserver ? $webserver : Webserver::from($webserver);

        return match($type) {
            Webserver::Apache        => new ApacheConfigService(),
            Webserver::Nginx         => new NginxConfigService(),
            Webserver::OpenLiteSpeed => new OpenLiteSpeedConfigService(),
            default                  => throw new InvalidArgumentException("Unsupported webserver: {$type->value}"),
        };
    }
}
