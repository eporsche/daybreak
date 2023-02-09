<?php

namespace App\Actions;

use App\Models\User;
use App\Models\VacationEntitlement;
use App\Contracts\TransfersVacationEntitlements;
use App\Formatter\DateFormatter;

class TransferVacationEntitlement implements TransfersVacationEntitlements
{
    protected $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function transfer(User $employee, $vacationEntitlementId)
    {
        $vacationEntitlementToBeTransferred = VacationEntitlement::findOrFail($vacationEntitlementId);

        //fetch a possible vacation entitlement candiate
        $useExistingVacationEntitlement = $employee->vacationEntitlements()->where('ends_at', $vacationEntitlementToBeTransferred->end_of_transfer_period)->first();
        if (!empty($useExistingVacationEntitlement)) {
            $vacationEntitlementToBeTransferred->transferVacationDays($useExistingVacationEntitlement, [
                'days' => $useExistingVacationEntitlement->days->plus($vacationEntitlementToBeTransferred->daysTransferrable())
            ]);
        } else {
            $newEntitlement = $employee->vacationEntitlements()->create([
                'name' => __('Transferred: :name', ['name' => $vacationEntitlementToBeTransferred->name]),
                'expires' => true,
                'transfer_remaining' => false,
                'starts_at' => $vacationEntitlementToBeTransferred->ends_at,
                'ends_at' => $vacationEntitlementToBeTransferred->end_of_transfer_period,
                'status' => $vacationEntitlementToBeTransferred->end_of_transfer_period->isPast() ? 'expired' : 'expires'
            ]);

            $vacationEntitlementToBeTransferred->transferVacationDays()->attach($newEntitlement, [
                'days' => $vacationEntitlementToBeTransferred->daysTransferrable()
            ]);
        }
    }
}
