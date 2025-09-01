<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        if (config('fortify.two_factor_authentication_enabled')) {
            Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        }

        // Custom view bindings
        Fortify::loginView(function () {
            return view('livewire.auth.login');
        });

        Fortify::registerView(function () {
            return view('livewire.auth.register');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('livewire.auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('livewire.auth.reset-password', ['request' => $request]);
        });

        Fortify::verifyEmailView(function () {
            return view('livewire.auth.verify-email');
        });

        Fortify::confirmPasswordView(function () {
            return view('livewire.auth.confirm-password');
        });

        if (config('fortify.two_factor_authentication_enabled', true)) {
            Fortify::twoFactorChallengeView(function () {
                return view('auth.two-factor-challenge');
            });
        }

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        if (config('fortify.two_factor_authentication_enabled')) {
            RateLimiter::for('two-factor', function (Request $request) {
                return Limit::perMinute(5)->by($request->session()->get('login.id'));
            });
        }
    }
}
