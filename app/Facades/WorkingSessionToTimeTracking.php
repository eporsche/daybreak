<?php

namespace App\Facades;

use App\Converter\WorkingSessionToTimeTracking as WorkingSessionConverter;
use Illuminate\Support\Facades\Facade;

class WorkingSessionToTimeTracking extends Facade
{
    public static function getFacadeAccessor()
    {
        return new WorkingSessionConverter();
    }
}
