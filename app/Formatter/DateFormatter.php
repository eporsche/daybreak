<?php

namespace App\Formatter;

use Carbon\CarbonImmutable;
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

    public function strToDate($value, $endOfDay = false);

    public function formatDateForView($date);

    public function formatDateTimeForView($date);

    public function generateTimeStr(string $date, string $hours = null, string $minutes = null);

    public function timeStrToCarbon(string $timeStr) : CarbonImmutable;

    public function dateFormatRule() : Rule;

    public function dateTimeFormatRule() : Rule;
}
