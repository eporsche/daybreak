<?php

namespace App\Collections;

use App\Models\Day;
use Brick\Math\BigDecimal;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class WeekDayCollection extends Collection
{
    public function __construct()
    {
        $this->items = $this->getArrayableItems($this->defaultDaysArray());
    }

    public function getDayForDate($date): Day
    {
        return $this->first(function ($value, $key) use ($date) {
            if (Str::ucfirst($value->day) === $date->format('D')) {
                return true;
            }
        }, (new Day('undefined', false, BigDecimal::zero())));
    }

    public function serializeForDatabase(): array
    {
        return $this->flattenArray(
            $this->normalizedDays(
                $this->items,
                array_keys($this->items)
            )
        );
    }

    protected function normalizedDays($items, $keys) {
        return array_map(
            function ($value) {
                return [
                    'is_'.$value->day => $value->state,
                    $value->day => $value->hours
                ];
            },
        $items, $keys);
    }

    protected function flattenArray($items)
    {
        $result = [];
        foreach ($items as $key => $value) {
            $assoc = $value;
            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }
        return $result;
    }

    protected function defaultDaysArray()
    {
        return array_map(
            function ($value) {
                return new Day(
                    $value['day'],
                    $value['state'],
                    $value['hours']
                );
            },
            $this->defaultValues()
        );
    }

    protected function defaultValues()
    {
        return [
            [
                'day' => 'mon',
                'state' => true,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'tue',
                'state' => true,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'wed',
                'state' => true,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'thu',
                'state' => true,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'fri',
                'state' => true,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'sat',
                'state' => false,
                'hours' => BigDecimal::zero()
            ],
            [
                'day' => 'sun',
                'state' => false,
                'hours' => BigDecimal::zero()
            ]
        ];
    }
}
