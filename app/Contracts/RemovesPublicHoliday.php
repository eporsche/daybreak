<?php

namespace App\Contracts;

use App\Models\Location;

interface RemovesPublicHoliday
{
    /**
     * Remove a public holiday.
     *
     * @param  mixed  $location
     * @param  mixed  $publicHolidayIdBeingRemoved
     * @return void
     */
    public function remove(Location $location, $publicHolidayIdBeingRemoved);
}
