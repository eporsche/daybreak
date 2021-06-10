<?php

namespace App\Casts;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Contracts\HasTimeZone;
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
            return new Carbon();
        }

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'));

        if ($model instanceof HasTimeZone) {
            return $date->copy()->setTimezone($model->timezone);
        }

        return $date->copy()->setTimezone($this->user()->currentTimezone());
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
            return $value->copy()->setTimezone(config('app.timezone'));
        }

        throw new UnknownTypeException("Value should be of type Carbon");
    }

    protected function user()
    {
        return Auth::user();
    }
}
