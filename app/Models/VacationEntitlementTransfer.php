<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VacationEntitlementTransfer extends Pivot
{
    /**
     * @var string
     */
    protected $table = 'vacation_entitlements_transfer';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
