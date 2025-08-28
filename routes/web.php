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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/two-factor', 'settings.two-factor')->name('settings.two-factor');
});

require __DIR__.'/auth.php';
