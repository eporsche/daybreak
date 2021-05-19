<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Mail\AbsenceRemoved;
use App\Contracts\RemovesAbsence;
use Illuminate\Support\Facades\Mail;
use DB;

class RemoveAbsence implements RemovesAbsence
{
    public function remove($user, $removesAbsenceId)
    {
        Gate::forUser($user)->authorize('removeAbsence', $user->currentLocation);

        tap($user->currentLocation->absences()->whereKey($removesAbsenceId)->first(), function ($absence) {

            $absence->delete();

            Mail::to($absence->employee)->send(new AbsenceRemoved());
        });
    }
}
