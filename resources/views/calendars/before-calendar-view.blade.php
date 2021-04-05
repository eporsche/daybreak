<div class="flex justify-between mb-3">
    <div>
        <x-jet-button wire:click="goToPreviousMonth" wire:loading.attr="disabled">
            {{ __('Previous') }}
        </x-jet-button>
        <x-jet-button wire:click="goToNextMonth" wire:loading.attr="disabled">
            {{ __('Next') }}
        </x-jet-button>
        <x-jet-button wire:click="goToCurrentMonth" wire:loading.attr="disabled">
            {{ __('Current') }}
        </x-jet-button>
    </div>
    <div>
        {{ __('Month: :month Year: :year', [
            'month' => $startsAt->format('M'),
            'year' => $startsAt->format('Y')
        ]) }}
    </div>
</div>
