<?php

namespace App\Facades;

use App\Calculators\PeriodCalculator as Calculator;
use Illuminate\Support\Facades\Facade;

class PeriodCalculator extends Facade
{
    public static function getFacadeAccessor()
    {
        return new Calculator();
    }
}
