<?php

namespace App\Actions;

use App\Models\User;
use App\Mail\AbsenceRemoved;
use App\Contracts\RemovesAbsence;
use Illuminate\Support\Facades\Mail;

class RemoveAbsence implements RemovesAbsence
{
    public function remove(User $employee, $absenceId)
    {
        $employee->absences()->whereKey($absenceId)->delete();

        Mail::to($employee)->send(new AbsenceRemoved());
    }
}
