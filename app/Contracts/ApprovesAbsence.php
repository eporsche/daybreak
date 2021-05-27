<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Absence;
use App\Models\Location;

interface ApprovesAbsence
{
    public function approve(User $user, Location $location, Absence $absence);
}
