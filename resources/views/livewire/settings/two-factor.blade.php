<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Livewire\Volt\Component;

new class extends Component {
    public string $code = '';
    public bool $showingQrCode = false;
    public bool $showingConfirmation = false;
    public bool $showingRecoveryCodes = false;
    public array $recoveryCodes = [];

    public bool $confirming2faDisable = false;
    public string $two_factor_code_for_disable = '';

    public bool $confirmingRecoveryCodes = false;
    public string $recoveryCodeForConfirmation = '';
    public string $recoveryCodeIntention = '';

    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        if (!config('fortify.two_factor_authentication_enabled')) {
            return;
        }

        $enable(Auth::user());

        $this->showingQrCode = true;
        $this->showingConfirmation = true;

        if (Auth::user()->two_factor_confirmed_at) {
            $this->showingQrCode = false;
            $this->showingConfirmation = false;
            $this->displayRecoveryCodes();
        }
    }

    public function displayRecoveryCodes(): void
    {
        $this->recoveryCodes = json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true);
        $this->showingRecoveryCodes = true;
    }

    public function confirmActionWith2fa(string $intention): void
    {
        $this->confirmingRecoveryCodes = true;
        $this->recoveryCodeIntention = $intention;
    }

    public function handleRecoveryConfirmation(TwoFactorAuthenticationProvider $provider, GenerateNewRecoveryCodes $generate): void
    {
        if (!config('fortify.two_factor_authentication_enabled')) {
            return;
        }

        $this->validate(['recoveryCodeForConfirmation' => ['required', 'string']]);

        $this->ensureValidTwoFactorCode($provider, $this->recoveryCodeForConfirmation, 'recoveryCodeForConfirmation');

        if ($this->recoveryCodeIntention === 'regenerate') {
            $generate(Auth::user());
            session()->flash('status', 'recovery-codes-generated');
        }

        $this->displayRecoveryCodes();

        $this->confirmingRecoveryCodes = false;
        $this->reset('recoveryCodeForConfirmation', 'recoveryCodeIntention');
        $this->resetErrorBag('recoveryCodeForConfirmation');
    }

    public function cancelRecoveryConfirmation(): void
    {
        $this->confirmingRecoveryCodes = false;
        $this->reset('recoveryCodeForConfirmation', 'recoveryCodeIntention');
        $this->resetErrorBag('recoveryCodeForConfirmation');
    }

    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm): void
    {
        if (!config('fortify.two_factor_authentication_enabled')) {
            return;
        }

        $this->validateOnly('code', ['code' => 'required']);

        $confirm(Auth::user(), $this->code);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->displayRecoveryCodes();

        session()->flash('status', 'two-factor-authentication-enabled');
    }

    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable, TwoFactorAuthenticationProvider $provider): void
    {
        if (!config('fortify.two_factor_authentication_enabled')) {
            return;
        }

        if ($this->confirming2faDisable) {
            $this->validate(['two_factor_code_for_disable' => ['required', 'string']]);

            $this->ensureValidTwoFactorCode($provider, $this->two_factor_code_for_disable, 'two_factor_code_for_disable');

            $disable(Auth::user());

            $this->showingQrCode = false;
            $this->showingConfirmation = false;
            $this->showingRecoveryCodes = false;
            $this->confirming2faDisable = false;
            $this->reset('two_factor_code_for_disable');

            session()->flash('status', 'two-factor-authentication-disabled');
        } else {
            $this->confirming2faDisable = true;
        }
    }

    public function cancel2faDisableConfirmation(): void
    {
        $this->confirming2faDisable = false;
        $this->reset('two_factor_code_for_disable');
        $this->resetErrorBag('two_factor_code_for_disable');
    }

    protected function ensureValidTwoFactorCode(TwoFactorAuthenticationProvider $provider, string $code, string $errorBag): void
    {
        if (! $provider->verify(decrypt(Auth::user()->two_factor_secret), $code)) {
            throw ValidationException::withMessages([
                $errorBag => [__('The provided two factor authentication code was invalid.')],
            ]);
        }
    }

    public function mount(): void
    {
        if (Auth::user()->two_factor_confirmed_at && Auth::user()->two_factor_recovery_codes) {
            $this->recoveryCodes = json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true);
        }
    }

    public function twoFactorQrCodeSvg(): string
    {
        return Auth::user()->twoFactorQrCodeSvg();
    }

    public function setupKey(): string
    {
        return decrypt(Auth::user()->two_factor_secret);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')
    <x-settings.layout :heading="__('Two Factor Authentication')" :subheading="__('Add additional security to your account using two factor authentication.')">
        @if (config('fortify.two_factor_authentication_enabled'))
        <div class="space-y-6">
            <div class="max-w-xl">
                @if (!Auth::user()->two_factor_confirmed_at)
                    <!-- Two Factor Authentication is not enabled -->
                    <div class="space-y-4">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                        </p>

                        <flux:button wire:click="enableTwoFactorAuthentication" variant="primary">
                            {{ __('Enable Two Factor Authentication') }}
                        </flux:button>
                    </div>

                    @if ($showingQrCode)
                        <div class="mt-6 space-y-4">
                            @if ($showingConfirmation)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                                </p>
                            @else
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                                </p>
                            @endif

                            <div class="p-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                {!! $this->twoFactorQrCodeSvg() !!}
                            </div>

                            <div class="space-y-2">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ __('Setup Key:') }} 
                                    <code class="bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded text-xs">{{ $this->setupKey() }}</code>
                                </p>
                            </div>

                            @if ($showingConfirmation)
                                <div class="space-y-4">
                                    <flux:input
                                        wire:model="code"
                                        wire:keydown.enter="confirmTwoFactorAuthentication"
                                        :label="__('Code')"
                                        type="text"
                                        inputmode="numeric"
                                        placeholder="000000"
                                        maxlength="6"
                                        autocomplete="one-time-code"
                                    />

                                    <div class="flex gap-2">
                                        <flux:button wire:click="confirmTwoFactorAuthentication" variant="primary">
                                            {{ __('Confirm') }}
                                        </flux:button>

                                        <flux:button wire:click="disableTwoFactorAuthentication" variant="ghost">
                                            {{ __('Cancel') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                @else
                    <!-- Two Factor Authentication is enabled -->
                    <div class="space-y-4">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('You have enabled two factor authentication.') }}
                        </p>

                        @if ($showingRecoveryCodes)
                            <div class="space-y-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                                </p>

                                <flux:callout icon="exclamation-triangle" color="amber">
                                    <flux:callout.text>
                                        <strong>{{ __('Important: Recovery codes are single-use only') }}</strong><br>
                                        {{ __('Each recovery code can only be used once. Once used, it becomes invalid and cannot be reused. If you run out of recovery codes, you must regenerate them from this page.') }}
                                    </flux:callout.text>
                                </flux:callout>

                                <div class="grid grid-cols-2 gap-2 p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                                    @foreach ($recoveryCodes as $code)
                                        <div class="font-mono text-sm">{{ $code }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            @if ($confirming2faDisable)
                                <div class="w-full space-y-4">
                                    <flux:callout icon="exclamation-triangle" color="amber" :heading="__('Please confirm access to your account by entering the authentication code provided by your authenticator application.')"></flux:callout>
                                    <flux:input
                                        wire:model="two_factor_code_for_disable"
                                        wire:keydown.enter="disableTwoFactorAuthentication"
                                        :label="__('Two-Factor Code')"
                                        type="text"
                                        required
                                        autocomplete="one-time-code"
                                        autofocus
                                        maxlength="6"
                                    />
                                    <div class="flex items-center justify-end gap-4">
                                        <flux:button wire:click.prevent="cancel2faDisableConfirmation">
                                            {{ __('Cancel') }}
                                        </flux:button>
                                
                                        <flux:button wire:click.prevent="disableTwoFactorAuthentication" variant="primary">
                                            {{ __('Confirm') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @elseif ($confirmingRecoveryCodes)
                                <div class="w-full space-y-4">
                                    <flux:callout icon="exclamation-triangle" color="amber" :heading="__('Please confirm access to your account by entering the authentication code provided by your authenticator application.')"></flux:callout>
                                    <flux:input
                                        wire:model="recoveryCodeForConfirmation"
                                        wire:keydown.enter="handleRecoveryConfirmation"
                                        :label="__('Two-Factor Code')"
                                        type="text"
                                        required                                    
                                        autocomplete="one-time-code"
                                        maxlength="6"
                                        autofocus
                                    />
                                    <div class="flex items-center justify-end gap-4">
                                        <flux:button wire:click.prevent="cancelRecoveryConfirmation">
                                            {{ __('Cancel') }}
                                        </flux:button>
                                
                                        <flux:button wire:click.prevent="handleRecoveryConfirmation" variant="primary">
                                            {{ __('Confirm') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @else
                                @if (!$showingRecoveryCodes)
                                    <flux:button wire:click="confirmActionWith2fa('show')" variant="outline">
                                        {{ __('Show Recovery Codes') }}
                                    </flux:button>
                                @else
                                    <flux:button wire:click="confirmActionWith2fa('regenerate')" variant="outline">
                                        {{ __('Regenerate Recovery Codes') }}
                                    </flux:button>
                                @endif
        
                                <flux:button wire:click="disableTwoFactorAuthentication" variant="danger">
                                    {{ __('Disable Two Factor Authentication') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
    <flux:callout icon="exclamation-triangle" color="amber" :heading="__('Two-Factor Authentication is disabled.')"></flux:callout>
    @endif
    </x-settings.layout>
</section>