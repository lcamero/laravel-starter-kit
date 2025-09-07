<?php

use App\Auth\Sanctum;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Sanctum::enableApiTokens();
    Sanctum::permissions([]);
    Sanctum::defaultPermissions([]);
});

test('api tokens settings screen cannot be rendered if sanctum is disabled', function () {
    Sanctum::enableApiTokens(false);

    $this->actingAs($user = User::factory()->create());

    $this->get(route('settings.api-tokens'))->assertSee(__('API Token Management is disabled.'));
});

test('api tokens can be created', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test('settings.api-tokens')
        ->set('tokenName', 'Test Token')
        ->call('createToken');

    expect($user->fresh()->tokens)->toHaveCount(1);
    expect($user->fresh()->tokens->first()->name)->toBe('Test Token');
    expect($user->fresh()->tokens->first()->abilities)->toBe(['*']);
});

test('api tokens can be created with abilities', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test('settings.api-tokens')
        ->set('tokenName', 'Test Token')
        ->set('permissions', ['read', 'create'])
        ->call('createToken');

    expect($user->fresh()->tokens)->toHaveCount(1);
    expect($user->fresh()->tokens->first()->name)->toBe('Test Token');
    expect($user->fresh()->tokens->first()->abilities)->toEqual(['read', 'create']);
});

test('api tokens can be deleted', function () {
    $this->actingAs($user = User::factory()->create());
    $token = $user->createToken('Test Token', ['read', 'create']);

    Livewire::test('settings.api-tokens')
        ->call('confirmDeleteToken', $token->accessToken->id)
        ->call('deleteToken');

    expect($user->fresh()->tokens)->toHaveCount(0);
});

test('a token name is required', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('settings.api-tokens')
        ->set('tokenName', '')
        ->call('createToken')
        ->assertHasErrors(['tokenName' => 'required']);
});
