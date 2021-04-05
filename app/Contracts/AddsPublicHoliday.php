<?php

namespace App\Contracts;

use App\Models\Location;

interface AddsPublicHoliday
{
    /**
     * Adds a public holiday
     *
     * @param  Location  $location
     * @param  array  $data
     * @return void
     */
    public function add(Location $location, array $data) : void;
}
