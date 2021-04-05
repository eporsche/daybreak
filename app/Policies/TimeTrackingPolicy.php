<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeTrackingPolicy
{
    use HandlesAuthorization;

    public function switchEmployee(User $user, Location $location)
    {
        return ($user->belongsToLocation($location) &&
            $user->hasLocationPermission($location,  'switchTimeTrackingEmployee')) ||
            $user->ownsLocation($location);
    }

    public function assignProjects(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'assignProjects') &&
            $user->projects()->exists();
    }
}
