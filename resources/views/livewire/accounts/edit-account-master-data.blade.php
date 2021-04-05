<div>
    <x-jet-section-border />

    <!-- Update Account Settings -->
    <div class="mt-10 sm:mt-0">
        <x-jet-form-section submit="updateAccountSettings">
            <x-slot name="title">
                {{ __('Master data') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Change account settings') }}
            </x-slot>

            <x-slot name="form">
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <x-jet-label for="name" value="{{ __('Account Name') }}" />
                    <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="updateAccountForm.name" autofocus />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-jet-action-message class="mr-3" on="updated">
                    {{ __('Updated.') }}
                </x-jet-action-message>

                <x-jet-button>
                    {{ __('Update') }}
                </x-jet-button>
            </x-slot>
        </x-jet-form-section>
    </div>
</div>
