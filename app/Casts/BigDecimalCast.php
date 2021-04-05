<?php

namespace App\Casts;

use Brick\Math\BigDecimal;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use PHPUnit\Framework\MockObject\UnknownTypeException;

class BigDecimalCast implements CastsAttributes
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
            return BigDecimal::zero();
        }
        return BigDecimal::of($value);
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

        if (is_float($value) || is_integer($value)) {
            return (string) $value;
        }

        throw new UnknownTypeException("Value has wrong type");
    }
}
