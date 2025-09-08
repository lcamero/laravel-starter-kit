<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('navigation.settings') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('settings.manage_personal_and_app_config') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:heading class="lg:hidden mt-2">{{ __('settings.select_category') }}</flux:heading>
    <flux:select class="w-full max-w-sm lg:hidden mt-2" x-on:change="Livewire.navigate($event.target.value)">
        @can(\App\Enums\Permission::ManageApplicationSettings)
        <flux:select.option value="{{ route('settings.general', absolute: false) }}" :selected="request()->routeIs('settings.general')">{{ __('settings.general') }}</flux:select.option>
        @endcan
        <flux:select.option value="{{ route('settings.profile', absolute: false) }}" :selected="request()->routeIs('settings.profile')">{{ __('settings.profile') }}</flux:select.option>
        <flux:select.option value="{{ route('settings.authentication', absolute: false) }}" :selected="request()->routeIs('settings.authentication')">{{ __('settings.authentication') }}</flux:select.option>
        @if (\App\Auth\Sanctum::apiTokensEnabled())
        <flux:select.option value="{{ route('settings.api-tokens', absolute: false) }}" :selected="request()->routeIs('settings.api-tokens')">{{ __('settings.api_tokens') }}</flux:select.option>
        @endif
    </flux:select>
    
    <div class="flex items-start max-md:flex-col gap-6 mt-6 lg:mt-10">
        <div class="hidden lg:block">
            @can(\App\Enums\Permission::ManageApplicationSettings)
            <div class="relative w-full mb-6 flex items-center gap-1">
                <flux:icon.wrench-screwdriver class="size-4" />
                <flux:heading size="lg" level="2">{{ __('settings.application_configuration') }}</flux:heading>
            </div>
            <div class="me-10 w-full pb-4 md:w-[220px]">
                <flux:navlist>
                    <flux:navlist.item :href="route('settings.general')" :current="request()->routeIs('settings.general')" wire:navigate>{{ __('settings.general') }}</flux:navlist.item>
                </flux:navlist>
            </div>

            <flux:separator class="my-12" />
            @endcan

            <div class="relative w-full mb-6 flex items-center gap-1">
                <flux:icon.cog class="size-4" />
                <flux:heading size="lg" level="2">{{ __('settings.personal_preferences') }}</flux:heading>
            </div>
            <div class="me-10 w-full pb-4 md:w-[220px]">
                <flux:navlist>
                    <flux:navlist.item :href="route('settings.profile')" :current="request()->routeIs('settings.profile')" wire:navigate>{{ __('settings.profile') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('settings.authentication')" :current="request()->routeIs('settings.authentication')" wire:navigate>{{ __('settings.authentication') }}</flux:navlist.item>
                    @if (\App\Auth\Sanctum::apiTokensEnabled())
                    <flux:navlist.item :href="route('settings.api-tokens')" :current="request()->routeIs('settings.api-tokens')" wire:navigate>{{ __('settings.api_tokens') }}</flux:navlist.item>
                    @endif
                </flux:navlist>
            </div>
        </div>

        <flux:separator vertical class="hidden lg:block" />
        
        <div class="flex-1 self-stretch max-md:pt-6">
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>
            <div class="mt-5 w-full max-w-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
