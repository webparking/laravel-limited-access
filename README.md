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

### optional steps
Publish the config
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

## Licence and Postcardware
This software is open source and licensed under the [MIT license](LICENSE.md).

If you use this software in your daily development we would appreciate to receive a postcard of your hometown. 

Please send it to: Webparking BV, Cypresbaan 31a, 2908 LT Capelle aan den IJssel, The Netherlands
