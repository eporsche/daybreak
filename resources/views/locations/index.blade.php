<x-app-account-layout>
    <x-slot name="header">
        <x-h2 class="text-gray-100">{{ __('Locations') }}</x-h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('locations.location-manager', [
                'account' => $account,
                'employee' => $user
            ])
        </div>
    </div>
</x-app-account-layout>
