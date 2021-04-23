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
        return ($user->hasLocationPermission($location,  'addAbsence'));
    }

    public function approveAbsence(User $user, Location $location)
    {
        return ($user->hasLocationPermission($location,  'approveAbsence'));
    }

    public function filterAbsences(User $user, Location $location)
    {
        return ($user->hasLocationPermission($location,  'filterAbsences'));
    }
}
