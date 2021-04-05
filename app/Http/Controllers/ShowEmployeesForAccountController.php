<?php

namespace App\Http\Controllers;

use App\Models\Account;

class ShowEmployeesForAccountController extends Controller
{
    public function __invoke(Account $account)
    {
        return view('employees.index', compact('account'));
    }
}
