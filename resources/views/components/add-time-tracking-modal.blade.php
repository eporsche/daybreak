@props([
    'hours',
    'minutes',
    'employee',
    'pauseTimeForm',
    'timeTrackingIdBeingUpdated' => null,
    'title' => __('Time Tracking'),
    'button' => __('Confirm')
    ])

@once
<x-jet-dialog-modal wire:model="manageTimeTracking">
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-col space-y-3">
            <x-jet-input-error for="time_tracking_id" class="mt-2" />
            <x-jet-input-error for="duration_in_minutes" class="mt-2" />
        </div>

        <div class="flex flex-col space-y-3">
            <div>
                <x-jet-label for="date" value="{{ __('Date') }}" />
                <x-date-picker id="date" wire:model="timeTrackingForm.date" />
                <x-jet-input-error for="date" class="mt-2" />
            </div>
            <div class="flex flex-wrap flex-row space-x-2">
                <div>
                    <x-jet-label for="startTime" value="{{ __('Start') }}" />
                    <div class="flex flex-row">
                        <x-simple-select wire:model="timeTrackingForm.start_hour" id="startHour" :options="$hours"/>
                        <div class="py-3 px-2">:</div>
                        <x-simple-select wire:model="timeTrackingForm.start_minute" id="startMinute" :options="$minutes"/>
                    </div>
                    <x-jet-input-error for="starts_at" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="endTime" value="{{ __('End') }}" />
                    <div class="flex flex-row">
                        <x-simple-select wire:model="timeTrackingForm.end_hour" id="endHour" :options="$hours" />
                        <div class="py-3 px-2">:</div>
                        <x-simple-select wire:model="timeTrackingForm.end_minute" id="endMinute" :options="$minutes"/>
                    </div>
                    <x-jet-input-error for="ends_at" class="mt-2" />
                </div>
            </div>

            @if(App\Daybreak::hasProjectBillingFeature())
                <x-dynamic-component component="project-form" :employee="$employee"/>
            @endif

            <div class="flex flex-col">
                <x-jet-label for="description" value="{{ __('Description') }}" />
                <x-textarea id="description" type="text" class="mt-1 block w-full
                bg-white cursor-pointer border-gray-800
                focus:ring-1 focus:ring-blue-800
                " wire:model.defer="timeTrackingForm.description" autofocus />
                <x-jet-input-error for="description" class="mt-2" />
            </div>
            <div class="flex-col items-center mt-5">
                <x-jet-button wire:click="addPauseTime" wire:loading.attr="disabled">
                    {{ __('Add Pause') }}
                </x-jet-button>
                <x-jet-input-error for="pause" class="mt-2" />
            </div>
            @forelse($pauseTimeForm as $index => $pause)
                <div class="flex flex-row">
                    @livewire(
                        'time-tracking.pause-times',
                        [
                            'pause' => $pause,
                            'index' => $index
                        ],
                        key($index)
                    )
                    <div class="flex-grow self-end">
                        <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="removePause({{$index}})" wire:loading.attr="disabled">
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="mt-2">
                    {{ __('No pause yet.') }}
                </div>
            @endforelse
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button wire:click="cancelManagingTimeTracking" wire:loading.attr="disabled">
            {{ __('Nevermind') }}
        </x-jet-secondary-button>

        @if($timeTrackingIdBeingUpdated)
            <x-jet-button class="ml-2" wire:click="confirmUpdateTimeTracking" wire:loading.attr="disabled">
                {{ __('Update') }}
            </x-jet-button>
        @else
            <x-jet-button class="ml-2" wire:click="confirmAddTimeTracking" wire:loading.attr="disabled">
                {{ $button }}
            </x-jet-button>
        @endif
    </x-slot>
</x-jet-dialog-modal>
@endonce
