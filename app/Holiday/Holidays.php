<?php

namespace App\Holiday;

class Holidays
{
    protected $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function toCollection()
    {
        return collect($this->response)->map(function ($item, $key) {
            return new Holiday($key, $item);
        });
    }
}
