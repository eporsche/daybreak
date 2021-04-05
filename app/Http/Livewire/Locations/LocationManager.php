<?php

namespace App\Http\Livewire\Locations;

use App\Daybreak;
use App\Models\User;
use App\Models\Account;
use Livewire\Component;
use App\Contracts\AddsLocation;
use App\Contracts\RemovesLocation;

class LocationManager extends Component
{
    /**
     * The account instance
     */
    public $account;

    /**
     * The employee instance
     */
    public $employee;

    /**
     * Holds the open/closed state for the modal window
     */
    public $addLocation = false;

    /**
     * Fetch the form state
     */
    public $addLocationForm = [
        'name' => ''
    ];

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
            $this->employee,
            $this->locationIdBeingRemoved
        );

        $this->confirmingLocationRemoval = false;

        $this->locationIdBeingRemoved = null;

        $this->account = $this->account->fresh();
    }

    public function mount(Account $account, User $employee)
    {
        $this->account = $account;
        $this->employee = $employee;
    }

    public function addLocation()
    {
        $this->addLocation = true;
    }

    public function cancelAddLocation()
    {
        $this->addLocation = false;
    }

    public function enterLocation($locationId)
    {
        $location = Daybreak::newLocationModel()->findOrFail($locationId);

        if (! $this->employee->switchLocation($location)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }

    public function confirmAddLocation(AddsLocation $adder)
    {
        $this->resetErrorBag();

        $adder->add(
            $this->account,
            $this->employee,
            [
                'name' => $this->addLocationForm['name'],
                'owned_by' => $this->employee->id
            ]
        );

        $this->addLocationForm = [
            'name' => ''
        ];

        $this->account = $this->account->fresh();

        $this->emit('saved');

        $this->addLocation = false;
    }

    public function render()
    {
        return view('locations.location-manager');
    }
}
