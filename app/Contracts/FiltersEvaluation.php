<?php

namespace App\Contracts;

use App\Models\Absence;
use App\Models\Location;
use App\Models\User;

interface FiltersEvaluation
{
    public function filter(User $employee, Location $location, array $filter);
}
