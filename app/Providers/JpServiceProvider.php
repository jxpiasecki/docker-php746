<?php

namespace App\Providers;

use App\Services\Jp\Client as JpClient;
use App\Services\Jp\Jp;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class JpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            JpClient::class,
            function (Application $app) {
                return new JpClient(env('JP_CLIENT_PASSWORD', ''));
            }
        );

        $this->app->bind(
            'Jp',
            function (Application $app) {
                return new Jp();
            }
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
