@inject('dateFormatter', 'App\Formatter\DateFormatter')

<div>
    <div class="mt-10 sm:mt-0">
        <x-jet-section-border />
        <div class="mt-10 sm:mt-0">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Public holidays') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Here you can add, edit and delete public holidays for this location.') }}
                </x-slot>

                <x-slot name="content">
                    <div class="overflow-x-auto">
                        <div class="py-2">
                            <table class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Day') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Half day')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($holidays as $publicHoliday)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $publicHoliday->title }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $dateFormatter->formatDateForView($publicHoliday->day) }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $publicHoliday->public_holiday_half_day }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmPublicHolidayRemoval('{{ $publicHoliday->id }}')">
                                                    {{ __('Remove') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colpsan="4" class="text-center p-2">
                                                {{ __('No public holidays yet.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        {{ $holidays->links() }}
                    </div>
                    <div class="flex items-center mt-5">
                        <x-jet-button wire:click="openPublicHolidayModal" wire:loading.attr="disabled">
                            {{ __('Add Public Holiday') }}
                        </x-jet-button>
                        <x-jet-button class="ml-1" wire:click="$toggle('importPublicHoliday')" wire:loading.attr="disabled">
                            {{ __('Import Public Holiday') }}
                        </x-jet-button>
                        <x-jet-action-message class="ml-3" on="savedPublicHoliday">
                            {{ __('Saved.') }}
                        </x-jet-action-message>
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>
    </div>


    <!-- Add manual public holiday -->
    <x-jet-dialog-modal wire:model="addPublicHolidayModal">
        <x-slot name="title">
            {{ __('Add public holiday') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="publicHolidayForm.name" autofocus />
                <x-jet-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="date" value="{{ __('Date') }}" />
                <x-date-picker id="date" wire:model.defer="publicHolidayForm.date" autofocus />
                <x-jet-input-error for="date" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <div class="inline-flex">
                    <input id="half_day" wire:model.defer="publicHolidayForm.half_day" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" disabled>
                    <x-jet-label for="half_day" class="ml-2 block text-sm text-gray-900" value="{{ __('Half day') }}" />
                </div>
                <x-jet-input-error for="half_day" class="ml-2 mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="closePublicHolidayModal" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="addPublicHoliday" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Deletion confirmation -->

    <x-jet-confirmation-modal wire:model="confirmingPublicHolidayRemoval">
        <x-slot name="title">
            {{ __('Remove Public Holiday') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove the public holiday from this location?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingPublicHolidayRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removePublicHoliday" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

    <!-- Importer Modal Window -->
    <x-jet-dialog-modal wire:model="importPublicHoliday">
        <x-slot name="title">
            {{ __('Import public holidays') }}
        </x-slot>
        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="yearToBeImported" value="{{ __('Select year for import') }}" />
                <x-simple-select wire:model.defer="yearToBeImported" id="yearToBeImported" :options="$importYears" />
                <x-jet-input-error for="import_year" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="countryToBeImported" value="{{ __('Select country') }}" />
                <x-simple-select wire:model.defer="countryToBeImported" id="countryToBeImported" :options="$importCountries" />
                <x-jet-input-error for="import_country" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('importPublicHoliday')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="importPublicHoliday" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
