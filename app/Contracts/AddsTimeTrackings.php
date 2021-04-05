<?php

namespace App\Contracts;

interface AddsTimeTrackings
{
    public function add($employee, array $array, array $pauseTimes);
}
