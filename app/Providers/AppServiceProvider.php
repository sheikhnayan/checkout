<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        if (!config('auth.guards.ambassador')) {
            config([
                'auth.guards.ambassador' => [
                    'driver' => 'session',
                    'provider' => 'ambassadors',
                ],
                'auth.providers.ambassadors' => [
                    'driver' => 'eloquent',
                    'model' => \App\Models\NightlyReportAmbassador::class,
                ],
            ]);
        }

        \Illuminate\Support\Facades\Event::subscribe(\App\Listeners\LogAuthenticationActivity::class);
    }
}
