<?php

namespace App\Report;

use Brick\Math\BigDecimal;

class StartBalanceRowBuilder
{
    public $date;

    public $balance;

    public function fromRow(ReportRow $row)
    {
        $this->date = $row->date;
        $this->balance = $row->balance();

        return new StartBalanceRow($this);
    }

    public function fromStartingBalance(BigDecimal $startingBalance)
    {
        $this->balance = $startingBalance;

        return new StartBalanceRow($this);
    }

}
