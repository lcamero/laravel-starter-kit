<x-layouts.auth :title="__('Two-Factor Challenge')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Two-Factor Challenge')" />

        <div class="text-sm text-zinc-600 dark:text-zinc-400">
            <span x-show="!recovery">
                {{ __('Please enter the authentication code from your authenticator app to continue.') }}
            </span>
            <span x-show="recovery" style="display: none;">
                {{ __('Please enter one of your emergency recovery codes to continue.') }}
            </span>
        </div>

        <form method="POST" action="{{ route('two-factor.login.store') }}" class="flex flex-col gap-6" x-data="{ recovery: {{ $errors->has('recovery_code') || old('recovery_code') ? 'true' : 'false' }} }">
            @csrf

            <!-- Authentication Code -->
            <div x-show="!recovery">
                <flux:input
                    :label="__('Authentication Code')"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    x-bind:required="!recovery"
                    autofocus
                    autocomplete="one-time-code"
                    :placeholder="__('000000')"
                    maxlength="6"
                    :value="old('code')"
                />
            </div>

            <!-- Recovery Code -->
            <div x-show="recovery" style="display: none;">
                <flux:input
                    :label="__('Recovery Code')"
                    name="recovery_code"
                    type="text"
                    x-bind:required="recovery"
                    autocomplete="one-time-code"
                    :placeholder="__('recovery-code')"
                    :value="old('recovery_code')"
                />
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="flex items-center justify-between">
                <button 
                    type="button" 
                    class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100"
                    @click="recovery = !recovery"
                >
                    <span x-show="!recovery">{{ __('Use recovery code') }}</span>
                    <span x-show="recovery" style="display: none;">{{ __('Use authentication code') }}</span>
                </button>

                <flux:button variant="primary" type="submit" class="ml-auto">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth>