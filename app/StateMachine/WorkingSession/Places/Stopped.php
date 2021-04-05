<?php

namespace App\StateMachine\WorkingSession\Places;

use DateTime;

class Stopped extends WorkingSessionState
{
    public static $name = 'stopped';

    public static function label() : string
    {
        return __('Leave');
    }

    public function since() : ?DateTime
    {
        return null;
    }
}
