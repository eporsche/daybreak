<?php

namespace App\Contracts;

use App\Models\Location;

interface ImportsPublicHolidays
{
    public function import(Location $location, int $year, string $countryCode);
}
