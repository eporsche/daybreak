<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface AddsTimeTrackings
{
    public function add(User $user, Location $location, int $managingTimeTrackingForId, array $array, array $pauseTimes);
}
