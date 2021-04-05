<?php

namespace App\Contracts;

use App\Models\User;

interface TransfersVacationEntitlements
{
    /**
     * Used to transfer a vacation entitlement
     *
     * @param  mixed $employee
     * @param  mixed $vacationEntitlementId
     * @return void
     */
    public function transfer(User $employee, $vacationEntitlementId);
}
