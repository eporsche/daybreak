<x-app-layout>
    <x-slot name="header">
        <x-h2>{{ __('Calendar') }}</x-h2>
    </x-slot>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <livewire:location-calendar
            :employee="$employee"/>
    </div>
</x-app-layout>
