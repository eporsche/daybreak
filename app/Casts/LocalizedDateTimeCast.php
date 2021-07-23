<?php

namespace App\Casts;

use Carbon\Carbon;
use App\Contracts\HasTimeZone;
use App\Exceptions\UnknownTimeZoneException;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\UnknownTypeException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LocalizedDateTimeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (!$value) {
            return;
        }

        if ($model instanceof HasTimeZone) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'))
                ->setTimezone($model->timezone);
        }

        throw new UnknownTimeZoneException();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value instanceof Carbon) {
            if ($value->isUtc()) {
                return [$key => $value];
            }

            // dd($model, $key, $value, $attributes);
            //if the carbon instance has been localized we need to convert it
            return $value->copy()->setTimezone(config('app.timezone'));
        }

        throw new UnknownTypeException("Value should be of type Carbon");
    }

    protected function user()
    {
        return Auth::user();
    }
}
