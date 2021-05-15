<?php

namespace App\Actions;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use App\Formatter\DateFormatter;
use App\Facades\PeriodCalculator;
use App\Contracts\AddsTimeTrackings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use DB;

class AddTimeTracking implements AddsTimeTrackings
{
    public $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function add($employee, array $data, array $pauseTimes)
    {
        Validator::make($data,[
            'starts_at' => ['required', $this->dateFormatter->dateTimeFormatRule() ],
            'ends_at' => ['required', $this->dateFormatter->dateTimeFormatRule() , 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string']
        ])->validateWithBag('addTimeTracking');

        $startsAt = $this->dateFormatter->timeStrToCarbon($data['starts_at']);
        $endsAt = $this->dateFormatter->timeStrToCarbon($data['ends_at']);

        $this->ensureDateIsNotBeforeEmploymentDate($employee, $startsAt);
        $this->ensureDateIsNotTooFarInTheFuture($endsAt);
        $this->ensureGivenTimeIsNotOverlappingWithExisting($employee, $startsAt, $endsAt);

        $this->validatePauseTimes(
            PeriodCalculator::fromTimesArray($pauseTimes),
            $startsAt,
            $endsAt
        );

        DB::transaction(function () use ($employee, $startsAt, $endsAt, $data, $pauseTimes) {
            $trackedTime = $employee->timeTrackings()->create(array_merge([
                'location_id' => $employee->currentLocation->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,

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

    protected function calculatePauseTimeFromDefaultRestingTimes($employee, $workingTimeInSeconds)
    {
        return optional(
            $employee->defaultRestingTimes()->firstWhere('min_hours','<=',$workingTimeInSeconds)
        )->duration->inSeconds();
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

    protected function ensureGivenTimeIsNotOverlappingWithExisting($employee, $startsAt, $endsAt) {

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
