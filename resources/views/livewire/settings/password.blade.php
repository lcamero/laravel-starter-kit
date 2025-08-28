<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Support Socialite features by allowing an empty password
    public bool $passwordIsSet = false;

    public bool $confirmingPasswordWith2fa = false;
    public string $two_factor_code = '';

    public function mount()
    {
        $this->passwordIsSet = ! empty(auth()->user()->password);
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        $user = Auth::user();
        $twoFactorEnabled = $user->two_factor_secret && ! is_null($user->two_factor_confirmed_at);

        // Validate passwords
        try {
            $rules = [
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ];
            if ($this->passwordIsSet) {
                $rules['current_password'] = ['required', 'string', 'current_password'];
            }
            $validated = $this->validate($rules);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        if ($twoFactorEnabled) {
            if ($this->confirmingPasswordWith2fa) {
                // 2FA confirmation step
                $this->validate(['two_factor_code' => ['required', 'string']]);

                $provider = app(TwoFactorAuthenticationProvider::class);
                if (! $provider->verify(decrypt($user->two_factor_secret), $this->two_factor_code)) {
                    throw ValidationException::withMessages([
                        'two_factor_code' => [__('The provided two factor authentication code was invalid.')],
                    ]);
                }

                // 2FA code is valid. Proceed with password update.
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);

                $this->reset('current_password', 'password', 'password_confirmation', 'two_factor_code');
                $this->confirmingPasswordWith2fa = false;
                $this->passwordIsSet = true;
                $this->dispatch('password-updated');

            } else {
                // 2FA is enabled, but we haven't asked for the code yet.
                $this->confirmingPasswordWith2fa = true;
            }
        } else {
            // No 2FA, just update the password.
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            $this->reset('current_password', 'password', 'password_confirmation');
            $this->passwordIsSet = true;
            $this->dispatch('password-updated');
        }
    }

    public function cancelPasswordUpdateConfirmation(): void
    {
        $this->confirmingPasswordWith2fa = false;
        $this->reset('current_password', 'password', 'password_confirmation', 'two_factor_code');
        $this->resetErrorBag('two_factor_code');
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
                viewable
            />
            
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            @if ($confirmingPasswordWith2fa)
                <div class="mt-6 space-y-6">
                    <flux:callout icon="exclamation-triangle" color="amber" :heading="__('Please confirm access to your account by entering the authentication code provided by your authenticator application.')"></flux:callout>
                    <flux:input
                        wire:model="two_factor_code"
                        wire:keydown.enter="updatePassword"
                        :label="__('Two-Factor Code')"
                        type="text"
                        required
                        autocomplete="one-time-code"
                        autofocus
                        maxlength="6"
                    />
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end gap-4">
                    @if ($confirmingPasswordWith2fa)
                        <flux:button wire:click.prevent="cancelPasswordUpdateConfirmation">
                            {{ __('Cancel') }}
                        </flux:button>
                    @endif

                    <flux:button variant="primary" type="submit" class="w-full">
                        @if ($confirmingPasswordWith2fa)
                            {{ __('Confirm') }}
                        @else
                            {{ $passwordIsSet ? __('Update Password') : __('Set Password') }}
                        @endif
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
