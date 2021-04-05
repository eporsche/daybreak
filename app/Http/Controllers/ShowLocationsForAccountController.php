<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class ShowLocationsForAccountController extends Controller
{

    public function __invoke(Account $account, Request $request)
    {
        return view('locations.index', [
            'account' => $account,
            'user' => $request->user()
        ]);
    }
}
