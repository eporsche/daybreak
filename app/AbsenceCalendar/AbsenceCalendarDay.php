<?php

namespace App\AbsenceCalendar;

interface AbsenceCalendarDay
{
    public function getDate();

    public function isPublicHoliday();

    public function isWorkingDay();

    public function getPaidHours();

    public function isVacationDay();

    public function getVacation();

}
