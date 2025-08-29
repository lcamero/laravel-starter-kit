<?php

namespace App\Providers\Auth;

use App\Auth\Sanctum;
use Illuminate\Support\ServiceProvider;

class SanctumServiceProvider extends ServiceProvider
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
        Sanctum::enableApiTokens();

        Sanctum::defaultPermissions([
            // 'read',
        ]);

        Sanctum::permissions([
            // 'create',
            // 'read',
            // 'update',
            // 'delete',
        ]);
    }
}
