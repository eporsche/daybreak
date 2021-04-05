<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\Account;

interface AddsLocation
{
    public function add(Account $account, User $employee, array $data);
}
