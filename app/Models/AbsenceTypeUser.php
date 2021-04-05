<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AbsenceTypeUser extends Pivot
{
    /**
     * A membership is a user assigned to a location
     *
     * @var string
     */
    protected $table = 'absence_type_user';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
