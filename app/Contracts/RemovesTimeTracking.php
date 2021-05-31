<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface RemovesTimeTracking
{
    public function remove(User $user, Location $location, int $removeTimeTrackingForId, $timeTrackingId);
}
