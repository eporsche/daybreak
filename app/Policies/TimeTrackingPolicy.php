<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use App\Models\TimeTracking;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeTrackingPolicy
{
    use HandlesAuthorization;

    public function updateTimeTracking(User $user, $managingTimeTrackingForId, Location $location)
    {
        return $user->hasLocationPermission($location, 'updateTimeTracking') ||
            $user->id === (int)$managingTimeTrackingForId;
    }


    public function addTimeTracking(User $user, $managingTimeTrackingForId, Location $location)
    {
        return $user->isLocationAdmin($location) ||
            $user->id === (int)$managingTimeTrackingForId;
    }

    public function assignProjects(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'assignProjects') &&
            $user->projects()->exists();
    }

    public function filterTimeTracking(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'filterTimeTracking');
    }

    public function manageTimeTracking(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'manageTimeTracking');
    }

    public function removeTimeTracking(User $user, TimeTracking $timeTracking, Location $location)
    {
        return $user->hasLocationPermission($location, 'manageTimeTracking') ||
            $user->id === $timeTracking->user_id;
    }
}
