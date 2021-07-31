<?php

namespace App\Actions;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use App\Formatter\DateFormatter;
use Laravel\Jetstream\Jetstream;
use App\Facades\PeriodCalculator;
use Illuminate\Support\Facades\Gate;
use App\Contracts\UpdatesTimeTracking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateTimeTracking implements UpdatesTimeTracking
{
    public $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function update(User $user, Location $location, int $managingTimeTrackingForId, int $timeTrackingId, array $data, array $pauseTimes)
    {
        Gate::forUser($user)->authorize('updateTimeTracking', [
            TimeTracking::class,
            $managingTimeTrackingForId,
            $location
        ]);

        Validator::make(array_merge($data, [
            'time_tracking_id' => $timeTrackingId
        ]),[
            'starts_at' => ['required', $this->dateFormatter->dateTimeFormatRule() ],
            'ends_at' => ['required', $this->dateFormatter->dateTimeFormatRule() , 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
            'time_tracking_id' => ['required', 'exists:time_trackings,id'],
        ])->validateWithBag('addTimeTracking');


        $startsAt = $this->dateFormatter->dateTimeStrToCarbon($data['starts_at']);
        $endsAt = $this->dateFormatter->dateTimeStrToCarbon($data['ends_at']);

        $updatingTimeTrackingFor = Jetstream::findUserByIdOrFail($managingTimeTrackingForId);

        $this->ensureDateIsNotBeforeEmploymentDate($updatingTimeTrackingFor, $startsAt);
        $this->ensureDateIsNotTooFarInTheFuture($endsAt);
        $this->ensureGivenTimeIsNotOverlappingWithExisting($updatingTimeTrackingFor, $startsAt, $endsAt, $timeTrackingId);

        $this->validatePauseTimes(
            PeriodCalculator::fromTimesArray($pauseTimes),
            $startsAt,
            $endsAt
        );

        $trackedTime = $location->timeTrackings()->whereKey($timeTrackingId)->first();

        DB::transaction(function () use ($trackedTime, $startsAt, $endsAt, $data, $pauseTimes, $updatingTimeTrackingFor) {
            $trackedTime->update(array_merge([
                'user_id' => $updatingTimeTrackingFor->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ], Arr::except($data, ['starts_at','ends_at','time_tracking_id'])));

            $trackedTime->pauseTimes->each->delete();

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

    protected function ensureDateIsNotBeforeEmploymentDate($user, $startsAt)
    {
        if ($user->date_of_employment) {
            if ($startsAt->isBefore($user->date_of_employment)) {
                throw ValidationException::withMessages([
                    'date' => [ __('Date should not before employment date.') ],
                ])->errorBag('addTimeTracking');
            }
        }
    }

    protected function ensureGivenTimeIsNotOverlappingWithExisting($user, $startsAt, $endsAt, $timeTrackingId) {
        if ($user->timeTrackings()->where(function ($query) use ($startsAt, $endsAt, $timeTrackingId) {
            $query->whereBetween('starts_at', [$startsAt, $endsAt])
                ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                ->orWhere(function ($query) use ($startsAt, $endsAt) {
                    return $query->where('ends_at','>',$endsAt)
                        ->where('starts_at','<', $startsAt);
                });
        })->where('id', '!=', [$timeTrackingId])->count() > 0
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
        $haystack = Arr::except($periods, [$index]);
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
