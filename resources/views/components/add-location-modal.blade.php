@props(['title' => __('Add location'), 'button' => __('Confirm')])

@once
<x-jet-dialog-modal wire:model="addLocation">
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
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="addLocationForm.name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button wire:click="cancelAddLocation" wire:loading.attr="disabled">
            {{ __('Nevermind') }}
        </x-jet-secondary-button>

        <x-jet-button class="ml-2" wire:click="confirmAddLocation" wire:loading.attr="disabled">
            {{ $button }}
        </x-jet-button>
    </x-slot>
</x-jet-dialog-modal>
@endonce
