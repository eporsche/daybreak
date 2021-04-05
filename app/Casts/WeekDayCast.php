<?php

namespace App\Casts;

use App\Models\Day;
use Brick\Math\BigDecimal;
use App\Collections\WeekDayCollection;
use PHPUnit\Framework\MockObject\UnknownTypeException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class WeekDayCast implements CastsAttributes
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
        $collection = new WeekDayCollection();
        $collection->map(
            function ($day) use ($attributes) {
                if (!isset($attributes[$day->day])) {
                    return;
                }
                $day->hours = BigDecimal::of($attributes[$day->day]);
                $day->state = $attributes['is_'.$day->day];
                return $day;
            }
        );
        return $collection;
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
        if ($value instanceof WeekDayCollection) {
            return $value->serializeForDatabase();
        }
        throw new UnknownTypeException("Value should be WeekDayCollection");
    }
}
