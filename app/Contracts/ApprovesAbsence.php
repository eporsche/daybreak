<?php

namespace App\Contracts;

use App\Models\Absence;
use App\Models\User;

interface ApprovesAbsence
{
    public function approve(User $user, Absence $absence);
}
