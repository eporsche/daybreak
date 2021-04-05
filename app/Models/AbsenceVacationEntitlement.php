<?php

namespace App\Models;

use App\Casts\BigDecimalCast;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AbsenceVacationEntitlement extends Pivot
{
    protected $casts = [
        'used_days' => BigDecimalCast::class
    ];

    /**
     * A membership is a user assigned to a location
     *
     * @var string
     */
    protected $table = 'absence_vacation_entitlement';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

}
