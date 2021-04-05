<?php

namespace App\StateMachine\WorkingSession\Transitions;

use App\Models\WorkingSession;
use Spatie\ModelStates\Transition;
use App\StateMachine\WorkingSession\Places\Paused;

class RunningToPaused extends Transition
{
    private WorkingSession $workingSession;

    public function __construct(WorkingSession $workingSession)
    {
        $this->workingSession = $workingSession;
    }

    public function handle(): WorkingSession
    {
        $this->workingSession->status = new Paused($this->workingSession);
        $this->workingSession->actions()->create([
            'action_type' => 'pause_starts_at',
            'action_time' => now(),
        ]);
        $this->workingSession->save();

        return $this->workingSession->fresh();
    }
}
