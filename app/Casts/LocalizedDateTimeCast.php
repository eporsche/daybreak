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
            if ($model->timezone) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'))
                    ->setTimezone($model->timezone);
            }
        }

        throw new UnknownTimeZoneException("Did you already run the artisan command daybreak:convert-timezone?");
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
        //if a string is passed, we suppose the value is UTC
        if (is_string($value)) {
            return [ $key => $value ];
        }

        //convert date to UTC
        if ($value instanceof Carbon) {
            if ($value->isUtc()) {
                return [ $key => $value ];
            }
            return [
                $key => $value->copy()->setTimezone(config('app.timezone'))
            ];
        }

        throw new UnknownTypeException("Unknown Type Excpetion");
    }

    protected function user()
    {
        return Auth::user();
    }
}
