@props([
    'title' => __('Add location'),
    'button' => __('Confirm'),
    'locationIdBeingUpdated' => null
])

@once
<x-jet-dialog-modal wire:model="manageLocation">
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-slot name="content">
        <h2>
            {{ __('Location') }}
        </h2>
        <div class="flex flex-wrap flex-row -mx-3 mb-6">
            <div class="px-3 mb-6 md:mb-0">
                <x-jet-label for="name" value="{{ __('Location Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="locationForm.name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </div>
        <div class="flex flex-wrap flex-row -mx-3 mb-6">
            <div class="px-3 mb-6 md:mb-0">
                <x-jet-label for="timezone" value="{{ __('Location Timezone') }}" />
                <x-simple-select wire:model.defer="locationForm.timezone" id="timezone"
                    placeholder="{{ trans('Please select') }}"
                    :options="array_combine(
                        DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                        DateTimeZone::listIdentifiers(DateTimeZone::ALL)
                )" />
                <x-jet-input-error for="timezone" class="mt-2" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button wire:click="cancelManageLocation" wire:loading.attr="disabled">
            {{ __('Nevermind') }}
        </x-jet-secondary-button>

        @if($locationIdBeingUpdated)
            <x-jet-button class="ml-2" wire:click="confirmUpdateLocation" wire:loading.attr="disabled">
                {{ __('Update') }}
            </x-jet-button>
        @else
            <x-jet-button class="ml-2" wire:click="confirmAddLocation" wire:loading.attr="disabled">
                {{ $button }}
            </x-jet-button>
        @endif
    </x-slot>
</x-jet-dialog-modal>
@endonce
