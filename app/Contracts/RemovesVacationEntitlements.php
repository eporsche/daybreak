<?php

namespace App\Contracts;

use App\Models\User;

interface RemovesVacationEntitlements
{
    public function remove(User $employee, $vacationEntitlementId);
}
