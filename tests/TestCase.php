<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Tests;

use Illuminate\Config\Repository;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webparking\LimitedAccess\Http\Middleware\LimitedAccess;
use Webparking\LimitedAccess\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return string[]
     */
    public function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.key', 'base64:16fR1kx71oDF3lUl/bSqpdR6PCah9jKLYdeADpQabYE=');

        $config->set('limited-access.enabled', true);
        $config->set('limited-access.codes', 'never,gonna,give,you,up');
        $config->set('limited-access.ignore_ips', [
            '192.168.1.0/24',
            '2607:f0d0:1002:51::4/96',
        ]);
        $config->set('limited-access.block_ips', [
            '192.168.2.0/24',
            '2607:f0d0:1002:52::4/96',
        ]);

        /** @var Router $router */
        $router = $app['router'];

        $router->get('/secret-stuff', static fn (): string => 'This is very secret!')->middleware([
            StartSession::class,
            ShareErrorsFromSession::class,
            LimitedAccess::class,
        ]);
    }
}
