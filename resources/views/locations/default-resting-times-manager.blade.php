<div>
    @if (Gate::check('addDefaultRestingTime', $location))
        <x-jet-section-border />
        <div class="mt-10 sm:mt-0">
            <x-jet-action-section submit="addLocationMember">
                <x-slot name="title">
                    {{ __('Add Default Resting Time') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Add a default resting time to new time trackings.') }}
                </x-slot>

                <x-slot name="content">
                    <div class="overflow-x-auto">
                        <div class="py-2">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Pause from x hours') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Duration of the pause in minutes') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($defaultRestingTimes as $defaultRestingTime)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $defaultRestingTime->min_hours->inHours() }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $defaultRestingTime->duration->inMinutes() }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmDefaultRestingTimeRemoval('{{ $defaultRestingTime->id }}')">
                                                    {{ __('Remove') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colpsan="3" class="text-center p-2">
                                                {{ __('No default durations yet.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        {{ $defaultRestingTimes->links() }}
                    </div>
                    <div class="flex items-center mt-5">
                        <x-jet-button wire:click="openDefaultRestingTimeModal" wire:loading.attr="disabled">
                            {{ __('Add default resting time') }}
                        </x-jet-button>
                        <x-jet-action-message class="ml-3" on="savedDefaultRestingTime">
                            {{ __('Saved.') }}
                        </x-jet-action-message>
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>
    @endif

    <!-- Add manual public holiday -->
    <x-jet-dialog-modal wire:model="addDefaultRestingTimeModal">
        <x-slot name="title">
            {{ __('Add default resting time') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="min_hours" value="{{ __('Pause from x hours') }}" />
                <x-jet-input type="number" min="0" step="1" id="min_hours" class="mt-1 block w-full" wire:model.defer="defaultRestingTimeForm.min_hours" autofocus/>
                <x-jet-input-error for="min_hours" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="duration" value="{{ __('Duration of the pause in minutes') }}" />
                <x-jet-input type="number" min="0" step="1" id="duration" class="mt-1 block w-full" wire:model.defer="defaultRestingTimeForm.duration" autofocus/>
                <x-jet-input-error for="duration" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="closeDefaultRestingTimeModal" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="addDefaultRestingTime" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>

            <x-jet-action-message class="mr-3" on="savedDefaultRestingTime">
                {{ __('Saved.') }}
            </x-jet-action-message>

        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-confirmation-modal wire:model="confirmingDefaultRestingTimeRemoval">
        <x-slot name="title">
            {{ __('Remove Default Pause Time') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove the default pause time from this location?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingDefaultRestingTimeRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removePublicHoliday" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
