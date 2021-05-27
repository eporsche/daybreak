<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface AddsAbsences
{
    /**
     * Add a absence for user and location
     *
     * @param  User  $user
     * @param  mixed  $addingAbsenceForId
     * @param  array  $data
     * @return void
     */
    public function add(User $user, Location $location, int $addingAbsenceForId, array $data);
}
