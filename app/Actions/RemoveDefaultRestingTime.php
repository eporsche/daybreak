<?php
namespace App\Actions;

use App\Models\Location;
use App\Contracts\RemovesDefaultRestingTime;

class RemoveDefaultRestingTime implements RemovesDefaultRestingTime
{
    /**
     * Remove default resting time from a location
     *
     * @param  mixed  $location
     * @param  mixed  $defaultRestingTimeIdBeingRemoved
     * @return void
     */
    public function remove(Location $location, $defaultRestingTimeIdBeingRemoved)
    {
        $location->defaultRestingTimes()->whereKey($defaultRestingTimeIdBeingRemoved)->delete();
    }
}
