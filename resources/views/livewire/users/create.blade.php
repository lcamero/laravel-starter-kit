<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate([
        'roles' => 'array',
        'roles.*' => ['string', 'max:255']
    ])]
    public array $roles = [];

    public function mount()
    {
        $this->authorize(\App\Enums\Permission::ManageApplicationUsers);
    }

    public function create(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make(Str::random(16)),
        ]);

        // Remap to valid roles
        if (!empty($this->roles)) {
            $user->assignRole(collect($this->roles)
                ->map(fn (string $role) => \App\Enums\Role::tryFrom($role))
                ->filter()
                ->map(fn (\App\Enums\Role $role) => $role->value)
                ->all()
            );
        }

        $user->notify(new \App\Notifications\UserInvitation);

        $this->redirect(route('users.index'), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="create" class="space-y-4">
        <div class="space-y-12">
            <flux:heading size="xl">{{ __('users.create_user') }}</flux:heading>
            <div class="w-full lg:w-4/5">
                <div class="grid grid-cols-2 gap-y-6">
                    <flux:input :label="__('common.name')" wire:model="name" class="max-w-sm" required :badge="__('common.required')" />
                    <flux:input :label="__('common.email')" wire:model="email" class="max-w-sm" type="email" required :badge="__('common.required')" />
                    <flux:checkbox.group wire:model="roles" :label="__('common.roles')">
                        <flux:checkbox :value="\App\Enums\Role::Administrator->value" :label="\App\Enums\Role::Administrator->getLabel()"></flux:checkbox>
                    </flux:checkbox.group>
                </div>
            </div>

            <flux:button type="submit" variant="primary">
                {{ __('users.create_user') }}
            </flux:button>

            <flux:callout class="w-full max-w-lg" variant="warning">
                <flux:callout.heading icon="exclamation-triangle">{{ __('common.important') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('users.random_password_notice') }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </form>
</div>
