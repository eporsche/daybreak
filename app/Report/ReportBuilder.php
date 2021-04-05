<?php

namespace App\Report;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;
use Carbon\CarbonPeriod;

class ReportBuilder
{
    public $employee;
    public $location;
    public $fromDate;
    public $toDate;

    public function __construct(User $employee, Location $location, Carbon $fromDate, Carbon $toDate)
    {
        $this->employee = $employee;
        $this->location = $location;
        $this->fromDate = $fromDate->copy()->startOfDay()->toImmutable();
        $this->toDate = $toDate;
        $this->period = new CarbonPeriod(
            $this->beginAt(),
            $toDate
        );
    }

    /**
     * The report period always starts with the date of the employment of the employee
     *
     * @return void
     */
    protected function beginAt()
    {
        return $this->employee->date_of_employment ?? $this->fromDate;
    }

    public function build()
    {
        return (new Report($this))
            ->generate();
    }
}
