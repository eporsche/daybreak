<?php

namespace App\Http\Livewire\Locations;

use Livewire\Component;
use Laravel\Jetstream\Jetstream;
use App\Models\LocationInvitation;
use Illuminate\Support\Facades\Auth;
use App\Contracts\InvitesLocationMembers;
use App\Contracts\RemovesLocationMembers;
use App\Contracts\UpdatesLocationMembersRole;

class LocationMemberManager extends Component
{
    /**
     * The location instance.
     *
     * @var mixed
     */
    public $location;

    /**
     * Indicates if a user's role is currently being managed.
     *
     * @var bool
     */
    public $currentlyManagingRole = false;

    /**
     * The user that is having their role managed.
     *
     * @var mixed
     */
    public $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     *
     * @var string
     */
    public $currentRole;

    /**
     * Indicates if the application is confirming if a user wishes to leave the current location.
     *
     * @var bool
     */
    public $confirmingLeavingLocation = false;

    /**
     * Indicates if the application is confirming if a location member should be removed.
     *
     * @var bool
     */
    public $confirmingLocationMemberRemoval = false;

    /**
     * The ID of the location member being removed.
     *
     * @var int|null
     */
    public $locationMemberIdBeingRemoved = null;

    /**
     * The "add location member" form state.
     *
     * @var array
     */
    public $addLocationMemberForm = [
        'email' => '',
        'role' => null,
    ];

    /**
     * Mount the component.
     *
     * @param  mixed  $location
     * @return void
     */
    public function mount($location)
    {
        $this->location = $location;
    }

    /**
     * Invite a new location member to a location.
     *
     * @param  \App\Contracts\InvitesLocationMembers
     * @return void
     */
    public function addLocationMember(InvitesLocationMembers $inviter)
    {
        $this->resetErrorBag();

        $inviter->invite(
            $this->user,
            $this->location,
            $this->addLocationMemberForm['email'],
            $this->addLocationMemberForm['role']
        );

        $this->addLocationMemberForm = [
            'email' => '',
            'role' => null,
        ];

        $this->location = $this->location->fresh();

        $this->emit('saved');
    }

    /**
     * Cancel a pending team member invitation.
     *
     * @param  int  $invitationId
     * @return void
     */
    public function cancelLocationInvitation($invitationId)
    {
        if (! empty($invitationId)) {
            LocationInvitation::whereKey($invitationId)->delete();
        }

        $this->location = $this->location->fresh();
    }

    /**
     * Allow the given user's role to be managed.
     *
     * @param  int  $userId
     * @return void
     */
    public function manageRole($userId)
    {
        $this->currentlyManagingRole = true;
        $this->managingRoleFor = Jetstream::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->locationRole($this->location)->key;
    }

    /**
     * Save the role for the user being managed.
     *
     * @param  \App\Contracts\UpdatesLocationMembersRole  $updater
     * @return void
     */
    public function updateRole(UpdatesLocationMembersRole $updater)
    {
        $updater->update(
            $this->user,
            $this->location,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->location = $this->location->fresh();

        $this->stopManagingRole();
    }

    /**
     * Stop managing the role of a given user.
     */
    public function stopManagingRole()
    {
        $this->currentlyManagingRole = false;
    }

    /**
     * Remove the currently authenticated user from the location.
     *
     * @param  \App\Contracts\RemoveLocationMember  $remover
     * @return void
     */
    public function leaveLocation(RemovesLocationMembers $remover)
    {
        $remover->remove(
            $this->user,
            $this->location,
            $this->user
        );

        $this->confirmingLeavingLocation = false;

        $this->location = $this->location->fresh();

        return redirect(config('fortify.home'));
    }

    /**
     * Confirm that the given location member should be removed.
     *
     * @param  int  $userId
     * @return void
     */
    public function confirmLocationMemberRemoval($userId)
    {
        $this->confirmingLocationMemberRemoval = true;

        $this->locationMemberIdBeingRemoved = $userId;
    }

    /**
     * Remove a location member from the location.
     *
     * @param \App\Contracts\RemoveLocationMember  $remover
     * @return void
     */
    public function removeLocationMember(RemovesLocationMembers $remover)
    {
        $remover->remove(
            $this->user,
            $this->location,
            $user = Jetstream::findUserByIdOrFail($this->locationMemberIdBeingRemoved)
        );

        $this->confirmingLocationMemberRemoval = false;

        $this->locationMemberIdBeingRemoved = null;

        $this->location = $this->location->fresh();
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Get the available location member roles.
     *
     * @return array
     */
    public function getRolesProperty()
    {
        return array_values(Jetstream::$roles);
    }

    public function render()
    {
        return view('locations.location-member-manager');
    }
}
