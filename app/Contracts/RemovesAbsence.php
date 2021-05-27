<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface RemovesAbsence
{
    /**
     * Remove a absence.
     *
     * @param  User  $user
     * @param  Location  $location
     * @param  mixed  $removesAbsenceId
     * @return void
     */
    public function remove(User $user, Location $location, $removesAbsenceId);
}
