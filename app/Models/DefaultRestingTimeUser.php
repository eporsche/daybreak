<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DefaultRestingTimeUser extends Pivot
{
    /**
     * A membership is a user assigned to a location
     *
     * @var string
     */
    protected $table = 'default_resting_time_users';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
