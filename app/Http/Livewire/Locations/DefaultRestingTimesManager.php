<?php

namespace App\Http\Livewire\Locations;

use Livewire\Component;
use Brick\Math\BigDecimal;
use App\Contracts\AddsDefaultRestingTime;
use App\Contracts\RemovesDefaultRestingTime;

class DefaultRestingTimesManager extends Component
{

    /**
     * The location instance.
     *
     * @var mixed
     */
    public $location;

    public $addDefaultRestingTimeModal = false;

    public $defaultRestingTimeForm = [
        'min_hours' => null,
        'duration' => null
    ];

    public $defaultRestingTimeIdBeingRemoved = null;

    public $confirmingDefaultRestingTimeRemoval = false;

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

    public function confirmDefaultRestingTimeRemoval($defaultRestingTimeId)
    {
        $this->confirmingDefaultRestingTimeRemoval = true;

        $this->defaultRestingTimeIdBeingRemoved = $defaultRestingTimeId;
    }

    public function addDefaultRestingTime(AddsDefaultRestingTime $adder)
    {
        $adder->add($this->location, [
            'min_hours' => BigDecimal::of($this->defaultRestingTimeForm['min_hours'])->multipliedBy(3600),
            'duration' => BigDecimal::of($this->defaultRestingTimeForm['duration'])->multipliedBy(60)
        ]);

        $this->location = $this->location->fresh();

        $this->emit('savedDefaultRestingTime');

        $this->addDefaultRestingTimeModal = false;
    }

    /**
     * Remove a default resting time from the location.
     *
     * @param  \App\Actions\RemovesDefaultRestingTime  $remover
     * @return void
     */
    public function removePublicHoliday(RemovesDefaultRestingTime $remover)
    {
        $remover->remove(
            $this->location,
            $this->defaultRestingTimeIdBeingRemoved
        );

        $this->confirmingDefaultRestingTimeRemoval = false;

        $this->defaultRestingTimeIdBeingRemoved = null;

        $this->location = $this->location->fresh();
    }

    public function openDefaultRestingTimeModal()
    {
        $this->addDefaultRestingTimeModal = true;
    }

    public function closeDefaultRestingTimeModal()
    {
        $this->reset('defaultRestingTimeForm');

        $this->addDefaultRestingTimeModal = false;
    }

    public function render()
    {
        return view('locations.default-resting-times-manager',
            [
                'defaultRestingTimes' => $this->location->defaultRestingTimes()->paginate(10)
            ]
        );
    }
}
