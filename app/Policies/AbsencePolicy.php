<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Absence;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsencePolicy
{
    use HandlesAuthorization;

    public function manageAbsence(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'manageAbsence');
    }

    public function addAbsence(User $user, $managingAbsenceForId, Location $location)
    {
        return $user->isLocationAdmin($location) ||
            $user->id === (int)$managingAbsenceForId;
    }

    public function removeAbsence(User $user, Absence $absence, Location $location)
    {
        return $user->hasLocationPermission($location,  'manageAbsence') ||
            $user->id === $absence->user_id;
    }

    public function approveAbsence(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'approveAbsence');
    }

    public function filterAbsences(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'filterAbsences');
    }
}
