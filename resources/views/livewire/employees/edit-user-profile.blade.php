<div>
    <div class="mt-10 sm:mt-0">
        <x-jet-action-section>
            <x-slot name="title">
                {{ __('Add Target Hours') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Based on employees target hours, overtime will be calculated.') }}
            </x-slot>

            <x-slot name="content">
                <div class="col-span-6">
                    <div class="text-sm text-gray-600">
                        <div class="overflow-hidden border-2 border-gray-200 sm:rounded-l overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Start at') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Summary') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Can create overtime')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->employee->targetHours as $targetHour)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $targetHour->start_date_for_humans }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $targetHour->createTargetHourSummary() }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $targetHour->target_limited }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap">
                                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmTargetHourRemoval({{ $targetHour->id }})" wire:loading.attr="disabled">
                                                    {{ __('Remove') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-2">
                                                {{ __('no target hours planned yet') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if (Gate::check('update', $employee))
                    <div class="flex items-center mt-5">
                        <x-jet-button wire:click="manageTargetHour" wire:loading.attr="disabled">
                            {{ __('Add Target Hour') }}
                        </x-jet-button>
                        <x-jet-action-message class="ml-3" on="savedTargetHours">
                            {{ __('Saved.') }}
                        </x-jet-action-message>
                         <x-jet-action-message class="ml-3" on="removedTargetHours">
                            {{ __('Removed.') }}
                        </x-jet-action-message>
                    </div>
                @endif
            </x-slot>
        </x-jet-action-section>
    </div>
    <x-jet-section-border />
    <div class="mt-10 sm:mt-0">
        <x-jet-action-section>
            <x-slot name="title">
                {{ __('Add Vacation Entitlement') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Here you can add the granted vacation days for this employee.') }}
            </x-slot>

            <x-slot name="content">
                <div class="col-span-6">
                    <div class="text-sm text-gray-600">
                        <div class="overflow-hidden border-2 border-gray-200 sm:rounded-l overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Starts at') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Ends at') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Planned')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Used')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Transferred')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->employee->vacationEntitlements as $vacationEntitlement)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->name }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->starts_at_for_humans }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->ends_at_for_humans }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $vacationEntitlement->status_color }}-100 text-{{ $vacationEntitlement->status_color }}-800">
                                                    {{ $vacationEntitlement->status }}
                                                </span>
                                            </td>
                                             <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->available_days }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->used_days }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $vacationEntitlement->transfer_days }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap">
                                                @if($vacationEntitlement->isExpired() && $vacationEntitlement->canBeTransferred())
                                                    <x-jet-button id="{{ md5($vacationEntitlement) }}" wire:click="transferVacationEntitlement({{ $vacationEntitlement->id }})" wire:loading.attr="disabled">
                                                        {{ __('Transfer') }}
                                                    </x-jet-button>
                                                @endif
                                                @if(!$vacationEntitlement->isTransferred())
                                                    <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmVactationEntitlementRemoval({{ $vacationEntitlement->id }})" wire:loading.attr="disabled">
                                                        {{ __('Remove') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-2">
                                                {{ __('no vacations entitlements found.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if (Gate::check('update', $employee))
                    <div class="flex items-center mt-5">
                        <x-jet-button wire:click="manageVacationEntitlement" wire:loading.attr="disabled">
                            {{ __('Add vacation entitlement') }}
                        </x-jet-button>
                        <x-jet-action-message class="ml-3" on="savedVacationEntitlement">
                            {{ __('Saved.') }}
                        </x-jet-action-message>
                        <x-jet-action-message class="ml-3" on="removedVacationEntitlement">
                            {{ __('Removed.') }}
                        </x-jet-action-message>
                    </div>
                @endif
            </x-slot>
        </x-jet-action-section>
    </div>
    <x-jet-section-border />
    <div class="mt-10 sm:mt-0">
        <x-jet-form-section submit="saveEmployeeProfile">
            <x-slot name="title">
                {{ __('Edit Employee Profile') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Edit general employee profile data.') }}
            </x-slot>
            <x-slot name="form">
                <div class="col-span-6 lg:col-span-4">
                    <div class="col-span-6 sm:col-span-4 mt-2">
                        <x-jet-label for="name" value="{{ __('Name') }}" />
                        <x-jet-input type="text" id="name" class="mt-1 block w-full" wire:model.defer="editUserProfileForm.name" />
                        <x-jet-input-error for="name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4 mt-1">
                        <x-jet-label for="date_of_employment" value="{{ __('Date of employment') }}" />
                        <x-date-picker id="date_of_employment" type="text" class="mt-1 block w-full" wire:model.defer="editUserProfileForm.date_of_employment" />
                        <x-jet-input-error for="date_of_employment" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4 mt-2">
                        <x-jet-label for="opening_overtime_balance" value="{{ __('Opening overtime balance') }}" />
                        <x-jet-input type="number" min="0" step="0.01" id="opening_overtime_balance" class="mt-1 block w-full" wire:model.defer="editUserProfileForm.opening_overtime_balance" />
                        <x-jet-input-error for="opening_overtime_balance" class="mt-2" />
                    </div>
                    @if(App\Daybreak::hasEmployeePayrollFeature())
                        <x-dynamic-component component="employee-payroll-form" />
                    @endif
                </div>
            </x-slot>
            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="savedProfile">
                    {{ __('Saved.') }}
                </x-jet-action-message>

                <x-jet-button>
                    {{ __('Save') }}
                </x-jet-button>
            </x-slot>
        </x-jet-form-section>
    </div>

    <!-- Target Hours Modal -->
    <x-jet-dialog-modal wire:model="currentlyManagingTargetHours">
        <x-slot name="title">
            {{ __('Manage Target Hour') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="start_date" value="{{ __('Start Date') }}" />
                <x-date-picker id="start_date" wire:model="targetHourForm.start_date" />
                <x-jet-input-error for="start_date" class="mt-2" />
            </div>
            <div class="col-span-6 lg:col-span-4 mt-2">
                <x-jet-label for="hours_per" value="{{ __('Mode') }}" />
                <x-jet-input-error for="hours_per" class="mt-2" />

                <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                    <div class="px-4 py-3"
                        wire:click="setHoursPerMode('week')">
                        <div class="{{ isset($targetHourForm['hours_per']) && $targetHourForm['hours_per'] !== 'week' ? 'opacity-50' : '' }}">
                            <!-- Mode Name -->
                            <div class="flex items-center">
                                <div class="text-sm text-gray-600 {{ $targetHourForm['hours_per'] == 'week' ? 'font-semibold' : '' }}">
                                    {{ __('Weekly hours') }}
                                </div>

                                @if ($targetHourForm['hours_per'] == 'week')
                                    <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <!-- Mode Description -->
                            <div class="mt-2 text-xs text-gray-600">
                                {{ __('Target hours based on per week ') }}
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 rounded-t-none"
                        wire:click="setHoursPerMode('month')">
                        <div class="{{ isset($targetHourForm['hours_per']) && $targetHourForm['hours_per'] !== 'month' ? 'opacity-50' : '' }}">
                            <!-- Mode Name -->
                            <div class="flex items-center">
                                <div class="text-sm text-gray-600 {{ $targetHourForm['hours_per'] == 'month' ? 'font-semibold' : '' }}">
                                    {{ __('Monthly hours') }}
                                </div>

                                @if ($targetHourForm['hours_per'] == 'month')
                                    <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <!-- Mode Description -->
                            <div class="mt-2 text-xs text-gray-600">
                                {{ __('Target hours based on per month ') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-jet-label for="targetHours" value="{{ __('Target Hours') }}" />
                <x-jet-input
                    type="number"
                    min="0"
                    step="0.1"
                    id="targetHours"
                    value="{{ $targetHourForm['target_hours'] }}"
                    wire:change="changeTargetHours($event.target.value)"
                />
                <x-jet-input-error for="target_hours" class="mt-2" />
            </div>
            <div class="col-span-6 lg:col-span-4 mt-2">
                <x-jet-label for="target_limited" value="{{ __('Target Limited') }}" />
                <x-jet-input-error for="target_limited" class="mt-2" />

                <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                    <div class="px-4 py-3"
                        wire:click="toggleTargetLimited">
                        <div class="{{ isset($targetHourForm['target_limited']) && $targetHourForm['target_limited'] == false ? 'opacity-50' : '' }}">
                            <!-- Mode Name -->
                            <div class="flex items-center">
                                <div class="text-sm text-gray-600 {{ $targetHourForm['target_limited'] == true ? 'font-semibold' : '' }}">
                                    {{ __('Target Limited') }}
                                </div>

                                @if ($targetHourForm['target_limited'] == true)
                                    <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>

                            <!-- Mode Description -->
                            <div class="mt-2 text-xs text-gray-600">
                                {{ __('Enable or disable overwork for this target hour plan.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 lg:col-span-4 mt-2">
                <x-jet-label for="days" value="{{ __('Days') }}" />
                <x-jet-input-error for="days" class="mt-2" />
                <div class="flex flex-row flex-wrap">
                    @foreach($days as $key => $day)
                        <div class="relative z-0  mt-1 mr-1 border border-gray-200 rounded-lg cursor-pointer">
                            <div class="px-4 py-3">
                                <div class="{{ isset($day['state']) && ($day['state']  == false) ? 'opacity-50' : '' }}">
                                    <div class="flex items-center">
                                        <div class="flex items-center" wire:click="toggleDayState('{{$key}}')">
                                            <div class="text-sm text-gray-600 {{ $day['state'] == true ? 'font-semibold' : '' }}">
                                                {{ __(Str::ucfirst($key)) }}
                                            </div>
                                            @if ($day['state'] == true)
                                                <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-600">
                                        <x-jet-input
                                            disabled="{{ $day['state'] == false || $targetHourForm['hours_per'] !== 'week' }}"
                                            type="number"
                                            min="0"
                                            max="23"
                                            step="0.01"
                                            class="w-21 {{ $targetHourForm['hours_per'] !== 'week' ? 'opacity-50' : '' }}" wire:model="days.{{$key}}.hours"
                                            />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="stopManageTargetHour" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="saveTargetHour" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Remove Target Hour Confirmation Dialog -->
    <x-jet-confirmation-modal wire:model="confirmingTargetHourRemoval">
        <x-slot name="title">
            {{ __('Remove Time Tracking') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove these target hours?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingTargetHourRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeTargetHour" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

    <!-- Vacation entitlement manager -->
    <x-jet-dialog-modal wire:model="currentlyManagingVacationEntitlement">
        <x-slot name="title">
            {{ __('Manage Vacation Entitlement') }}
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col space-y-2" x-data="{ open: @entangle('vacationEntitlementForm.expires') }">
                <div class="">
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input id="name" type="text" wire:model.defer="vacationEntitlementForm.name"/>
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 space-x-4 mt-2">
                    <div>
                        <x-jet-label for="starts_at" value="{{ __('Start Date') }}" />
                        <x-date-picker id="starts_at" wire:model.defer="vacationEntitlementForm.starts_at" />
                        <x-jet-input-error for="starts_at" class="mt-2" />
                    </div>
                    <div>
                        <x-jet-label for="ends_at" value="{{ __('End Date') }}" />
                        <x-date-picker id="ends_at" wire:model.defer="vacationEntitlementForm.ends_at" />
                        <x-jet-input-error for="ends_at" class="mt-2" />
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex flex-col">
                        <div class="inline-flex">
                            <input id="expires" wire:model="vacationEntitlementForm.expires" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <x-jet-label for="expires" class="ml-2 block text-sm text-gray-900" value="{{ __('Expires') }}" />
                        </div>
                        <x-jet-input-error for="expires" class="mt-2" />
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-4 mt-2">
                    <x-jet-label for="days" value="{{ __('Vacation Days') }}" />
                    <x-jet-input
                        type="number"
                        min="0"
                        step="0.5"
                        id="days"
                        wire:model.defer="vacationEntitlementForm.days"
                    />
                    <x-jet-input-error for="days" class="mt-2" />
                </div>

                <div class="grid grid-cols-1">
                    <div x-show="open ? true : false">
                        <div class="mt-2">
                            <div class="flex flex-col">
                                <div class="inline-flex">
                                    <input id="transfer_remaining" wire:model="vacationEntitlementForm.transfer_remaining" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <x-jet-label for="transfer_remaining" class="ml-2 block text-sm text-gray-900" value="{{ __('Transfer remaining entitlement') }}" />
                                </div>
                                <x-jet-input-error for="transfer_remaining" class="mt-2" />
                            </div>
                        </div>
                        <div  class="mt-2">
                            <x-jet-label for="end_of_transfer_period" value="{{ __('Expiration date of the transfer') }}" />
                            <x-date-picker
                                id="end_of_transfer_period"
                                wire:model="vacationEntitlementForm.end_of_transfer_period"
                                :disabled="!$vacationEntitlementForm['transfer_remaining']"
                            />
                            <x-jet-input-error for="end_of_transfer_period" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="stopManagingVacationEntitlement" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="saveVacationEntitlement" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Remove Vacation Entitlement Confirmation Dialog -->
    <x-jet-confirmation-modal wire:model="confirmingVacationEntitlementRemoval">
        <x-slot name="title">
            {{ __('Remove Time Tracking') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this vacation entitlement?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingVacationEntitlementRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeVacationEntitlement" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
