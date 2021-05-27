<?php

namespace App\Actions;

use DB;
use App\Models\User;
use App\Models\Location;
use App\Mail\AbsenceRemoved;
use App\Contracts\RemovesAbsence;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class RemoveAbsence implements RemovesAbsence
{
    public function remove(User $user, Location $location, $removesAbsenceId)
    {
        tap($location->absences()->whereKey($removesAbsenceId)->first(), function ($absence) use ($user, $location) {

            Gate::forUser($user)->authorize('removeAbsence', [
                Absence::class,
                $absence,
                $location
            ]);

            $absence->delete();

            Mail::to($absence->employee)->send(new AbsenceRemoved());
        });
    }
}
