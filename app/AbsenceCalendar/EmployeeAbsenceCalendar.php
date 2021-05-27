<?php

namespace App\AbsenceCalendar;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;
use Carbon\CarbonPeriod;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class EmployeeAbsenceCalendar implements AbsenceCalendar
{
    protected $employee;
    protected $location;
    protected $period;
    protected $startDay;
    protected $endDay;

    public $days;

    public function __construct(User $employee, Location $location, CarbonPeriod $period)
    {
        $this->employee = $employee;
        $this->location = $location;
        $this->startDay = $period->getStartDate()->toImmutable();
        $this->endDay = $period->getEndDate()->toImmutable();
        $this->period = new CarbonPeriod(
            $period->getStartDate()->startOfDay(),
            $period->getEndDate()->endOfDay()
        );
        $this->calculateDays();
    }

    protected function calculateDays()
    {
        $this->days = collect();
        foreach ($this->period as $date) {
            $this->days->add(new EmployeeAbsenceCalendarDay(
                $date,
                $this->employee->targetHourDayForDate($date),
                $this->location->publicHolidayForDate($date) ? true : false,
                $this->calculatePaidHours(),
                $this->calculateVacation($date)
            ));
        }

        $this->period->rewind();
    }

    /**
     * Filters dates which are a public holidays or not a normal working day
     * for the employee e.g. weekends.
     *
     * @return Collection $days
     */
    public function getWorkingDays()
    {
        return $this->days->filter(fn($day) => $day->isWorkingDay());
    }

    public function getVacationDays()
    {
        return $this->days->filter(fn($day) => $day->isVacationDay());
    }

    protected function calculateVacation($date)
    {
        return function () use ($date) {
            if ($this->isHalfDayVacation($date)) {
                return BigDecimal::of('0.5');
            }

            return BigDecimal::one();
        };
    }

    protected function calculatePaidHours()
    {
        /**
         * @var Day $absenceDay
         */
        return function ($absenceDay) {
            //if we end up having start and end date on the the same day
            if ($this->startAndEndDayAreSame()) {
                $calcHours = BigDecimal::of($this->startDay->diffInMinutes($this->endDay))
                    ->dividedBy('60', 2, RoundingMode::HALF_EVEN);
                return $calcHours->isGreaterThanOrEqualTo($absenceDay->getTargetHours()) ?
                    $absenceDay->getTargetHours() : $calcHours;
            }

            //else we need to calculate the paid hours for that day
            $diffInHours = $this->diffHoursToStartOrEndOfDay($absenceDay->getDate());
            return $absenceDay->getTargetHours()
                ->isGreaterThanOrEqualTo($diffInHours) ?
                $diffInHours : $absenceDay->getTargetHours();
        };
    }

    protected function diffHoursToStartOrEndOfDay(Carbon $date)
    {
        $diffInHours = BigDecimal::of('24');

        $date = $date->toImmutable();

        if ($this->startDay->isSameDay($date)) {
            //hours from start to end of day
            $diffInHours = BigDecimal::of($this->startDay->diffInMinutes($date->addDay()->startOfDay()))
                ->dividedBy('60', 2, RoundingMode::HALF_EVEN);
        } elseif ($date->isSameDay($this->endDay)) {
            //hours from start of day to end of end day
            $diffInHours = BigDecimal::of($date->startOfDay()->diffInMinutes($this->endDay))
                ->dividedBy('60', 2, RoundingMode::HALF_EVEN);
        }
        return $diffInHours;
    }

    protected function startAndEndDayAreSame()
    {
        return $this->startDay->isSameDay($this->endDay);
    }

    protected function isHalfDayVacation(Carbon $date)
    {
        $diffInHours = BigDecimal::zero();

        if ($this->startAndEndDayAreSame()) {
            $diffInHours = BigDecimal::of($this->startDay->diffInMinutes($this->endDay))
                ->dividedBy('60', 2, RoundingMode::HALF_EVEN);
        } else {
            $diffInHours = $this->diffHoursToStartOrEndOfDay($date);
        }

        return $diffInHours->isLessThan(
            $this->employee
                ->targetHoursForDate($date)
                ->dividedBy('2', 2, RoundingMode::HALF_EVEN)
        );
    }
}
