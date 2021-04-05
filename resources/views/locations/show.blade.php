<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Location Settings') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('locations.update-location-name-form', ['location' => $user->currentLocation])

            @livewire('locations.absence-types-manager', ['location' => $user->currentLocation])

            @livewire('locations.public-holidays-manager', ['location' => $user->currentLocation])

            @livewire('locations.location-member-manager', ['location' => $user->currentLocation])
        </div>
    </div>
</x-app-layout>
