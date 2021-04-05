<div>
    @if (Gate::check('addLocationAbsentType', $location))
        <div class="mt-10 sm:mt-0">
            <x-jet-section-border />
            <div class="mt-10 sm:mt-0">
                <x-jet-action-section>
                    <x-slot name="title">
                        {{ __('Absence types for location') }}
                    </x-slot>

                    <x-slot name="description">
                        {{ __('Absence types available for this location.') }}
                    </x-slot>

                    <x-slot name="content">
                        <div class="space-y-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Description') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Vacation') }}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Evaluation')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Employees')}}
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->location->absentTypes as $absentType)
                                        <tr>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $absentType->title }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $absentType->affect_vacation_times }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $absentType->affect_evaluations }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                {{ $absentType->users->count() }}
                                            </td>
                                            <td class="px-6 py-2 whitespace-no-wrap">
                                                 <x-jet-button wire:click="updateAbsenceType({{ $absentType->id }})" wire:loading.attr="disabled">
                                                    {{ __('Update') }}
                                                </x-jet-button>
                                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmAbsenceTypeRemoval({{ $absentType->id  }})"  wire:loading.attr="disabled">
                                                    {{ __('Remove') }}
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colpsan="4" class="text-center p-2">
                                                {{ __('No absent types for this location.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="flex items-center mt-5">
                            <x-jet-button wire:click="manageAbsenceType" wire:loading.attr="disabled">
                                {{ __('Add Absence Type') }}
                            </x-jet-button>
                            <x-jet-action-message class="ml-3" on="savedTargetHours">
                                {{ __('Saved.') }}
                            </x-jet-action-message>
                        </div>
                    </x-slot>
                </x-jet-action-section>
            </div>
        </div>
    @endif

    <x-jet-dialog-modal wire:model="addAbsenceType">
        <x-slot name="title">
            {{ __('Add absent type') }}
        </x-slot>

        <x-slot name="content">
            <div class="flex-col space-y-5">
                <!-- Absence Type Name -->
                <div>
                    <x-jet-label for="name" value="{{ __('Absence Type Name') }}" />
                    <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="addAbsenceTypeForm.title" autofocus />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
                <div>
                    <x-jet-label for="employeeSelect" value="{{ __('Assigned employees') }}" />
                    @foreach($this->employees as $key => $user)
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="users.{{ $key }}" type="checkbox"
                                    value="{{ $key }}"
                                    @if(in_array($key, $selectedEmployees))
                                        checked
                                    @endif
                                    wire:model="selectedEmployees"
                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                >
                            </div>
                            <div class="ml-3 text-sm leading-5">
                                <label for="users.{{ $key }}" class="font-medium text-gray-700">{{ $user }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Affects Vacation Contingent -->
                <div>
                    <x-jet-label for="affect_vacation_times" value="{{ __('Vacation contingent') }}" />
                    <x-jet-input-error for="affect_vacation_times" class="mt-2" />
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3"
                            wire:click="toggleVacationContingent">
                            <div class="{{ isset($addAbsenceTypeForm['affect_vacation_times']) && $addAbsenceTypeForm['affect_vacation_times'] == false ? 'opacity-50' : '' }}">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $addAbsenceTypeForm['affect_vacation_times'] == true ? 'font-semibold' : '' }}">
                                        {{ __('Vacation contingent') }}
                                    </div>
                                    @if ($addAbsenceTypeForm['affect_vacation_times'] == true)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ __('Hours booked with this absent type should be substracted from vacation allowence.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Affects Evaluation -->
                <div>
                    <x-jet-label for="affects_evaluation" value="{{ __('Affects Evaluation') }}" />
                    <x-jet-input-error for="affects_evaluation" class="mt-2" />
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3"
                            wire:click="toggleAffectsEvaluation">
                            <div class="{{ isset($addAbsenceTypeForm['affect_evaluations']) && $addAbsenceTypeForm['affect_evaluations'] == false ? 'opacity-50' : '' }}">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $addAbsenceTypeForm['affect_evaluations'] == true ? 'font-semibold' : '' }}">
                                        {{ __('Affects Evaluation') }}
                                    </div>
                                    @if ($addAbsenceTypeForm['affect_evaluations'] == true)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ __('Choose if the report calculation method is affected by this absent type.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Evaluation Type Mode  -->
                @if ($addAbsenceTypeForm['affect_evaluations'] == true)
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3">
                            <ul>
                                @foreach ($evaluationOptions as $index => $option)
                                <li class="{{$loop->last ? '' : 'mb-4'}}">
                                    <label class="inline-flex items-center text-sm text-gray-600 font-semibold">
                                        <input  id="{{$index}}" type="radio" wire:model="addAbsenceTypeForm.evaluation_calculation_setting" class="form-radio" value="{{$index}}">
                                        <span class="ml-2">{{ $option['label'] }}</span>
                                    </label>
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ $option['description'] }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <x-jet-input-error for="evaluation_calculation_setting" class="mt-2" />
                @endif
                <!-- Regard Holidays  -->
                <div>
                    <x-jet-label for="regard_holidays" value="{{ __('Regard Holidays') }}" />
                    <x-jet-input-error for="regard_holidays" class="mt-2" />
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3"
                            wire:click="toggleRegardHolidays">
                            <div class="{{ isset($addAbsenceTypeForm['regard_holidays']) && $addAbsenceTypeForm['regard_holidays'] == false ? 'opacity-50' : '' }}">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $addAbsenceTypeForm['regard_holidays'] == true ? 'font-semibold' : '' }}">
                                        {{ __('Regard Holidays') }}
                                    </div>
                                    @if ($addAbsenceTypeForm['regard_holidays'] == true)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ __('Holidays are taken into account (On public holidays - no vacation days are calculated. Within evaluations the holiday hours are considered, not the absence hours)') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Assign New Users  -->
                <div>
                    <x-jet-label for="assign_new_users" value="{{ __('Assign new users') }}" />
                    <x-jet-input-error for="assign_new_users" class="mt-2" />
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3"
                            wire:click="toggleAssignNewUsers">
                            <div class="{{ isset($addAbsenceTypeForm['assign_new_users']) && $addAbsenceTypeForm['assign_new_users'] == false ? 'opacity-50' : '' }}">
                                <!-- Mode Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $addAbsenceTypeForm['assign_new_users'] == true ? 'font-semibold' : '' }}">
                                        {{ __('Assign new users') }}
                                    </div>
                                    @if ($addAbsenceTypeForm['assign_new_users'] == true)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ __('Assign absence type to new users automatically.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Remove Working Sessions On Confirm -->
                <div>
                    <x-jet-label for="remove_working_sessions_on_confirm" value="{{ __('Remove working sessions on confirm') }}" />
                    <x-jet-input-error for="remove_working_sessions_on_confirm" class="mt-2" />
                    <div class="relative z-0  mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <div class="px-4 py-3"
                            wire:click="toggleRemoveWorkingSessionsOnConfirm">
                            <div class="{{ isset($addAbsenceTypeForm['remove_working_sessions_on_confirm']) && $addAbsenceTypeForm['remove_working_sessions_on_confirm'] == false ? 'opacity-50' : '' }}">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $addAbsenceTypeForm['remove_working_sessions_on_confirm'] == true ? 'font-semibold' : '' }}">
                                        {{ __('Remove working sessions on confirm') }}
                                    </div>
                                    @if ($addAbsenceTypeForm['remove_working_sessions_on_confirm'] == true)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ __('When confirming an absence, remove all time records in the period') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="stopManagingAbsenceType" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            @if($currentAbsenceType)
                <x-jet-button class="ml-2" wire:click="confirmUpdateAbsenceType" wire:loading.attr="disabled">
                    {{ __('Update') }}
                </x-jet-button>
            @else
                <x-jet-button class="ml-2" wire:click="createAbsenceType" wire:loading.attr="disabled">
                    {{ __('Create') }}
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
    <x-jet-confirmation-modal wire:model="confirmingAbsenceTypeRemoval">
        <x-slot name="title">
            {{ __('Remove Absence Type') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove the absence type from this location?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingAbsenceTypeRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeAbsenceType" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
