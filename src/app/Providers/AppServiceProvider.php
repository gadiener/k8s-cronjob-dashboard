<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maclof\Kubernetes\Client;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->app->singleton('Maclof\Kubernetes\Client', function ($app) {
            return new Client([
                'master'  => config('services.kubernetes.api'),
                'ca_cert' => config('services.kubernetes.ca-path'),
                'token'   => config('services.kubernetes.token'),
                'namespace' => config('services.kubernetes.namespace')
            ]);
        });
    }
}
