<?php

use App\Auth\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {
    public string $tokenName = '';
    public array $abilities = [];
    public ?string $plainTextToken = null;
    public bool $showTokenModal = false;
    public ?int $tokenToDeleteId = null;
    public bool $showDeleteModal = false;
    public Collection $tokens;

    public function mount(): void
    {
        $this->tokens = Auth::user()->tokens;
    }

    public function createToken(): void
    {
        $this->validate([
            'tokenName' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string'],
        ]);

        $this->plainTextToken = Auth::user()->createToken(
            $this->tokenName,
            empty($this->abilities) ? ['*'] : $this->abilities
        )->plainTextToken;

        $this->tokens = Auth::user()->tokens;
        $this->tokenName = '';
        $this->abilities = [];
        $this->showTokenModal = true;
    }

    public function confirmDeleteToken(int $tokenId): void
    {
        $this->tokenToDeleteId = $tokenId;
        $this->showDeleteModal = true;
    }

    public function deleteToken(): void
    {
        if (! $this->tokenToDeleteId) {
            return;
        }

        Auth::user()->tokens()->where('id', $this->tokenToDeleteId)->delete();
        $this->tokens = Auth::user()->tokens;

        $this->tokenToDeleteId = null;
        $this->showDeleteModal = false;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('API Tokens')" :subheading="__('Manage API tokens for your account.')">
        <div class="my-6 w-full space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading level="3">{{ __('Create API Token') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 gap-y-6 bg-zinc-100 dark:bg-zinc-700 p-4 rounded-md">
                <flux:input wire:model="tokenName" :label="__('Token Name')" type="text" required />

                @if (app(App\Auth\Sanctum\Sanctum::class)->abilities())
                <flux:label>{{ __('Abilities') }}</flux:label>
                <flux:checkbox.group class="">
                    <flux:checkbox.all :label="__('Check all')" />
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        @foreach (app(App\Auth\Sanctum\Sanctum::class)->abilities() as $ability)
                            <label class="flex items-center">
                                <flux:checkbox wire:model="abilities" :value="$ability" :label="$ability"/>
                            </label>
                        @endforeach
                    </div>
                </flux:checkbox.group>
                @endif
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" wire:click="createToken">{{ __('Create Token') }}</flux:button>
            </div>
        </div>

        @if ($plainTextToken)
            <flux:modal wire:model="showTokenModal" max-width="lg">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">
                            {{ __('API Token') }}
                        </flux:heading>

                        <flux:text class="mt-2">
                            <p>{{ __('Here is your new API token. This is the only time it will be shown, so please copy it to a safe place.') }}</p>
                        </flux:text>
                    </div>
                    <div class="mt-4 rounded-md bg-zinc-100 p-4 font-mono text-sm dark:bg-zinc-700">
                        {{ $plainTextToken }}
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />

                        <flux:modal.close>
                            <flux:button>{{ __('Close') }}</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
        @endif

        <flux:modal wire:model="showDeleteModal" max-width="lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Delete API Token') }}
                    </flux:heading>

                    <flux:text class="mt-2">
                        <p>{{ __('Are you sure you want to delete this API token?') }}</p>
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button>{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="deleteToken">{{ __('Delete') }}</flux:button>
                </div>
            </div>
        </flux:modal>

        <div class="my-6 w-full space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading level="3">{{ __('Manage API Tokens') }}</flux:heading>
            </div>

            <div class="space-y-4">
                @forelse ($tokens as $token)
                    <div class="flex items-center justify-between rounded-md p-4 bg-zinc-100 dark:bg-zinc-700">
                        <div class="">
                            <flux:heading>
                                {{ $token->name }} - {{ __('Last used') }} {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : __('never') }}
                            </flux:heading>
                            
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mt-2">
                                @foreach ($token->abilities as $ability)
                                    <span class="text-xs">
                                        {{ $ability }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <flux:button variant="danger" wire:click="confirmDeleteToken({{ $token->id }})">{{ __('Delete') }}</flux:button>
                    </div>
                @empty
                    <p>{{ __('You have no API tokens.') }}</p>
                @endforelse
            </div>
        </div>
    </x-settings.layout>
</section>
