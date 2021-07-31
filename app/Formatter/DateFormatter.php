<?php

namespace App\Formatter;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

interface DateFormatter
{
    /**
     * Determine if the given value is a standard date format.
     *
     * @param  string  $value
     * @return bool
     */
    public function isStandardDateFormat($value);

    public function dateStrToDate($value, $endOfDay = false);

    public function dateTimeStrToCarbon(string $timeStr, string $tz = null) : Carbon;

    public function generateTimeStr(string $date = null, string $hours = null, string $minutes = null);

    public function formatDateForView($date);

    public function formatDateTimeForView($date);

    public function dateFormatRule() : Rule;

    public function dateTimeFormatRule() : Rule;

    public function getLocalizedDateTimeString() : string;

    public function getLocalizedDateString() : string;
}
