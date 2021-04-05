<?php

namespace App\Models;

use ArrayAccess;
use Brick\Math\BigDecimal;

class Day implements ArrayAccess
{
    public $day;

    public $state;

    public $hours;


    public function __construct(string $day, bool $state, BigDecimal $hours)
    {
        $this->day = $day;
        $this->state = $state;
        $this->hours = $hours;
    }

    public function toArray()
    {
        return array (
            'day' => $this->day,
            'state' => $this->state,
            'hours' => $this->hours,
        );
    }


    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset)
    {
        return $this->toArray()[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->toArray()[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->toArray()[$offset]);
    }
}
