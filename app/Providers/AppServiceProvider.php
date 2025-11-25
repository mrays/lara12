<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DuitkuService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DuitkuService::class, function ($app) {
            return new DuitkuService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
