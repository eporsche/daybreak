<?php

namespace App\Contracts;

use App\Models\User;

interface AddsVacationEntitlements
{
    public function add(User $employee, array $array);
}
