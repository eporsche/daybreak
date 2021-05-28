<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface UpdatesTimeTracking
{
    /**
     * Validate and update the given time tracking.
     *
     * @param User     $user
     * @param mixed    $timeTrackingId
     * @param array    $input
     * @param array    $pauseTimes
     * @return void
     */
    public function update(User $user, Location $location, int $manageTimeTrackingForId, int $timeTrackingId, array $input, array $pauseTimes);
}
