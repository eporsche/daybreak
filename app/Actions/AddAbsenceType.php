<?php

namespace App\Actions;

use App\Models\Location;
use App\Contracts\AddsAbsenceType;
use Illuminate\Support\Facades\Validator;

class AddAbsenceType implements AddsAbsenceType
{
    public function add(Location $location, array $data, array $assignedUsers = null)
    {
        Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'affect_vacation_times' => ['required', 'boolean'],
            'affect_evaluations' => ['required','boolean'],
            'evaluation_calculation_setting' => ['required_if:affect_evaluations,true'],
            'regard_holidays' => ['required', 'boolean'],
            'assign_new_users' => ['required', 'boolean'],
            'remove_working_sessions_on_confirm' => ['required', 'boolean'],
        ])->validateWithBag('createAbsenceType');

        $absentType = $location->absentTypes()->create($data);

        $absentType->users()->sync($assignedUsers);
    }
}
