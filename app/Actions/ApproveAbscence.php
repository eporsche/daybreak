<?php

namespace App\Actions;

use DB;
use App\Daybreak;
use App\Models\User;
use App\Models\Absence;
use App\Models\Location;
use Carbon\CarbonPeriod;
use App\Jobs\SendAbsenceApproved;
use App\Contracts\ApprovesAbsence;
use Illuminate\Support\Facades\Validator;
use App\AbsenceCalendar\AbsenceCalculator;
use Daybreak\Caldav\Jobs\CreateCaldavEvent;
use Illuminate\Validation\ValidationException;
use App\AbsenceCalendar\EmployeeAbsenceCalendar;
use App\AbsenceCalendar\EmployeeAbsenceCalendarDay;

class ApproveAbscence implements ApprovesAbsence
{
    /**
     * Approves employee absence
     *
     * @param  User  $user
     * @param  Location  $location
     * @param  int  $absenceId
     * @return void
     */
    public function approve(User $user, Location $location, $absenceId)
    {
        Validator::make([
            'absence_id' => $absenceId
        ], [
            'absence_id' => ['required', 'exists:absences,id']
        ])->validateWithBag('approvesAbsence');

        $absence = Absence::findOrFail($absenceId);

        DB::transaction(function () use ($absence, $user, $location) {
            $this->bookVacationDays($absence, $user);
            $this->createAbsenceIndex($absence, $user, $location);
            $absence->markAsConfirmed();
            if (Daybreak::hasCaldavFeature()) {
                CreateCaldavEvent::dispatch($absence, $user)
                    ->afterCommit();
            }
            SendAbsenceApproved::dispatch($user, $absence)->afterCommit();
        });
    }

    public function bookVacationDays($absence, $user)
    {
        if (!$absence->absenceType->affectsVacation()) {
            return;
        }

        //TODO: distribute absence days between available vacation entitlements
        $currentVacationEntitlement = $user->currentVacationEntitlement();
        if (!isset($currentVacationEntitlement) || !$currentVacationEntitlement->hasEnoughUnusedVacationDays($absence->vacation_days)) {
            throw ValidationException::withMessages([
                'error' => [__('Sorry, there is no fitting vacation entitlement for this absence.')],
            ])->errorBag('approvesAbsence');
        }

        $currentVacationEntitlement->useVacationDays($absence);
    }

    public function createAbsenceIndex($absence, $user, $location)
    {
        if (!$absence->absenceType->affectsEvaluation()) {
            return;
        }

        $calendar = (new EmployeeAbsenceCalendar(
            $user,
            $location,
            new CarbonPeriod(
                $absence->starts_at,
                $absence->ends_at
            )
        ));

        $absenceCalculator = new AbsenceCalculator($calendar, $absence->absenceType);

        if ($absenceCalculator->sumPaidHours() != $absence->paid_hours) {
            throw ValidationException::withMessages([
                'error' => [__("Paid hours changed from {$absence->paid_hours} to {$absenceCalculator->sumPaidHours()}")],
            ])->errorBag('approvesAbsence');
        }

        if ($absenceCalculator->sumVacationDays()->compareTo($absence->vacation_days) != 0) {
            throw ValidationException::withMessages([
                'error' => [__("Vacation days changed from {$absence->vacation_days} to {$absenceCalculator->sumVacationDays()}")],
            ])->errorBag('approvesAbsence');
        }

        foreach ($absenceCalculator->getDays() as $day) {
            /**
             * @var EmployeeAbsenceCalendarDay $day
             */
            $absence->index()->create([
                'date' => $day->getDate(),
                'hours' => $day->getPaidHours(),
                'absence_type_id' => $absence->absence_type_id,
                'user_id' => $user->id,
                'location_id' => $location->id
            ]);
        }
    }
}
