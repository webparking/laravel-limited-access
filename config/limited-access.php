<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Specified codes that provide access to the application
    |--------------------------------------------------------------------------
    |
    | Comma separated list of codes.
    | These codes will provide access to the application.
    |
    */
    'codes' => env('LIMITED_ACCESS_CODES', ''),

    /*
    |--------------------------------------------------------------------------
    | Whether limited access is enabled or not
    |--------------------------------------------------------------------------
    */
    'enabled' => env('LIMITED_ACCESS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | List of ips to always be blocked
    |--------------------------------------------------------------------------
    |
    | These ips will always be blocked as long as limited access is enabled.
    | supports cidr ranges e.g. 192.168.0.0/32
    |
    */
    'block_ips' => [],

    /*
    |--------------------------------------------------------------------------
    | List of ips ignored by limited access
    |--------------------------------------------------------------------------
    |
    | These ips will be ignored by limited access and will always be granted
    | access.
    | supports cidr ranges e.g. 192.168.0.0/32
    |
    */
    'ignore_ips' => [],
];
