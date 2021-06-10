<div>
    <div class="flex flex-col">
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
                        {{ __('Time Zone') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('Locale') }}
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($account->locations as $location)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                {{ $location->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                {{ $location->timezone }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                {{ $location->locale }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <x-jet-button wire:click="enterLocation({{ $location->id }})" wire:loading.attr="disabled">
                                    {{ __('Enter') }}
                                </x-jet-button>
                                <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="confirmLocationRemoval({{ $location->id }})" wire:loading.attr="disabled">
                                    {{ __('Remove') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-2">
                                {{ __('no data yet') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>

    <div class="mt-5">
        <x-jet-button wire:click="addLocation" wire:loading.attr="disabled">
            {{ __('Add location') }}
        </x-jet-button>
    </div>

    <x-add-location-modal />


    <x-jet-confirmation-modal wire:model="confirmingLocationRemoval">
        <x-slot name="title">
            {{ __('Remove Time Tracking') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this time entry?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingLocationRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeLocation" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

</div>
