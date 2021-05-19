<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;


    public function approveAbsence(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'approveAbsence');
    }

    public function removeAbsence(User $user, Location $location)
    {

        return $user->hasLocationPermission($location,  'removeAbsence');
    }

    /**
     * Determine whether the user can add team members.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Team  $team
     * @return mixed
     */
    public function addLocationMember(User $user, Location $location)
    {
        return $user->ownsLocation($location);
    }

    public function updateLocationMember(User $user, Location $location)
    {
        return $user->ownsLocation($location);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return mixed
     */
    public function view(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'editLocations');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return mixed
     */
    public function update(User $user, Location $location)
    {
        return $user->ownsLocation($location);
    }

    public function removeLocationMember(User $user, Location $location)
    {
        return $user->ownsLocation($location);
    }

    public function addLocationAbsentType(User $user, Location $location)
    {
        return $user->ownsLocation($location);
    }

    public function addDefaultRestingTime(User $user, Location $location)
    {
        return $user->hasLocationPermission($location,  'addDefaultRestingTime');
    }
}
