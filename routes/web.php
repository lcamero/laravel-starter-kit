<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $readme = File::get(base_path('README.md'));

    $readmeHtml = Str::markdown($readme);

    return view('welcome', [
        'readme' => $readmeHtml,
    ]);
})->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/authentication', 'settings.authentication')->name('settings.authentication');
    Volt::route('settings/api-tokens', 'settings.api-tokens')->name('settings.api-tokens');
});

require __DIR__.'/auth.php';
