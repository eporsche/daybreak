<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Gate;
use App\Contracts\RemovesTimeTracking;

class RemoveTimeTracking implements RemovesTimeTracking
{
    public function remove(User $user, Location $location, int $removeTimeTrackingForId, $timeTrackingId)
    {
        tap($location->timeTrackings()->whereKey($timeTrackingId)->first(), function ($timeTracking) use ($user, $location) {

            Gate::forUser($user)->authorize('removeAbsence', [
                TimeTracking::class,
                $timeTracking,
                $location
            ]);

            $timeTracking->delete();
        });
    }
}
