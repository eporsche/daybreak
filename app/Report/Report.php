<?php

namespace App\Report;

use Carbon\Carbon;
use App\Report\StartBalanceRowFacade as StartBalanceRow;
use Brick\Math\BigDecimal;

class Report
{
    public $startRow;

    public $reportRows;

    protected $employee;

    protected $location;

    protected $period;

    /**
     * Date when to memorize the StartBalanceRow
     * @var Carbon
     */
    public $fromDate;

    public function __construct(ReportBuilder $builder)
    {
        $this->employee = $builder->employee;
        $this->location = $builder->location;
        $this->fromDate = $builder->fromDate;
        $this->period = $builder->period;
        $this->reportRows = collect();
    }

    protected function currentDateIsStartDate($current)
    {
        return $this->fromDate->isSameDay($current);
    }

    public function generate()
    {
        $previousRow = null;
        while ($this->period->valid()) {
            $builder = new ReportRowBuilder(
                $this->period->current(),
                $this->employee,
                $this->location
            );

            if ($this->currentDateIsStartDate($this->period->current())) {
                $builder = $builder->withStartingBalance(
                    $this->employee->opening_overtime_balance ?? 0
                );
            }

            if ($previousRow) {
                $builder = $builder->withPreviousRow($previousRow);
            }

            $row = $builder->build();

            if ($this->currentDateIsStartDate($this->period->current())) {
                $this->memoizeStartRow($row);
            }

            if ($this->period->current()->gte($this->fromDate)) {
                $this->reportRows->add($row);
            }

            $previousRow = $row;

            $this->period->next();
        }
        $this->period->rewind();
        return $this;
    }

    public function getTotalBalance(): BigDecimal
    {
        return $this->reportRows->last()->balance();
    }

    /**
     * Memoizes the starting Row of the report
     */
    protected function memoizeStartRow($row)
    {
        if ($this->fromDate->isSameDay($this->employee->date_of_employment)) {
            $this->startRow = StartBalanceRow::fromStartingBalance(
                $this->employee->opening_overtime_balance
            );
        } else {
            $this->startRow = StartBalanceRow::fromRow($row);
        }
    }
}
