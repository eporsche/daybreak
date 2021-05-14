<?php

namespace App\Actions;

use App\Contracts\AddsDefaultRestingTime;
use Illuminate\Support\Facades\Validator;

class AddDefaultRestingTime implements AddsDefaultRestingTime
{
    public function add($user, $location, array $data)
    {
        Validator::make($data, [
            'min_hours' => ['required'],
            'duration' => ['required'],
        ])->validate();

        $defaultRestingTime = $location->defaultRestingTimes()->create($data);

        $user->defaultRestingTimes()->attach($defaultRestingTime);
    }
}
