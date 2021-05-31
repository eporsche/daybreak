<div class="flex-col space-y-2">

    <div class="flex justify-between">
        <div class="bg-gray-50 lg:w-1/2">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">
                    {{ __(ucfirst($workingSession->status)) }}
                </span>
                <span class="block text-indigo-600">
                    @if($workingSession->status->since())
                        {{ $workingSession->status->since()->format('H:i') }}
                    @endif
                </span>
                </h2>
                <div class="mt-8 lex lg:mt-0 lg:flex-shrink-0">
                    @foreach($workingSession->status->transitionableStates() as $state)
                        <x-jet-button class="py-3 px-4" wire:click="transition('{{ $state }}')" wire:loading.attr="disabled">
                            {{
                                __($workingSession
                                    ->status
                                    ->resolveStateClass($state)::label())
                            }}
                        </x-jet-button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="self-end">
            <x-jet-button class="py-3 px-4 " wire:click="manageTimeTracking" wire:loading.attr="disabled">
                {{ __('Add time') }}
            </x-jet-button>
        </div>
    </div>
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Name') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Date') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Times') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Duration') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Pause') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Balance') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Description') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @can('filterTimeTracking',  [App\Model\TimeTracking::class, $this->user->currentLocation])
                        <tr class="bg-white">
                            <td>
                                <x-multiple-select
                                    wire:model="employeeFilter"
                                    trackBy="id"
                                    :options="$employeeMultipleSelectOptions"
                                />
                            </td>
                            <td colspan="7">
                            </td>
                        </tr>
                    @endcan
                    @forelse ($timing as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            <div class="inline-flex">
                                {{ $item->day }}
                                @if(App\Daybreak::hasProjectBillingFeature())
                                    <x-dynamic-component component="project-table-info" :item="$item"/>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->time }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->duration }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->pause_time_for_humans }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->balance }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            {{ $item->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap">
                            @if(
                                App\Daybreak::hasProjectBillingFeature() &&
                                App\Daybreak::hasEmployeePayrollFeature() &&
                                !$item->isPaid() && !$item->isBilled()
                            )
                                <x-jet-button wire:click="updateTimeTracking({{ $item->id }})" wire:loading.attr="disabled">
                                    {{ __('Edit') }}
                                </x-jet-button>
                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmTimeTrackingRemoval({{ $item->id }})" wire:loading.attr="disabled">
                                    {{ __('Remove') }}
                                </button>
                            @else
                                <x-jet-button wire:click="updateTimeTracking({{ $item->id }})" wire:loading.attr="disabled">
                                    {{ __('Edit') }}
                                </x-jet-button>
                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmTimeTrackingRemoval({{ $item->id }})" wire:loading.attr="disabled">
                                    {{ __('Remove') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-2">
                                {{ __('no data yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        {{ $timing->links() }}
    </div>

    <x-add-time-tracking-modal
        :hours="$hours"
        :minutes="$minutes"
        :user="$this->user"
        :employeeSimpleSelectOptions="$employeeSimpleSelectOptions"
        :pauseTimeForm="$pauseTimeForm"
        :timeTrackingIdBeingUpdated="$timeTrackingIdBeingUpdated"
    />

     <x-jet-confirmation-modal wire:model="confirmingTimeTrackingRemoval">
        <x-slot name="title">
            {{ __('Remove Time Tracking') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this time entry?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingTimeTrackingRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeTimeTracking" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
