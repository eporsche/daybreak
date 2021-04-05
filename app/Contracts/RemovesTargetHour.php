<?php

namespace App\Contracts;

use App\Models\User;

interface RemovesTargetHour
{
    /**
     * Remove target hours from employee
     *
     * @param mixed $employee
     * @param mixed $targetHourIdBeingRemoved
     * @return void
     */
    public function remove(User $employee, $targetHourIdBeingRemoved);
}
