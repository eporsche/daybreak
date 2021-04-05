<?php

namespace App\StateMachine\WorkingSession\Transitions;

use App\Models\WorkingSession;
use Spatie\ModelStates\Transition;
use App\StateMachine\WorkingSession\Places\Running;

class PausedToRunning extends Transition
{
    private WorkingSession $workingSession;

    public function __construct(WorkingSession $workingSession)
    {
        $this->workingSession = $workingSession;
    }

    public function handle(): WorkingSession
    {
        $this->workingSession->status = new Running($this->workingSession);
        $this->workingSession->actions()->create([
            'action_type' => 'pause_ends_at',
            'action_time' => now(),
        ]);
        $this->workingSession->save();

        return $this->workingSession->fresh();
    }
}
