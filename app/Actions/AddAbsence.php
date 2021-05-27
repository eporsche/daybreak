<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Absence;
use App\Models\Location;
use Carbon\CarbonPeriod;
use App\Models\AbsenceType;
use App\Contracts\AddsAbsences;
use App\Formatter\DateFormatter;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\AbsenceCalendar\AbsenceCalculator;
use App\Mail\NewAbsenceWaitingForApproval;
use App\AbsenceCalendar\EmployeeAbsenceCalendar;

class AddAbsence implements AddsAbsences
{
    /**
     * @var DateFormatter
     */
    public $dateFormatter;

    public function __construct()
    {
        $this->dateFormatter = app(DateFormatter::class);
    }

    public function add(User $user, Location $location, int $managingAbsenceForId, array $data)
    {
        Gate::forUser($user)->authorize('addAbsence', [
            Absence::class,
            $managingAbsenceForId,
            $location
        ]);

        Validator::make($data, [
            'absence_type_id' => 'required',
            'starts_at' => [
                'required',
                $this->dateFormatter->dateTimeFormatRule()
            ],
            'ends_at' => [
                'required',
                $this->dateFormatter->dateTimeFormatRule(),
                'after_or_equal:starts_at'
            ],
            'full_day' => ['required', 'boolean']
        ])->validateWithBag('addAbsence');

        $startsAt = $this->dateFormatter->timeStrToCarbon($data['starts_at']);
        $endsAt = $this->dateFormatter->timeStrToCarbon($data['ends_at']);

        //ignore given time if calculation is based on full day
        if (isset($data['full_day']) && $data['full_day']) {
            $startsAt = $startsAt->copy()->startOfDay();
            $endsAt = $endsAt->copy()->endOfDay();
        }

        $addingAbsenceFor = Jetstream::findUserByIdOrFail($managingAbsenceForId);

        $calculator = new AbsenceCalculator(
            new EmployeeAbsenceCalendar(
                $addingAbsenceFor,
                $location,
                new CarbonPeriod($startsAt, $endsAt)
            ),
            AbsenceType::findOrFail($data['absence_type_id'])
        );

        $absence = $addingAbsenceFor->absences()->create(
            [
                'location_id' => $location->id,
                'vacation_days' => $calculator->sumVacationDays(),
                'paid_hours' => $calculator->sumPaidHours(),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ] + $data
        );

        Mail::to(
            $location->allUsers()->filter->hasLocationRole($location, 'admin')
        )->send(new NewAbsenceWaitingForApproval($absence, $addingAbsenceFor));
    }
}
