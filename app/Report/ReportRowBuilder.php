<?php

namespace App\Report;

use App\Models\User;
use App\Models\Location;

class ReportRowBuilder
{
    /**
     *  The current day, which should be inspected
     *  @var DateTime
     */
    public $date;

    public $employee;

    public $location;

    /**
     * @var Row
     */
    public $previousRow;

    public $startingBalance;

    public function withPreviousRow($previousRow)
    {
        $this->previousRow = $previousRow;
        return $this;
    }

    public function withStartingBalance($startingBalance)
    {
        $this->startingBalance = $startingBalance;
        return $this;
    }

    public function __construct($date, User $employee, Location $location)
    {
        $this->date = $date;
        $this->employee = $employee;
        $this->location = $location;
    }

    public function build()
    {
        return (new ReportRow($this))->generate();
    }
}
