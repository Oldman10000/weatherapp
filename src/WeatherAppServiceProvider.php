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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleMigrations();
        $this->handleViews();
        $this->handleRoutes();
        $this->handlePublic();

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->commands([CreateIpAddresses::class]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function handleViews()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'weather-app');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views'),
        ]);
    }

    private function handleRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/weather-routes.php');
        $this->publishes([
            __DIR__ . 'weather-routes.php' => base_path('routes'),
        ]);
    }

    private function handleMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path(
                'database/migrations'
            ),
        ]);
    }

    private function handlePublic()
    {
        $this->publishes(
            [
                __DIR__ . '/public' => public_path(''),
            ],
            'public'
        );
    }
}
