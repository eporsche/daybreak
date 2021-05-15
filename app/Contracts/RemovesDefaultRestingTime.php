<?php

namespace App\Contracts;

use App\Models\Location;

interface RemovesDefaultRestingTime
{
    /**
     * Remove a default resting time.
     *
     * @param  mixed  $location
     * @param  mixed  $defaultRestingTimeIdBeingRemoved
     * @return void
     */
    public function remove(Location $location, $defaultRestingTimeIdBeingRemoved);
}
