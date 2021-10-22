<?php

namespace App\Http\Livewire\Locations;

use App\Daybreak;
use App\Models\User;
use App\Models\Account;
use App\Traits\HasUser;
use Livewire\Component;
use App\Models\Location;
use App\Contracts\AddsLocation;
use App\Contracts\RemovesLocation;
use App\Contracts\UpdatesLocation;
use Laravel\Jetstream\Jetstream;

class LocationManager extends Component
{
    use HasUser;

    /**
     * The account instance
     */
    public $account;

    /**
     * Holds the open/closed state for the modal window
     */
    public $manageLocation = false;

    /**
     * Fetch the form state
     */
    public $locationForm = [
        'name' => '',
        'timezone' => ''
    ];

    public $locationIdBeingUpdated = null;

    /**
     * Indicates if the application is confirming if a public holiday should be removed.
     *
     * @var bool
     */
    public $confirmingLocationRemoval = false;

    /**
     * The ID of the public holiday being removed.
     *
     * @var int|null
     */
    public $locationIdBeingRemoved = null;

    public function confirmLocationRemoval($locationId)
    {
        $this->confirmingLocationRemoval = true;

        $this->locationIdBeingRemoved = $locationId;
    }

    /**
     * Removes a location
     *
     * @param  \App\Contracts\RemovesLocation  $remover
     * @return void
     */
    public function removeLocation(RemovesLocation $remover)
    {
        $remover->remove(
            $this->user,
            $this->locationIdBeingRemoved
        );

        $this->confirmingLocationRemoval = false;

        $this->locationIdBeingRemoved = null;

        $this->account = $this->account->fresh();
    }

    public function mount(Account $account)
    {
        $this->account = $account;
    }

    public function manageLocation()
    {
        $this->manageLocation = true;
    }

    public function cancelManageLocation()
    {
        $this->locationIdBeingUpdated = null;

        $this->manageLocation = false;
    }

    public function enterLocation($locationId)
    {
        $location = Daybreak::newLocationModel()->findOrFail($locationId);

        if (! $this->user->switchLocation($location)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }

    public function updateLocation($index)
    {
        $this->locationIdBeingUpdated = $index;

        $this->updateLocationForm(
            $this->user->ownedLocations()
                ->whereKey($index)
                ->first()
        );

        $this->manageLocation = true;
    }

    public function updateLocationForm(Location $location)
    {
        $this->locationForm = [
            'name' => $location->name,
            'timezone' => $location->timezone
        ];
    }

    public function confirmUpdateLocation(UpdatesLocation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            $this->user,
            $this->locationIdBeingUpdated,
            [
                'name' => $this->locationForm['name'],
                'timezone' => $this->locationForm['timezone'],
                'owned_by' => $this->user->id
            ]
        );

        $this->reset('locationForm');

        $this->locationIdBeingUpdated = null;

        $this->account = $this->account->fresh();

        $this->emit('saved');

        $this->manageLocation = false;
    }

    public function confirmAddLocation(AddsLocation $adder)
    {
        $this->resetErrorBag();

        $adder->add(
            $this->account,
            $this->user,
            [
                'name' => $this->locationForm['name'],
                'timezone' => $this->locationForm['timezone'],
                'owned_by' => $this->user->id
            ]
        );

        $this->reset('locationForm');

        $this->account = $this->account->fresh();

        $this->emit('saved');

        $this->manageLocation = false;
    }

    public function render()
    {
        return view('locations.location-manager');
    }
}
