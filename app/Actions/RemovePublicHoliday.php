<?php
namespace App\Actions;

use App\Models\Location;
use App\Contracts\RemovesPublicHoliday;

class RemovePublicHoliday implements RemovesPublicHoliday
{
    /**
     * Remove public holiday from a location
     *
     * @param  mixed  $location
     * @param  mixed  $locationMember
     * @return void
     */
    public function remove(Location $location, $publicHolidayIdBeingRemoved)
    {
        $location->publicHolidays()->whereKey($publicHolidayIdBeingRemoved)->delete();
    }
}
