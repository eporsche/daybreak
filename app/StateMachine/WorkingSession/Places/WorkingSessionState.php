<?php

namespace App\StateMachine\WorkingSession\Places;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;
use App\StateMachine\WorkingSession\Transitions\PunchIn;
use App\StateMachine\WorkingSession\Transitions\PausedToRunning;
use App\StateMachine\WorkingSession\Transitions\PausedToStopped;
use App\StateMachine\WorkingSession\Transitions\RunningToPaused;
use App\StateMachine\WorkingSession\Transitions\RunningToStopped;

abstract class WorkingSessionState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Stopped::class)
            ->allowTransition(Stopped::class, Running::class, PunchIn::class)
            ->allowTransition(Running::class, Paused::class, RunningToPaused::class)
            ->allowTransition(Paused::class, Running::class, PausedToRunning::class)
            ->allowTransition(Paused::class, Stopped::class, PausedToStopped::class)
            ->allowTransition(Running::class, Stopped::class, RunningToStopped::class);
    }
}
