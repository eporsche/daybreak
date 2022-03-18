<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShowLocationsForAccountController extends Controller
{

    public function __invoke(Account $account, Request $request)
    {
        if (Gate::denies('view', $account)) {
            abort(403);
        }

        return view('locations.index', [
            'account' => $account,
            'user' => $request->user()
        ]);
    }
}
