<?php

namespace App\AbsenceCalendar;

interface AbsenceCalendar
{
    public function getWorkingDays();

    public function getVacationDays();
}
