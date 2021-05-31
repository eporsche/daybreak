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
use Illuminate\Support\Facades\Gate;
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
        Gate::check('approveAbsence',  [App\Model\Absence::class, $location]);

        Validator::make([
            'absence_id' => $absenceId
        ], [
            'absence_id' => ['required', 'exists:absences,id']
        ])->validateWithBag('approvesAbsence');

        $absence = Absence::findOrFail($absenceId);

        DB::transaction(function () use ($absence, $user) {
            $this->bookVacationDays($absence);
            $this->createAbsenceIndex($absence, $user->currentLocation);
            $absence->markAsConfirmed();
            if (Daybreak::hasCaldavFeature()) {
                CreateCaldavEvent::dispatch($absence)
                    ->afterCommit();
            }
            SendAbsenceApproved::dispatch($absence)->afterCommit();
        });
    }

    public function bookVacationDays($absence)
    {
        if (!$absence->absenceType->affectsVacation()) {
            return;
        }

        //TODO: distribute absence days between available vacation entitlements
        $currentVacationEntitlement = $absence->employee->currentVacationEntitlement();
        if (!isset($currentVacationEntitlement) || !$currentVacationEntitlement->hasEnoughUnusedVacationDays($absence->vacation_days)) {
            throw ValidationException::withMessages([
                'error' => [__('Sorry, there is no fitting vacation entitlement for this absence.')],
            ])->errorBag('approvesAbsence');
        }

        $currentVacationEntitlement->useVacationDays($absence);
    }

    public function createAbsenceIndex($absence, $location)
    {
        if (!$absence->absenceType->affectsEvaluation()) {
            return;
        }

        $calendar = (new EmployeeAbsenceCalendar(
            $absence->employee,
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
                'user_id' => $absence->employee->id,
                'location_id' => $location->id
            ]);
        }
    }
}
