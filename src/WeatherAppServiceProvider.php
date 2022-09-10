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

        if (!class_exists('CreateIpAddressTable')) {
            $this->publishes(
                [
                    __DIR__ .
                    '/../database/migrations/create_allowed_ip_addresses_table.php.stub' => database_path(
                        'migrations/' .
                            date('Y_m_d_His', time()) .
                            '_create_allowed_ip_addresses_table.php'
                    ),
                    // you can add any number of migrations here
                ],
                'migrations'
            );
        }

        if (!class_exists('CreateWeatherReportTable')) {
            $this->publishes(
                [
                    __DIR__ .
                    '/../database/migrations/create_weather_report_table.php.stub' => database_path(
                        'migrations/' .
                            date('Y_m_d_His', time()) .
                            '_create_weather_report_table.php'
                    ),
                    // you can add any number of migrations here
                ],
                'migrations'
            );
        }
    }
}
