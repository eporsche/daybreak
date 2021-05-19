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
     * @param  array  $data
     * @return void
     */
    public function add(User $employee, array $data) : void;
}
