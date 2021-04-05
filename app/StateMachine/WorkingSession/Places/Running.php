<?php

namespace App\StateMachine\WorkingSession\Places;

use DateTime;

class Running extends WorkingSessionState
{
    public static $name = 'running';

    public static function label() : string
    {
        return __('Punch in');
    }

    public function since() : ?DateTime
    {
        return $this->getModel()
            ->actions()
            ->latest()
            ->firstWhere('action_type', 'starts_at')
            ->action_time;
    }
}
