<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\ReservationService;
use App\Services\StripeService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeService::class, fn($app) => new StripeService());
        $this->app->singleton(ReservationService::class, fn($app) => new ReservationService($app->make(StripeService::class)));
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Custom pagination view using our dark theme
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.simple-custom');
    }
}
