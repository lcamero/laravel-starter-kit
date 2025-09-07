<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use \Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url] 
    public $search = '';

    public ?int $userToDelete = null;

    public function mount()
    {
        $this->authorize(\App\Enums\Permission::ManageApplicationUsers);
    }

    #[Computed]
    public function users()
    {
        return User::search($this->search)
            ->query(fn ($query) => $query->with('roles')->latest())
            ->paginate();
    }

    public function confirmUserDeletion($userId): void
    {
        $this->userToDelete = $userId;
        $this->dispatch('open-modal', 'confirm-user-deletion');
    }

    public function deleteUser(): void
    {
        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();
        }
        
        $this->redirect(route('users.index'), navigate: true);
    }
}; ?>

<div>
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center">
            <flux:heading size="xl" level="1" class="mb-6">{{ __('Users') }}</flux:heading>
            <flux:button :href="route('users.create')" variant="primary" size="sm">
                {{ __('New user') }}
            </flux:button>
        </div>
        <flux:separator variant="subtle" />
    </div>

    <flux:input icon="magnifying-glass" size="sm" :placeholder="__('Search')" wire:model.live.debounce.300ms="search" class="max-w-xs mb-6" />

    <div class="relative overflow-x-auto">
        <table class="w-full lg:w-4/5 text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        {{ __('Name') }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __('Email') }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __('Created at') }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __('Roles') }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->users as $user)
                    <tr class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 border-zinc-200">
                        <th scope="row" class="px-6 py-4 font-medium text-zinc-900 whitespace-nowrap dark:text-white">
                            {{ $user->name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->created_at }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->roles->pluck('name')->join(', ') }}
                        </td>
                        <td>
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" size="sm"></flux:button>
    
                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('users.edit', ['userId' => $user->id]) }}" wire:navigate>
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                    <flux:modal.trigger name="confirm-user-deletion">
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmUserDeletion('{{ $user->id }}')">{{ __('Delete') }}</flux:menu.item>
                                    </flux:modal.trigger>
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="w-full lg:w-4/5 mt-4 text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            {{ $this->users->links() }}
        </div>
    </div>

    <flux:modal name="confirm-user-deletion" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6 p-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete this user?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Once this user is deleted, all of its resources and data will be permanently deleted. This action cannot be undone') }}
                </flux:subheading>

                @if(auth()->user()->id === $userToDelete)
                <flux:callout
                    variant="warning"
                    icon="exclamation-triangle"
                    :heading="__('If you delete your account you will be logged out immediately.')"
                    class="mt-6 text-sm!"
                />
                @endif
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Delete user') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
