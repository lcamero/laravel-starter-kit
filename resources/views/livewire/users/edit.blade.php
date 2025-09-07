<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;

new class extends Component {
    #[Locked]
    public string $userId;

    public string $name = '';
    public string $email = '';  
    public array $roles = [];

    #[Computed]
    protected function user()
    {
        return User::findOrFail($this->userId);
    }

    public function mount($userId)
    {
        $this->authorize(\App\Enums\Permission::ManageApplicationUsers);

        $this->userId = $userId;

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->roles = $this->user->getRoleNames()->all();
    }

    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->userId)],
            'roles' => 'array',
            'roles.*' => ['string', 'max:255']
        ]);

        $this->user->update(['name' => $validated['name'], 'email' => $validated['email']]);

        // Remap to valid roles
        $this->user->syncRoles(collect($this->roles)
            ->map(fn (string $role) => \App\Enums\Role::tryFrom($role))
            ->filter()
            ->map(fn (\App\Enums\Role $role) => $role->value)
            ->all()
        );

        $this->redirect(route('users.index'), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="update" class="space-y-4">
        <div class="space-y-12">
            <flux:heading size="xl">{{ __('Update User') }}</flux:heading>
            <div class="w-full lg:w-4/5">
                <div class="grid grid-cols-2 gap-y-6">
                    <flux:input :label="__('Name')" wire:model="name" class="max-w-sm" required :badge="__('Required')" />
                    <flux:input :label="__('Email')" wire:model="email" class="max-w-sm" type="email" required :badge="__('Required')" />
                    <flux:checkbox.group wire:model="roles" :label="__('Roles')">
                        <flux:checkbox :value="\App\Enums\Role::Administrator->value" :label="\App\Enums\Role::Administrator->getLabel()"></flux:checkbox>
                    </flux:checkbox.group>
                </div>
            </div>
            <flux:button type="submit" variant="primary">
                {{ __('Update User') }}
            </flux:button>
        </div>
    </form>
</div>
