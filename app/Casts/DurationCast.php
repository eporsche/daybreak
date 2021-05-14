<?php

namespace App\Casts;

use App\Models\Duration;
use Brick\Math\BigDecimal;
use PHPUnit\Framework\MockObject\UnknownTypeException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DurationCast implements CastsAttributes
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
            return new Duration(0);
        }
        return new Duration($value);
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
        if ($value instanceof BigDecimal || is_string($value)) {
            return $value;
        }

        if ($value instanceof Duration) {
            return $value->inSeconds();
        }

        throw new UnknownTypeException("Value should be of type BigDecimal or Duration");
    }
}
