<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Support Socialite features by allowing an empty password
    public bool $passwordIsSet = false;

    public function mount()
    {
        $this->passwordIsSet = !empty(auth()->user()->password);
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            // Conditional validation based on whether password is already set
            $rules = [
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ];

            // Only require current password validation if user already has a password
            if ($this->passwordIsSet) {
                $rules['current_password'] = ['required', 'string', 'current_password'];
            }

            $validated = $this->validate($rules);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        // Update the passwordIsSet flag since user now has a password
        $this->passwordIsSet = true;

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="$passwordIsSet ? __('Update password') : __('Set password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            @if($passwordIsSet)
                <flux:input
                    wire:model="current_password"
                    :label="__('Current password')"
                    type="password"
                    required
                    autocomplete="current-password"
                />
            @else
                <flux:callout icon="users" color="sky">
                    <flux:callout.text>
                        {{ __('You signed up using a social login. Set a password to enable password-based login for your account.') }}
                    </flux:callout.text>
                </flux:callout>
            @endif

            <flux:input
                wire:model="password"
                :label="__('New password')"
                type="password"
                required
                autocomplete="new-password"
            />
            
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ $passwordIsSet ? __('Update Password') : __('Set Password') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
