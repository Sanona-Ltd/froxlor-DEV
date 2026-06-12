<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WAF Cookie Lifetime
    |--------------------------------------------------------------------------
    | How many minutes a visitor's "passed" cookie stays valid.
    | Default: 4 hours (240 minutes)
    */
    'cookie_lifetime' => env('WAF_COOKIE_LIFETIME', 240),

    /*
    |--------------------------------------------------------------------------
    | Challenge Token Window
    |--------------------------------------------------------------------------
    | Seconds per HMAC time-bucket. A challenge token stays valid for this
    | duration. Default: 300 seconds (5 minutes).
    */
    'token_window' => env('WAF_TOKEN_WINDOW', 300),

    /*
    |--------------------------------------------------------------------------
    | WAF Cookie Name
    |--------------------------------------------------------------------------
    */
    'cookie_name' => env('WAF_COOKIE_NAME', 'frx_waf_pass'),

    /*
    |--------------------------------------------------------------------------
    | Bypass Paths
    |--------------------------------------------------------------------------
    | These path prefixes are always allowed through without WAF checks.
    | Include the Froxlor admin/panel routes and the challenge endpoint itself.
    */
    'bypass_paths' => [
        '/admin',
        '/panel',
        '/login',
        '/logout',
        '/waf',
        '/up',
    ],

    /*
    |--------------------------------------------------------------------------
    | Log WAF Events
    |--------------------------------------------------------------------------
    | Whether to write challenge/block events to the waf_logs table.
    */
    'logging' => env('WAF_LOGGING', true),

];
