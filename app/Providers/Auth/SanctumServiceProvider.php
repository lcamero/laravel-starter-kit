<?php

namespace App\Providers\Auth;

use App\Auth\Sanctum\Sanctum;
use Illuminate\Support\ServiceProvider;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Sanctum::class, function () {
            return new Sanctum;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(Sanctum::class)->defineAbilities([
            // 'create',
            // 'read',
            // 'update',
            // 'delete',
        ]);
    }
}
