<?php

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user without 2fa can log in via google and is redirected to the dashboard', function () {
    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = '12345';
    $socialiteUser->name = 'John Doe';
    $socialiteUser->email = 'john.doe@example.com';
    $socialiteUser->avatar = 'https://example.com/avatar.png';

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get(route('socialite.callback.google'));

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
