<x-app-account-layout>
    <x-slot name="header">
        <x-h2 class="text-gray-100">{{ __('Employee: :name',[ 'name' => $employee->name ]) }}</x-h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('employees.edit-user-profile',['employee' => $employee])
        </div>
    </div>
</x-app-account-layout>
