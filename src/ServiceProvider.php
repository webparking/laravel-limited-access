<?php

namespace Webparking\LimitedAccess;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Webparking\LimitedAccess\Http\Middleware\LimitedAccess;

class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/limited-access.php',
            'limited-access'
        );
    }

    public function boot(Router $router): void
    {
        $this->registerWebRoutes();
        $this->registerViews();
        $this->registerTranslations();

        $router->middlewarePriority = [
            LimitedAccess::class,
            SubstituteBindings::class,
        ];

        $router->pushMiddlewareToGroup('web', LimitedAccess::class);

        $this->publishes([
            __DIR__ . '/../config/limited-access.php' => config_path('limited-access.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/limited-access'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/limited-access'),
        ], 'public');
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'LimitedAccess');
    }

    private function registerWebRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    private function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'LimitedAccess');
    }
}
