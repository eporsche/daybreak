<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Absence;
use App\Models\Location;
use Carbon\CarbonPeriod;
use App\Models\AbsenceType;
use Illuminate\Support\Arr;
use App\Contracts\AddsAbsences;
use App\Formatter\DateFormatter;
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

    public function add(User $employee, array $data): void
    {
        Gate::forUser($employee)->authorize('addAbsence', [
            Absence::class,
            $employee->currentLocation
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

        $calculator = new AbsenceCalculator(
            new EmployeeAbsenceCalendar(
                $employee,
                $employee->currentLocation,
                new CarbonPeriod($startsAt, $endsAt)
            ),
            AbsenceType::findOrFail($data['absence_type_id'])
        );

        $absence = $employee->absences()->create(
            [
                'location_id' => $employee->currentLocation->id,
                'vacation_days' => $calculator->sumVacationDays(),
                'paid_hours' => $calculator->sumPaidHours(),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ] + $data
        );

         $admins = User::all()->filter
            ->hasLocationRole($employee->currentLocation, 'admin');

         Mail::to($admins)
            ->send(new NewAbsenceWaitingForApproval($absence, $employee));
    }
}
