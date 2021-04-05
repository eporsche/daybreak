<?php

namespace App\Contracts;

use App\Models\User;

interface RemovesLocation
{
    public function remove(User $employee, $removeLocationId);
}
