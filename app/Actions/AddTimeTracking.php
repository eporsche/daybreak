<?php

namespace App\Actions;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use App\Models\TimeTracking;
use App\Formatter\DateFormatter;
use Laravel\Jetstream\Jetstream;
use App\Facades\PeriodCalculator;
use App\Facades\DateTimeConverter;
use App\Contracts\AddsTimeTrackings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddTimeTracking implements AddsTimeTrackings
{
    public $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function add(User $user, Location $location, int $managingTimeTrackingForId, array $data, array $pauseTimes)
    {
        Gate::forUser($user)->authorize('addTimeTracking', [
            TimeTracking::class,
            $managingTimeTrackingForId,
            $location
        ]);

        Validator::make($data,[
            'starts_at' => ['required', $this->dateFormatter->dateTimeFormatRule() ],
            'ends_at' => ['required', $this->dateFormatter->dateTimeFormatRule() , 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string']
        ])->validateWithBag('addTimeTracking');

        $addingTimeTrackingFor = Jetstream::findUserByIdOrFail($managingTimeTrackingForId);

        $startsAt = DateTimeConverter::fromLocalDateTime(
            $this->dateFormatter
                ->dateTimeStrToCarbon($data['starts_at'])
                ->shiftTimezone($addingTimeTrackingFor->currentTimezone())
        )->toUTC();

        $endsAt = DateTimeConverter::fromLocalDateTime(
            $this->dateFormatter
                ->dateTimeStrToCarbon($data['ends_at'])
                ->shiftTimezone($addingTimeTrackingFor->currentTimezone())
        )->toUTC();

        $this->ensureDateIsNotBeforeEmploymentDate($addingTimeTrackingFor, $startsAt);
        $this->ensureDateIsNotTooFarInTheFuture($endsAt);
        $this->ensureGivenTimeIsNotOverlappingWithExisting($addingTimeTrackingFor, $startsAt, $endsAt);

        $this->validatePauseTimes(
            PeriodCalculator::fromTimesArray($pauseTimes),
            $startsAt,
            $endsAt
        );

        DB::transaction(function () use ($addingTimeTrackingFor, $startsAt, $endsAt, $data, $pauseTimes, $location) {
            $trackedTime = $addingTimeTrackingFor->timeTrackings()->create(array_merge([
                'location_id' => $location->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'timezone' => $addingTimeTrackingFor->currentTimezone()
            ], Arr::except($data, ['starts_at','ends_at'])));

            $trackedTime->pauseTimes()->createMany($pauseTimes);
            $trackedTime->updatePauseTime();
        });

    }

    protected function validatePauseTimes($pauseTimePeriodCalculator, $startsAt, $endsAt)
    {
        if (!$pauseTimePeriodCalculator->hasPeriods()) {
            return;
        }

        $pauseTimePeriodCalculator->periods->each(function ($period, $index) use ($pauseTimePeriodCalculator, $startsAt, $endsAt) {
            $this->ensurePeriodIsNotTooSmall($period);
            $this->ensurePeriodsAreNotOverlapping($pauseTimePeriodCalculator->periods, $index, $period);
            $this->ensurePeriodWithinWorkingHours($period, $startsAt, $endsAt);
        });
    }

    protected function ensureDateIsNotTooFarInTheFuture($endsAt)
    {
        if ($endsAt->isAfter(Carbon::now()->endOfDay())) {
            throw ValidationException::withMessages([
                'date' => [ __('Date should not be in the future.') ],
            ])->errorBag('addTimeTracking');
        }
    }

    protected function ensureDateIsNotBeforeEmploymentDate($employee, $startsAt)
    {
        if ($employee->date_of_employment) {
            if ($startsAt->isBefore($employee->date_of_employment)) {
                throw ValidationException::withMessages([
                    'date' => [ __('Date should not before employment date.') ],
                ])->errorBag('addTimeTracking');
            }
        }
    }

    protected function ensureGivenTimeIsNotOverlappingWithExisting($employee, $startsAt, $endsAt)
    {
        if ($employee->timeTrackings()->where(function ($query) use ($startsAt, $endsAt) {
            $query->whereBetween('starts_at', [$startsAt, $endsAt])
                ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                ->orWhere(function ($query) use ($startsAt, $endsAt) {
                    return $query->where('ends_at','>',$endsAt)
                        ->where('starts_at','<', $startsAt);
                });
        })->count() > 0
        ) {
            throw ValidationException::withMessages([
                'date' => [ __('The given time period overlapps with an existing entry.') ],
            ])->errorBag('addTimeTracking');
        }
    }

    protected function ensurePeriodIsNotTooSmall($period)
    {
        if ($period->count() <= 1) {
            throw ValidationException::withMessages([
                'pause' => [ __('Given pause time is too small.') ],
            ])->errorBag('addTimeTracking');
        }
    }

    protected function ensurePeriodWithinWorkingHours($period, $startsAt, $endsAt)
    {
        if (!$period->startsAfterOrAt($startsAt) || !$period->endsBeforeOrAt($endsAt)) {
            throw ValidationException::withMessages([
                'pause' => [ __('Pause is not between working hours.') ],
            ])->errorBag('addTimeTracking');
        }
    }

    protected function ensurePeriodsAreNotOverlapping($periods, $index, $period)
    {
        $haystack = Arr::except($periods->toArray(), [$index]);
        foreach ($haystack as $needle) {
            /**
             * @var CarbonPeriod $period
             */
            if ($period->overlaps($needle)) {
                throw ValidationException::withMessages([
                    'pause' => [ __('Overlapping pause time detected.') ],
                ])->errorBag('addTimeTracking');
            }
        }
    }
}
