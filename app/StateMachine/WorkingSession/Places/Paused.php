<?php

namespace App\StateMachine\WorkingSession\Places;

use DateTime;

class Paused extends WorkingSessionState
{
    public static $name = 'paused';

    public static function label() : string
    {
        return __('Pause');
    }

    public function since() : ?DateTime
    {
        return $this->getModel()
            ->actions()
            ->latest()
            ->firstWhere('action_type', 'pause_starts_at')
            ->action_time;
    }
}
