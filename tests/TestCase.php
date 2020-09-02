<?php


declare(strict_types=1);

namespace Webparking\LimitedAccess\Tests;

use Webparking\LimitedAccess\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    public function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}