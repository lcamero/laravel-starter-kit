<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading(! app()->isProduction());
        Model::automaticallyEagerLoadRelationships();

        Gate::define('viewPulse', function (User $user) {
            return true;
            // return $user->isAdmin();
        });

        // Implicitly grant "Administrator" role all permissions.
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function (User $user, $ability) {
            return $user->hasRole(Role::Administrator) ? true : null;
        });

        // Mostly when provisioning, the settings db does not exist yet
        try {
            config(['app.name' => app(\App\Settings\GeneralSettings::class)->application_name]);
        } catch (\Exception $e) {
        }
    }
}
