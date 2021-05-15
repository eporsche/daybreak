<?php

namespace App\Actions;

use App\Contracts\AddsDefaultRestingTime;
use Illuminate\Support\Facades\Validator;
use DB;

class AddDefaultRestingTime implements AddsDefaultRestingTime
{
    public function add($location, array $data)
    {
        Validator::make($data, [
            'min_hours' => ['required'],
            'duration' => ['required'],
        ])->validate();

        DB::transaction(function () use ($location, $data) {
            $defaultRestingTime = $location->defaultRestingTimes()->create($data);

            $location->allUsers()->each(function ($user) use ($defaultRestingTime) {

                $user->defaultRestingTimes()->attach($defaultRestingTime);
            });

        });

    }
}
