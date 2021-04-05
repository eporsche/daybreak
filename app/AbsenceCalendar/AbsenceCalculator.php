<?php

namespace App\AbsenceCalendar;

use App\Models\AbsenceType;
use Brick\Math\BigDecimal;

class AbsenceCalculator
{
    public $calendar;

    public $absenceType;

    public function __construct(AbsenceCalendar $calendar, AbsenceType $absenceType)
    {
        $this->calendar = $calendar;
        $this->absenceType = $absenceType;
    }

    public function sumVacationDays()
    {
        return $this->absenceType->affectsVacation() ?
            $this->calendar->getVacationDays()->sumBigDecimals(
                fn($day) => $day->getVacation()
            ) : BigDecimal::zero();
    }

    /**
     * Paid hours are only calculated if it actually affects the evaluation and
     * it should be equaled out with target hours.
     *
     * @return BigDecimal
     */
    public function sumPaidHours()
    {
        return $this->absenceType->affectsEvaluation() &&
            $this->absenceType->shouldSumAbsenceHoursUpToTargetHours() ?
            $this->calendar->getWorkingDays()->sumBigDecimals(
                fn($day) => $day->getPaidHours()
            ) : BigDecimal::zero();
    }

    public function getDays()
    {
        return $this->calendar->days;
    }
}
