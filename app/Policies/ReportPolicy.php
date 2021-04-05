<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function switchEmployee(User $user, Location $location)
    {
        return ($user->belongsToLocation($location) &&
            $user->hasLocationPermission($location,  'switchReportEmployee')) ||
            $user->ownsLocation($location);
    }
}
