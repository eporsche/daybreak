<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsencePolicy
{
    use HandlesAuthorization;

    public function addAbsence(User $user, Location $location)
    {
        return ($user->belongsToLocation($location) &&
            $user->hasLocationPermission($location,  'addAbsence')) ||
            $user->ownsLocation($location);
    }

    public function approveAbsence(User $user, Location $location)
    {
        return ($user->belongsToLocation($location) &&
            $user->hasLocationPermission($location,  'approveAbsence')) ||
            $user->ownsLocation($location);
    }

    public function switchEmployee(User $user, Location $location)
    {
        return ($user->belongsToLocation($location) &&
            $user->hasLocationPermission($location,  'switchAbsenceEmployee')) ||
            $user->ownsLocation($location);
    }
}
