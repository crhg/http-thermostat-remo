<?php

namespace App\Providers;

use Crhg\RemoClient\Api\DefaultApi;
use Crhg\RemoClient\Configuration;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(DefaultApi::class, function ($app) {
            $configuration = $app->make(Configuration::class);
            return new DefaultApi(null, $configuration);
        });
        app()->bind(Configuration::class, function() {
            $token = config('app.remo_token');
            $configuration = new Configuration();
            $configuration->setAccessToken($token);
            return $configuration;
        });
    }
}
