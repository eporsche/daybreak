<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class EditEmployeeController extends Controller
{
    public function __invoke(Account $account, User $employee)
    {
        if (Gate::denies('view', $account)) {
            abort(403);
        }
        return view('employees.edit',['employee' => $employee]);
    }
}
