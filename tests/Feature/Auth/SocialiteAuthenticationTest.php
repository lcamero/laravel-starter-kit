<?php

use App\Models\User;
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

test('a user with 2fa enabled is redirected to the two-factor challenge page', function () {
    $user = User::factory()->create([
        'two_factor_secret' => 'test-secret',
    ]);

    $socialiteUser = new SocialiteUser;
    $socialiteUser->id = '12345';
    $socialiteUser->name = $user->name;
    $socialiteUser->email = $user->email;
    $socialiteUser->avatar = 'https://example.com/avatar.png';

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get(route('socialite.callback.google'));

    $this->assertGuest();
    $this->assertEquals($user->id, session()->get('login.id'));
    $response->assertRedirect(route('two-factor.login'));
});

test('2fa settings screen cannot be rendered if it is disabled', function () {
    config()->set('fortify.two_factor_authentication_enabled', false);
    
    $this->actingAs($user = User::factory()->create());
    
    $this->get(route('settings.two-factor'))->assertSee(__('Two-Factor Authentication is disabled.'));
});