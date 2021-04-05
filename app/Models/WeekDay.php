<?php

namespace App\Models;

use App\Casts\WeekDayCast;
use Illuminate\Contracts\Database\Eloquent\Castable;

class WeekDay implements Castable
{
    public static function castUsing(array $arguments)
    {
        return WeekDayCast::class;
    }
}
