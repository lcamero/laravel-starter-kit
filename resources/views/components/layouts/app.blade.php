<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main container>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
