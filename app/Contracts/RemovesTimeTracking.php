<?php

namespace App\Contracts;

use App\Models\User;

interface RemovesTimeTracking
{
    public function remove(User $employee, $timeTrackingId);
}
