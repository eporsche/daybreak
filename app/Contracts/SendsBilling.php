<?php

namespace App\Contracts;

use App\Models\Account;

interface SendsBilling
{
    public function send(Account $account, $projectId, $billingFrom, $billingTo);
}
