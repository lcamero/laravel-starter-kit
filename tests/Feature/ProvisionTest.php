<?php

use App\Enums\Role;
use App\Models\User;

it('creates a seeded administrator with the Administrator role', function () {
    $user = User::where('email', config('settings.provision_admin_email'))->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole(Role::Administrator->value))->toBeTrue();
});
