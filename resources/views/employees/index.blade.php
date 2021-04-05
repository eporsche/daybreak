<x-app-account-layout>
    <x-slot name="header">
        <x-h2 class="text-gray-100">{{ __('Employees') }}</x-h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('accounts.show-employees-for-account',['account' => $account])
        </div>
    </div>
</x-app-account-layout>
