<?php

namespace App\Formatter;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Rules\DateFormatterRule;
use App\Rules\DateTimeFormatterRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class GermanDateFormatter implements DateFormatter
{
    /**
     * Determine if the given value is a standard "DE" date format.
     *
     * @param  string  $value
     * @return bool
     */
    public function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{1,2}).(\d{1,2}).(\d{4})$/', $value);
    }

    public function strToDate($value, $endOfDay = false)
    {
        if ($this->isStandardDateFormat($value)) {
            $date = Date::instance(Carbon::createFromFormat('d.m.Y', $value));
            if ($endOfDay) {
                return $date->endOfDay();
            } else {
                return $date->startOfDay();
            }
        } else {
            throw new InvalidParameterException($value.' is not a standard date format "d.m.Y"');
        }
    }

    public function formatDateForView($date)
    {
        return $date->format('d.m.Y');
    }

    public function formatDateTimeForView($date)
    {
        return $date->format('d.m.Y H:i');
    }

    public function generateTimeStr(string $date = null, string $hours = null, string $minutes = null)
    {
        return $date.' '.str_pad($hours,2,"0",STR_PAD_LEFT).':'.str_pad($minutes,2,"0",STR_PAD_LEFT);
    }

    public function timeStrToCarbon(string $timeStr) : Carbon
    {
        return Carbon::createFromTimestamp(strtotime($timeStr))
            ->shiftTimezone(Auth::user() ? Auth::user()->currentTimezone() : config('app.timezone'));
    }

    public function dateTimeFormatRule() : Rule
    {
        return new DateTimeFormatterRule();
    }

    public function dateFormatRule() : Rule
    {
        return new DateFormatterRule();
    }
}
