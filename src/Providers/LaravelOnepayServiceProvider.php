<?php

namespace NuocGanSoi\LaravelOnepay\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelOnepayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadViewsFrom(__DIR__.'/../views', 'onepay');

        $this->publishes([
            __DIR__ . '/../config/onepay.php' => config_path('onepay.php'),
            __DIR__.'/../views' => resource_path('views/vendor/onepay'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../Helpers/functions.php';

        $this->mergeConfigFrom(
            __DIR__.'/../config/onepay.php', 'onepay'
        );
    }
}
