<x-app-layout>
    <x-slot name="header">
        <x-h2 class="text-gray-800">{{ __('Time Tracking') }}</x-h2>
    </x-slot>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        @livewire('time-tracking.time-tracking-manager')
    </div>
</x-app-layout>
