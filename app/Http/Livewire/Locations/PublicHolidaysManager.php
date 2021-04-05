<?php

namespace App\Http\Livewire\Locations;

use Livewire\Component;
use App\Models\Location;
use Livewire\WithPagination;
use App\Contracts\AddsPublicHoliday;
use App\Contracts\RemovesPublicHoliday;
use App\Contracts\ImportsPublicHolidays;

class PublicHolidaysManager extends Component
{
    use WithPagination;

    /**
     * Indicates if the application is confirming if a public holiday should be removed.
     *
     * @var bool
     */
    public $confirmingPublicHolidayRemoval = false;

    /**
     * The ID of the public holiday being removed.
     *
     * @var int|null
     */
    public $publicHolidayIdBeingRemoved = null;

    public $addPublicHolidayModal = false;

    public $importPublicHoliday = false;

    public $publicHolidayForm = [
        'name' => null,
        'date' => null,
        'half_day' => false
    ];

    public $importYears;

    public $importCountries;

    public $countryToBeImported;

    public $yearToBeImported;

    public $location;

    public function mount(Location $location)
    {
        $this->location = $location;

        $yearsRange = range(date('Y',strtotime('+1 year')), date('Y',strtotime('-2 year')));
        $this->importYears = array_combine($yearsRange, $yearsRange);

        $this->importCountries = collect(config('public_holidays.countries'))->mapWithKeys(
            function ($item) {
                return [
                    $item['code'] => $item['title']
                ];
            }
        )->all();
    }

    public function openPublicHolidayModal()
    {
        $this->addPublicHolidayModal = true;
    }

    public function closePublicHolidayModal()
    {
        $this->addPublicHolidayModal = false;
    }

    public function addPublicHoliday(AddsPublicHoliday $adder)
    {

        $adder->add($this->location, $this->publicHolidayForm);

        $this->location = $this->location->fresh();

        $this->emit('savedPublicHoliday');

        $this->addPublicHolidayModal = false;
    }

    public function importPublicHoliday(ImportsPublicHolidays $importer)
    {
        $this->resetErrorBag();

        $importer->import(
            $this->location,
            $this->yearToBeImported,
            $this->countryToBeImported
        );

        $this->location = $this->location->fresh();

        $this->importPublicHoliday = false;

        $this->emit('savedPublicHoliday');
    }


    public function confirmPublicHolidayRemoval($publicHolidayId)
    {
        $this->confirmingPublicHolidayRemoval = true;

        $this->publicHolidayIdBeingRemoved = $publicHolidayId;
    }

    /**
     * Remove a team member from the team.
     *
     * @param  \App\Actions\RemovesPublicHoliday  $remover
     * @return void
     */
    public function removePublicHoliday(RemovesPublicHoliday $remover)
    {
        $remover->remove(
            $this->location,
            $this->publicHolidayIdBeingRemoved
        );

        $this->confirmingPublicHolidayRemoval = false;

        $this->publicHolidayIdBeingRemoved = null;

        $this->location = $this->location->fresh();
    }

    public function render()
    {
        return view('livewire.locations.public-holidays-manager', [
            'holidays' => $this->location->publicHolidays()->paginate(10)
        ]);
    }
}
