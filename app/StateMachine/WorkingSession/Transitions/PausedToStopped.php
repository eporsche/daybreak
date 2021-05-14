<?php

namespace App\StateMachine\WorkingSession\Transitions;

use App\Models\WorkingSession;
use Spatie\ModelStates\Transition;
use App\Facades\WorkingSessionToTimeTracking;
use App\StateMachine\WorkingSession\Places\Running;
use App\StateMachine\WorkingSession\Places\Stopped;
use DB;

class PausedToStopped extends Transition
{
    private WorkingSession $workingSession;

    public function __construct(WorkingSession $workingSession)
    {
        $this->workingSession = $workingSession;
    }

    public function handle()
    {
        $this->workingSession->status = new Stopped($this->workingSession);

        $now = now();
        $this->workingSession->actions()->create([
            'action_type' => 'pause_ends_at',
            'action_time' => $now,
        ]);

        $this->workingSession->actions()->create([
            'action_type' => 'ends_at',
            'action_time' => $now,
        ]);

        $converter = WorkingSessionToTimeTracking::fromCollection($this->workingSession->actions);

        DB::transaction(function () use ($converter) {
            $trackedTime = $this->workingSession
                ->user
                ->timeTrackings()
                ->create(array_merge([
                        'location_id' => $this->workingSession->location->id,
                        'starts_at' => $this->workingSession->starts_at,
                        'ends_at' => $this->workingSession->ends_at
                ], $converter->timeTracking()));
            $trackedTime->pauseTimes()->createMany($converter->pauseTimes());

            $trackedTime->updatePauseTime();

            $this->workingSession->actions->each->delete();

            $this->workingSession->save();
        });


        return $this->workingSession->fresh();
    }
}
