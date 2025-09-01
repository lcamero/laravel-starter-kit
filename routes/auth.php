<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\SocialiteGoogleController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('register', 'auth.register')
        ->name('register');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');

    Route::view('two-factor-challenge', 'auth.two-factor-challenge')
        ->name('two-factor.login');

    // Socialite support
    Route::get('/auth/{provider}/redirect', SocialiteController::class)
        ->name('socialite.redirect');

    Route::get('/auth/google/callback/', SocialiteGoogleController::class)
        ->name('socialite.callback.google');
});

Route::middleware('auth:sanctum')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});