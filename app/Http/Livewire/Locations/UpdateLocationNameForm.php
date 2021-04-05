<?php

namespace App\Http\Livewire\Locations;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Contracts\UpdatesLocationNames;

class UpdateLocationNameForm extends Component
{
    /**
     * The team instance.
     *
     * @var mixed
     */
    public $location;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * Mount the component.
     *
     * @param  mixed  $location
     * @return void
     */
    public function mount($location)
    {
        $this->location = $location;

        $this->state = ['name' => $location->name];
    }

    /**
     * Update the location's name.
     *
     * @param  \App\Contracts\UpdatesLocationNames  $updater
     * @return void
     */
    public function updateLocationName(UpdatesLocationNames $updater)
    {
        $this->resetErrorBag();

        $updater->update($this->user, $this->location, $this->state);

        $this->emit('saved');

        $this->emit('refresh-navigation-dropdown');
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
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.locations.update-location-name-form');
    }
}
