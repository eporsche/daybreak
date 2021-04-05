<?php
namespace App\Actions;

use App\Models\User;
use App\Contracts\RemovesVacationEntitlements;

class RemoveVacationEntitlement implements RemovesVacationEntitlements
{
    /**
     * Remove target hours from employee
     *
     * @param mixed $employee
     * @param mixed $vacationEntitlementId
     * @return void
     */
    public function remove(User $employee, $vacationEntitlementId)
    {
        $employee->vacationEntitlements->each(function ($vacationEntitlement) {
            $vacationEntitlement->usedVacationDays()->each(function ($absence) {
                tap($absence)->markAsPending()->index()->delete();
            });
            $vacationEntitlement->usedVacationDays()->detach();
        });

        $employee->vacationEntitlements()->whereKey($vacationEntitlementId)->delete();
    }
}
