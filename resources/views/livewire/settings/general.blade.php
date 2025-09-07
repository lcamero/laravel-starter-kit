<?php

use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public string $application_name = '';

    /**
     * Mount the component.
     */
    public function mount(GeneralSettings $generalSettings): void
    {
        $this->authorize(\App\Enums\Permission::ManageApplicationSettings);

        $this->application_name = $generalSettings->application_name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function update(): void
    {
        $validated = $this->validate([
            'application_name' => ['required', 'string', 'max:255'],
        ]);

        $generalSettings = app(GeneralSettings::class);

        $generalSettings->fill($validated);

        $generalSettings->save();

        $this->redirect(route('settings.general'), navigate: true);
    }
}; ?>

<section class="w-full">
    <x-settings.layout :heading="__('General')" :subheading="__('Manage general system settings')">
        <form wire:submit="update" class="my-6 w-full space-y-6">
            <flux:input wire:model="application_name" :label="__('Application Name')" type="text" required autofocus autocomplete="application_name" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>
            </div>
        </form>
    </x-settings.layout>
</section>
