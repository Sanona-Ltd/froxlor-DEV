<?php

namespace App\Enums;

enum Webserver: string
{
    case Apache        = 'apache';
    case Nginx         = 'nginx';
    case OpenLiteSpeed = 'openlitespeed';

    public function label(): string
    {
        return match($this) {
            Webserver::Apache        => 'Apache',
            Webserver::Nginx         => 'Nginx',
            Webserver::OpenLiteSpeed => 'OpenLiteSpeed',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            Webserver::Apache        => 'red',
            Webserver::Nginx         => 'green',
            Webserver::OpenLiteSpeed => 'orange',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
