<?php

namespace App\Rules;

use DateTime;
use Illuminate\Contracts\Validation\Rule;

class DateFormatterRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value) || is_null($value)) {
            return false;
        }

        $format = 'd.m.Y';

        $date = DateTime::createFromFormat('!'.$format, $value);

        return $date && $date->format($format) == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The date format is wrong.');
    }
}
