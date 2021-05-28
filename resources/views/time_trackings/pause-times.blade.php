<div>
    <div class="flex flex-wrap flex-row space-x-2">
        <div>
            <x-jet-label for="startTime" value="{{ __('Start') }}" />
            <div class="flex flex-row">
                <x-simple-select wire:model="pause.start_hour" wire:change="changedTime"  id="startHour" :options="$hours"/>
                <div class="py-3 px-2">:</div>
                <x-simple-select wire:model="pause.start_minute" wire:change="changedTime" id="startMinute" :options="$minutes"/>
            </div>
            <x-jet-input-error for="starts_at" class="mt-2" />
        </div>
        <div>
            <x-jet-label for="endTime" value="{{ __('End') }}" />
            <div class="flex flex-row">
                <x-simple-select wire:model="pause.end_hour" wire:change="changedTime" id="endHour" :options="$hours" />
                <div class="py-3 px-2">:</div>
                <x-simple-select wire:model="pause.end_minute" wire:change="changedTime" id="endMinute" :options="$minutes"/>
            </div>
            <x-jet-input-error for="ends_at" class="mt-2" />
        </div>
    </div>
</div>
