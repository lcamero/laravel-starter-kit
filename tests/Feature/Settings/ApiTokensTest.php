<?php

use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

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
        ->set('abilities', ['read', 'create'])
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
