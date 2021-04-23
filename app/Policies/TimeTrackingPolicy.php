<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeTrackingPolicy
{
    use HandlesAuthorization;

    public function assignProjects(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'assignProjects') &&
            $user->projects()->exists();
    }

    public function filterTimeTracking(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'filterTimeTracking');
    }
}
