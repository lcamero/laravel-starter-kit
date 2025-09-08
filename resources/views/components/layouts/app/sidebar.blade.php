<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:brand
                href="{{ route('dashboard') }}"
                name="{{ config('app.name') }}"
                class="ml-4"
            >
                <x-slot name="logo">
                    <div class="flex aspect-square size-6 p-1 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                    </div>
                </x-slot>
            </flux:brand>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('navigation.platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('navigation.dashboard') }}</flux:navlist.item>
                    @can(\App\Enums\Permission::ManageApplicationUsers)
                    <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                        {{ __('navigation.users') }}
                    </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                @if (app()->isLocal())
                <flux:navlist.item icon="folder-git-2" href="https://github.com/lcamero/laravel-starter-kit" target="_blank">
                {{ __('navigation.repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs" target="_blank">
                {{ __('navigation.documentation') }}
                </flux:navlist.item>
                @endif
                @can(\App\Enums\Permission::ManageApplicationSettings)
                <flux:navlist.item icon="wrench-screwdriver" :href="route('settings.general')" :current="request()->routeIs('settings.general')" wire:navigate>
                    {{ __('navigation.configuration') }}
                </flux:navlist.item>
                @endcan
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                    :avatar="auth()->user()->avatar"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="my-2">
                        <flux:radio value="light" icon="sun"></flux:radio>
                        <flux:radio value="dark" icon="moon"></flux:radio>
                        <flux:radio value="system" icon="computer-desktop"></flux:radio>
                    </flux:radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('navigation.preferences') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        @if (Route::has('horizon.index'))
                        <flux:menu.item :href="route('horizon.index')" target="_blank" icon="circle-stack">{{ __('navigation.horizon') }}</flux:menu.item>
                        @endif
                        @if (Route::has('telescope') && config('telescope.enabled'))
                        <flux:menu.item :href="route('telescope')" target="_blank" icon="lifebuoy">{{ __('navigation.telescope') }}</flux:menu.item>
                        @endif
                        @if (Route::has('pulse') && config('pulse.enabled'))
                        <flux:menu.item :href="route('pulse')" target="_blank" icon="heart">{{ __('navigation.pulse') }}</flux:menu.item>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('common.log_out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    :avatar="auth()->user()->avatar"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="my-2">
                        <flux:radio value="light" icon="sun"></flux:radio>
                        <flux:radio value="dark" icon="moon"></flux:radio>
                        <flux:radio value="system" icon="computer-desktop"></flux:radio>
                    </flux:radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('navigation.preferences') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        @if (Route::has('horizon.index'))
                        <flux:menu.item :href="route('horizon.index')" target="_blank" icon="circle-stack">{{ __('navigation.horizon') }}</flux:menu.item>
                        @endif
                        @if (Route::has('telescope') && config('telescope.enabled'))
                        <flux:menu.item :href="route('telescope')" target="_blank" icon="lifebuoy">{{ __('navigation.telescope') }}</flux:menu.item>
                        @endif
                        @if (Route::has('pulse') && config('pulse.enabled'))
                        <flux:menu.item :href="route('pulse')" target="_blank" icon="heart">{{ __('navigation.pulse') }}</flux:menu.item>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('common.log_out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
