<?php

namespace App\Actions;

use App\Models\AbsenceType;
use App\Contracts\UpdatesAbsenceType;
use Illuminate\Support\Facades\Validator;

class UpdateAbsenceType implements UpdatesAbsenceType
{
    public function update(AbsenceType $absenceType, array $data, array $assignedUsers = null)
    {
        Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'affect_vacation_times' => ['required', 'boolean'],
            'affect_evaluations' => ['required','boolean'],
            'evaluation_calculation_setting' => ['required_if:affect_evaluations,true'],
            'regard_holidays' => ['required', 'boolean'],
            'assign_new_users' => ['required', 'boolean'],
            'remove_working_sessions_on_confirm' => ['required', 'boolean'],
        ])->validateWithBag('updateAbsenceType');

        $absenceType->update($data);

        $absenceType->users()->sync($assignedUsers);
    }
}
