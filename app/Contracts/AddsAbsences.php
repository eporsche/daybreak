<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Location;

interface AddsAbsences
{
    /**
     * Add a absence for user and location
     *
     * @param  User  $employee
     * @param  Location  $location
     * @param  array  $data
     * @return void
     */
    public function add(User $employee, Location $location, array $data) : void;
}
