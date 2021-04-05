<?php
namespace App\Actions;

use App\Models\User;
use App\Contracts\RemovesTargetHour;

class RemoveTargetHour implements RemovesTargetHour
{
    /**
     * Remove target hours from employee
     *
     * @param mixed $employee
     * @param mixed $targetHourIdBeingRemoved
     * @return void
     */
    public function remove(User $employee, $targetHourIdBeingRemoved)
    {
        $employee->targetHours()->whereKey($targetHourIdBeingRemoved)->delete();
    }
}
