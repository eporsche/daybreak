<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Account;
use App\Contracts\AddsLocation;
use Illuminate\Support\Facades\Validator;

class AddLocation implements AddsLocation
{
    public function add(Account $account, User $employee, array $data)
    {
        Validator::make($data, [
            'owned_by' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:255']
        ])->validateWithBag('createLocation');

        $account->locations()->create($data);
    }
}
