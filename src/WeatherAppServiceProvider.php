<?php

namespace Oldman10000\WeatherApp;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Oldman10000\WeatherApp\Console\createIpAddresses;

class WeatherAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Oldman10000\WeatherApp\WeatherController');
        $this->loadViewsFrom(__DIR__ . '/views', 'weather');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';

        $this->publishes(
            [
                __DIR__ . '/public' => public_path(''),
            ],
            'public'
        );

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([CreateIpAddresses::class]);
        }

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../views', 'weather-app');
    }
}
