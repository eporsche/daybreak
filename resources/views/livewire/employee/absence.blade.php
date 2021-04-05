<div>
    <x-jet-validation-errors class="mb-4" />
    <div>
        <div class="bg-white border-gray-200 shadow mb-6 sm:rounded-lg ">
            <div class="sm:flex sm:flex-row border-b flex-wrap hidden">
                <!-- overall vacation details -->
                <div class="sm:w-1/5 w-full py-8">
                    <div class="text-sm uppercase text-grey tracking-wide border-r h-full flex flex-wrap justify-center content-center">
                        {{ __('Overall') }}
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanel['overall_vacation_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Overall entitled vacation') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanel['used_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                             {{ __('Used') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanel['transferred_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Transferred') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div>
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanel['available_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Rest') }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- vacation details until end of year -->
            <div class="flex flex-wrap sm:flex-row">
                <div class="sm:w-1/5 w-full text-center py-8">
                    <div class="text-sm uppercase text-grey tracking-wide border-r h-full flex flex-wrap justify-center content-center">
                        {{ __('Overall') }}<br>
                        {{ __('(until :date)', ['date' => $vacationInfoPanelUntil['until']] ) }}
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanelUntil['overall_vacation_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Overall entitled vacation') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanelUntil['used_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Used') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div class="sm:border-r">
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanelUntil['transferred_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Transferred') }}
                        </div>
                    </div>
                </div>
                <div class="sm:w-1/5 w-1/2 text-center py-8">
                    <div>
                        <div class="text-grey-darker mb-2">
                            <span class="text-xl align-top">{{ $vacationInfoPanelUntil['available_days'] }}</span>
                        </div>
                        <div class="text-sm uppercase text-grey tracking-wide">
                            {{ __('Rest') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="flex flex-row mb-4">
        <div class="flex justify-start flex-1">
            @can('switchEmployee',  [App\Model\Absence::class, $location])
                <x-simple-select wire:model="employeeIdToBeSwitched" wire:change="switchEmployee" id="employeeSwitcher" :options="$employeeSwitcher" />
            @endcan
        </div>
        <div class="flex justify-end flex-1">
            @can('addAbsence', [App\Model\Absence::class, $location])
                <x-jet-button class="py-3 px-4 " wire:click="openAbsenceModal" wire:loading.attr="disabled">
                    {{ __('Add Absence') }}
                </x-jet-button>
            @endcan
        </div>
    </div>

    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Type') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Start') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('End') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Hours') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Vacation') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->employee->absencesForLocation($this->location) as $absence)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                {{ $absence->absenceType->title }}
                            </td>
                            <td>
                                {{ $absence->starts_at_for_humans }}
                            </td>
                            <td>
                                {{ $absence->ends_at_for_humans }}
                            </td>
                            <td>
                                {{ $absence->paid_hours }}
                            </td>
                            <td>
                                {{ $absence->vacation_days }}
                            </td>
                            <td>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $absence->status_color }}-100 text-{{ $absence->status_color }}-800">
                                    {{ $absence->status }}
                                </span>
                            </td>
                            <td>
                                @if(Gate::check('approveAbsence',  [App\Model\Absence::class, $location]) && !$absence->isConfimred())
                                    <x-jet-button id="{{ md5($absence) }}" wire:click="approveAbsence({{ $absence->id }})" wire:loading.attr="disabled">
                                        {{ __('Approve') }}
                                    </x-jet-button>
                                @endif
                                <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                                        wire:click="confirmAbsenceRemoval({{ $absence->id }})">
                                    {{ __('Remove') }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7"  class="text-center p-2">
                                {{ __('No absences yet.') }}
                            </td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-jet-dialog-modal wire:model="absenceModal">
        <x-slot name="title">
            {{ __('Add absence') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1" x-data="{ open: @entangle('hideTime'), details: @entangle('hideDetails') }">
                <div class="mt-2">
                    <x-jet-label for="type" value="{{ __('Absence Type') }}" />
                    <x-simple-select wire:model="addAbsenceForm.absence_type_id" id="type" :options="$absenceTypes"/>
                    <x-jet-input-error for="absence_type_id" class="mt-2" />
                </div>
                <div class="mt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <x-jet-label for="startDate" value="{{ __('Starts at') }}" />
                            <x-date-picker id="startDate" wire:model="startDate" />
                            <x-jet-input-error for="starts_at" class="mt-2" />
                        </div>
                        <div>
                            <x-jet-label for="endDate" value="{{ __('Ends at') }}" />
                            <x-date-picker id="endDate" wire:model="endDate" />
                            <x-jet-input-error for="ends_at" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-2" x-show="open ? false : true">
                        <div class="mt-1 col-span-3">
                            <div class="inline-flex">
                                <x-simple-select wire:model="startHours" id="startHours" :options="$hours"/>
                                <div class="py-3 px-2">:</div>
                                <x-simple-select wire:model="startMinutes" id="startMinutes" :options="$minutes"/>
                            </div>
                        </div>
                        <div class="mt-1 col-span-2">
                            <div class="inline-flex">
                                <x-simple-select wire:model="endHours" id="endHours" :options="$hours"/>
                                <div class="py-3 px-2">:</div>
                                <x-simple-select wire:model="endMinutes" id="endMinutes" :options="$minutes"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex flex-col">
                        <div class="inline-flex">
                            <input id="full_day" wire:model="hideTime" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <x-jet-label for="full_day" class="ml-2 block text-sm text-gray-900" value="{{ __('Full day') }}" />
                        </div>
                        <x-jet-input-error for="full_day" class="mt-2" />
                    </div>
                </div>
                <div class="mt-2 overflow-hidden border-2 border-gray-200 sm:rounded-l overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Hours')  }}
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Vacation Days')  }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-6 py-2 whitespace-no-wrap">
                                    {{ $paidHours }}
                                </td>
                                <td class="px-6 py-2 whitespace-no-wrap">
                                    {{ $vacationDays }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <section class="shadow mt-2">
                    <article>
                        <div class="border-l-2 border-{{ $hideDetails ? 'transparent' : 'indigo-500' }}">
                            <header wire:click="$toggle('hideDetails')" class="flex justify-between items-center p-3 pl-5 pr-5 cursor-pointer select-none">
                                <span class="text-grey-darkest ">
                                    {{ __('Details') }}
                                </span>
                                <div x-show="details ? true  : false" class="rounded-full border border-grey w-7 h-7 flex items-center justify-center">
                                    <svg aria-hidden="true" class="" data-reactid="266" fill="none" height="24" stroke="#606F7B" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <polyline points="6 9 12 15 18 9">
                                        </polyline>
                                    </svg>
                                </div>
                                <div x-show="details ? false : true" class="rounded-full border border-indigo-500 w-7 h-7 flex items-center justify-center bg-indigo-500">
                                    <svg aria-hidden="true" data-reactid="281" fill="none" height="24" stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <polyline points="18 15 12 9 6 15">
                                        </polyline>
                                    </svg>
                                </div>
                            </header>
                            <div  x-show="details ? false : true">
                                <div class="pl-8 pr-8 pb-5 text-grey-darkest">
                                    <div class="mt-2 overflow-hidden border-2 border-gray-200 sm:rounded-l overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Date') }}
                                                    </th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Target Hours') }}
                                                    </th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Paid hours') }}
                                                    </th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ __('Vacation (calculative)') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($calculatedDays as $day)
                                                    <tr>
                                                        <td class="px-6 py-2 whitespace-no-wrap">
                                                            {{ $day->getDateForHumans() }}
                                                        </td>
                                                        <td class="px-6 py-2 whitespace-no-wrap">
                                                            {{ $day->getTargetHours()  }}
                                                        </td>
                                                        <td class="px-6 py-2 whitespace-no-wrap">
                                                            {{ $day->getPaidHours()  }}
                                                        </td>
                                                        <td class="px-6 py-2 whitespace-no-wrap">
                                                            {{ $day->getVacation()  }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">
                                                            {{ __('no details') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="closeAbsenceModal" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="addAbsence" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-confirmation-modal wire:model="confirmingAbsenceRemoval">
        <x-slot name="title">
            {{ __('Remove Absence') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this absence?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingAbsenceRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeAbsence" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
