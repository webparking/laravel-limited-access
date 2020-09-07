<h1 align="center">
  Laravel limited access
</h1>

![Tests](https://github.com/webparking/laravel-limited-access/workflows/Tests/badge.svg)

This package is meant to provide an easy limited access layer to your application.

With this package you can specify codes to provide access to your application, block ips entirely or whitelist ips.

## Installation
Add this package to composer.

```shell script
composer require webparking/laravel-limited-access
```

Publish assets:

```shell script
php artisan vendor:publish --provider="Webparking\LimitedAccess\ServiceProvider" --tag="public"
```

### Optional steps

Publish config:

```shell script
php artisan vendor:publish --provider="Webparking\LimitedAccess\ServiceProvider" --tag="config"
```

Publish translations:

```shell script
php artisan vendor:publish --provider="Webparking\LimitedAccess\ServiceProvider" --tag="lang"
```

Or just publish all the things:

```shell script
php artisan vendor:publish --provider="Webparking\LimitedAccess\ServiceProvider"
```

## Usage
To use this package you need to enable it by either enabling the package in the config or adding the following to your .env file.
```dotenv
LIMITED_ACCESS_ENABLED=true
```

To add access codes add the following key to your .env file and specify your access codes in a comma separated list.
```dotenv
LIMITED_ACCESS_CODES=comma,separated,access,codes
```

## Blocking/Ignoring IP addresses

In `config/limited-access` you can add single IP addresses or IP address ranges in CIDR format.

Example:

```php
return [
    'block_ips' => [
        '192.168.1.0/24',
        '66.66.66.66', // 66.66.66.66/32 is equivalent
        '2000:ffff::1',
        '2000:ffff/64',
    ],

    'ignore_ips' => [
        '11.22.33.44',
        '10.0.0.0/8',
        '127.0.0.1',
        '::1',
    ],
];
```

Blocked IPs take priority over ignored IPs, so adding the same address or range to both arrays will cause the given IP to be blocked.

You can leave both of these arrays empty if you wish for everyone to be prompted for a password.

## Licence and Postcardware
This software is open source and licensed under the [MIT license](LICENSE.md).

If you use this software in your daily development we would appreciate to receive a postcard of your hometown. 

Please send it to: Webparking BV, Cypresbaan 31a, 2908 LT Capelle aan den IJssel, The Netherlands
