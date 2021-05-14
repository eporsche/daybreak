<?php

namespace App\Contracts;

interface AddsDefaultRestingTime
{
    public function add($user, $location, array $data);
}
