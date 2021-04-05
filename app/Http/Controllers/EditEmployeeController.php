<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;

class EditEmployeeController extends Controller
{
    public function __invoke(Account $account, User $employee)
    {
        return view('employees.edit',['employee' => $employee]);
    }
}
