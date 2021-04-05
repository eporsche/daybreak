<?php

namespace App\AbsenceCalendar;

use App\Formatter\DateFormatter;
use Closure;
use Carbon\Carbon;
use App\Models\Day;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Support\Arrayable;

class EmployeeAbsenceCalendarDay implements AbsenceCalendarDay, Arrayable
{
    protected $date;
    protected $day;
    protected $state;
    protected $targetHours;
    protected $vacation;
    protected $isPublicHoliday;

    public function __construct(Carbon $date, Day $day, bool $isPublicHoliday, Closure $paidHours, Closure $vacation)
    {
        $this->date = $date;
        $this->day = $day->day;
        $this->state = $day->state;
        $this->targetHours = $day->hours;
        $this->isPublicHoliday = $isPublicHoliday;
        $this->paidHours = $paidHours;
        $this->vacation = $vacation;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDateForHumans()
    {
        return app(DateFormatter::class)
            ->formatDateForView($this->date);
    }

    public function isWorkingDay()
    {
        return $this->state && !$this->isPublicHoliday();
    }

    public function isPublicHoliday()
    {
        return $this->isPublicHoliday;
    }

    public function isVacationDay()
    {
        return $this->getVacation()->isPositive();
    }

    public function getTargetHours()
    {
        return $this->targetHours;
    }

    public function getPaidHours()
    {
        return call_user_func($this->paidHours, $this);
    }

    public function getVacation()
    {
        if (!$this->state || $this->isPublicHoliday()) {
            return BigDecimal::zero();
        }
        return call_user_func($this->vacation);
    }

    public function toArray()
    {
        return [
            'date' => $this->date,
            'day' => $this->day,
            'state' => $this->state,
            'paidHours' => $this->getPaidHours(),
            'targetHours' => $this->targetHours,
            'vacation' => $this->getVacation(),
            'isPublicHoliday' => $this->isPublicHoliday
        ];
    }
}
