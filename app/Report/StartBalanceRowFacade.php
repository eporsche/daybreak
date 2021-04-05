<?php

namespace App\Report;

use Illuminate\Support\Facades\Facade;

class StartBalanceRowFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StartBalanceRowBuilder::class;
    }
}
