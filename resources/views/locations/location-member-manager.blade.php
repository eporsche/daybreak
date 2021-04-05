<div>
    @if (Gate::check('addLocationMember', $location))
        <x-jet-section-border />

        <!-- Add Location Member -->
        <div class="mt-10 sm:mt-0">
            <x-jet-form-section submit="addLocationMember">
                <x-slot name="title">
                    {{ __('Add Location Member') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Add a new location member to your location, allowing them to collaborate with you.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            {{ __('Please provide the email address of the person you would like to add to this location. The email address must be associated with an existing account.') }}
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="email" value="{{ __('Email') }}" />
                        <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="addLocationMemberForm.email" />
                        <x-jet-input-error for="email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    @if (count($this->roles) > 0)
                        <div class="col-span-6 lg:col-span-4">
                            <x-jet-label for="role" value="{{ __('Role') }}" />
                            <x-jet-input-error for="role" class="mt-2" />

                            <div class="mt-1 border border-gray-200 rounded-lg cursor-pointer">
                                @foreach ($this->roles as $index => $role)
                                        <div class="px-4 py-3 {{ $index > 0 ? 'border-t border-gray-200' : '' }}"
                                                        wire:click="$set('addLocationMemberForm.role', '{{ $role->key }}')">
                                            <div class="{{ isset($addLocationMemberForm['role']) && $addLocationMemberForm['role'] !== $role->key ? 'opacity-50' : '' }}">
                                                <!-- Role Name -->
                                                <div class="flex items-center">
                                                    <div class="text-sm text-gray-600 {{ $addLocationMemberForm['role'] == $role->key ? 'font-semibold' : '' }}">
                                                        {{ $role->name }}
                                                    </div>

                                                    @if ($addLocationMemberForm['role'] == $role->key)
                                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    @endif
                                                </div>

                                                <!-- Role Description -->
                                                <div class="mt-2 text-xs text-gray-600">
                                                    {{ $role->description }}
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-slot>

                <x-slot name="actions">
                    <x-jet-action-message class="mr-3" on="saved">
                        {{ __('Added.') }}
                    </x-jet-action-message>

                    <x-jet-button>
                        {{ __('Add') }}
                    </x-jet-button>
                </x-slot>
            </x-jet-form-section>
        </div>
    @endif

    @if ($location->locationInvitations->isNotEmpty() && Gate::check('addLocationMember', $location))
        <x-jet-section-border />
        <!-- Location Member Invitations -->
        <div class="mt-10 sm:mt-0">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Pending Location Invitations') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('These people have been invited to your location and have been sent an invitation email. They may join the location by accepting the email invitation.') }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-6">
                        @foreach ($location->locationInvitations as $invitation)
                            <div class="flex items-center justify-between">
                                <div class="text-gray-600">{{ $invitation->email }}</div>
                                <div class="flex items-center">
                                    @if (Gate::check('removeLocationMember', $location))
                                        <!-- Cancel Location Invitation -->
                                        <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                                            wire:click="cancelLocationInvitation({{ $invitation->id }})">
                                            {{ __('Cancel') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>
    @endif

    @if ($location->users->isNotEmpty())
        <x-jet-section-border />

        <!-- Manage Location Members -->
        <div class="mt-10 sm:mt-0">
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Location Members') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('All of the people that are part of this location.') }}
                </x-slot>

                <!-- Location Member List -->
                <x-slot name="content">
                    <div class="space-y-6">
                        @foreach ($location->users->sortBy('name') as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="w-8 h-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                    <div class="ml-4">{{ $user->name }}</div>
                                </div>

                                <div class="flex items-center">
                                    <!-- Manage Location Member Role -->
                                    @if (Gate::check('addLocationMember', $location) && Laravel\Jetstream\Jetstream::hasRoles())
                                        <button class="ml-2 text-sm text-gray-400 underline" wire:click="manageRole('{{ $user->id }}')">
                                            {{ Laravel\Jetstream\Jetstream::findRole($user->membership->role)->name }}
                                        </button>
                                    @elseif (Laravel\Jetstream\Jetstream::hasRoles())
                                        <div class="ml-2 text-sm text-gray-400">
                                            {{ Laravel\Jetstream\Jetstream::findRole($user->membership->role)->name }}
                                        </div>
                                    @endif

                                    <!-- Leave Location -->
                                    @if ($this->user->id === $user->id)
                                        <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="$toggle('confirmingLeavingLocation')">
                                            {{ __('Leave') }}
                                        </button>

                                    <!-- Remove Location Member -->
                                    @elseif (Gate::check('removeLocationMember', $location))
                                        <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none" wire:click="confirmLocationMemberRemoval('{{ $user->id }}')">
                                            {{ __('Remove') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-jet-action-section>
        </div>
    @endif

    <!-- Role Management Modal -->
    <x-jet-dialog-modal wire:model="currentlyManagingRole">
        <x-slot name="title">
            {{ __('Manage Role') }}
        </x-slot>

        <x-slot name="content">
                <div class="mt-1 border border-gray-200 rounded-lg cursor-pointer">
                    @foreach ($this->roles as $index => $role)
                        <div class="px-4 py-3 {{ $index > 0 ? 'border-t border-gray-200' : '' }}"
                                        wire:click="$set('currentRole', '{{ $role->key }}')">
                            <div class="{{ $currentRole !== $role->key ? 'opacity-50' : '' }}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 {{ $currentRole == $role->key ? 'font-semibold' : '' }}">
                                        {{ $role->name }}
                                    </div>

                                    @if ($currentRole == $role->key)
                                        <svg class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ $role->description }}
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="stopManagingRole" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="updateRole" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Leave Location Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingLeavingLocation">
        <x-slot name="title">
            {{ __('Leave Location') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to leave this location?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingLeavingLocation')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="leaveLocation" wire:loading.attr="disabled">
                {{ __('Leave') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

    <!-- Remove Location Member Confirmation Modal -->
    <x-jet-confirmation-modal wire:model="confirmingLocationMemberRemoval">
        <x-slot name="title">
            {{ __('Remove Location Member') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to remove this person from the location?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingLocationMemberRemoval')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="removeLocationMember" wire:loading.attr="disabled">
                {{ __('Remove') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
