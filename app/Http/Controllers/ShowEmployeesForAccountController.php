<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class ShowEmployeesForAccountController extends Controller
{
    public function __invoke(Account $account)
    {
        if (Gate::denies('view', $account)) {
            abort(403);
        }
        return view('employees.index', compact('account'));
    }
}
