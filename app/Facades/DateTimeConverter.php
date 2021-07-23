<?php

namespace App\Facades;

use App\Converter\DateTimeConverter as Converter;
use Illuminate\Support\Facades\Facade;

class DateTimeConverter extends Facade
{
    public static function getFacadeAccessor()
    {
        return Converter::class;
    }
}
