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

    Route::redirect('settings', '/settings/profile');

    // General settings
    Volt::route('settings/general', 'settings.general')->name('settings.general')->can(\App\Enums\Permission::ManageApplicationSettings);

    // Personal preferences
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/authentication', 'settings.authentication')->name('settings.authentication');
    Volt::route('settings/api-tokens', 'settings.api-tokens')->name('settings.api-tokens');

    Route::middleware('can:'.\App\Enums\Permission::ManageApplicationUsers->value)
        ->prefix('users')
        ->name('users.')
        ->group(function () {
            Volt::route('/', 'users.index')->name('index');
            Volt::route('create', 'users.create')->name('create');
            Volt::route('{userId}/edit', 'users.edit')->name('edit');
        });
});

require __DIR__.'/auth.php';
