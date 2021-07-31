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
use App\Facades\DateTimeConverter;
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

        $addingAbsenceFor = Jetstream::findUserByIdOrFail($managingAbsenceForId);

        $startsAtConverter = DateTimeConverter::fromLocalDateTime(
            $this->dateFormatter->dateTimeStrToCarbon(
                $data['starts_at'],
                $addingAbsenceFor->currentTimezone()
            )
        );

        $endsAtConverter = DateTimeConverter::fromLocalDateTime(
            $this->dateFormatter->dateTimeStrToCarbon(
                $data['ends_at'],
                $addingAbsenceFor->currentTimezone()
            )
        );

        $calculator = new AbsenceCalculator(
            new EmployeeAbsenceCalendar(
                $addingAbsenceFor,
                $location,
                new CarbonPeriod(
                    $startsAtConverter->toLocalDateTime(),
                    $endsAtConverter->toLocalDateTime()
                )
            ),
            AbsenceType::findOrFail($data['absence_type_id'])
        );

        $absence = $addingAbsenceFor->absences()->create(
            [
                'location_id' => $location->id,
                'vacation_days' => $calculator->sumVacationDays(),
                'paid_hours' => $calculator->sumPaidHours(),
                'starts_at' => $startsAtConverter->toUTC(),
                'ends_at' => $endsAtConverter->toUTC(),
                'timezone' => $addingAbsenceFor->currentTimezone()
            ] + $data
        );

        Mail::to(
            $location->allUsers()->filter->hasLocationRole($location, 'admin')
        )->send(new NewAbsenceWaitingForApproval($absence, $addingAbsenceFor));
    }
}
