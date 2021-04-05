<?php

namespace App\Report;

use Brick\Math\BigDecimal;
use Carbon\Carbon;

class StartBalanceRow
{
    public $balance;

    public function __construct(StartBalanceRowBuilder $builder)
    {
        $this->balance = $builder->balance;
    }

    public function balance() : BigDecimal
    {
        return $this->balance;
    }

    public function date() : Carbon
    {
        return $this->date;
    }

    public function label() : string
    {
        return __("Starting Balance");
    }
}
