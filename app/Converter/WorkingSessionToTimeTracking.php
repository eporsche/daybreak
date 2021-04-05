<?php

namespace App\Converter;

use Illuminate\Database\Eloquent\Collection;

class WorkingSessionToTimeTracking
{
    public $startsAt;

    public $endsAt;

    public $pauseTimes;

    public function fromCollection(Collection $collect)
    {
        $this->startsAt = $collect->firstWhere('action_type', 'starts_at')->action_time;

        $this->endsAt = $collect->firstWhere('action_type', 'ends_at')->action_time;

        $pauseCollection = $collect->reject(function ($value, $key) {
            return in_array(
                $value->action_type,
                ['starts_at', 'ends_at']
            );
        })->sortBy('action_time', SORT_ASC);

        $pauseArray = [];

        $pauseCollection->each(function ($pause) use (&$pauseArray) {
            if ($pause->action_type === 'pause_starts_at') {
                $pauseArray[] = [
                    'starts_at' => $pause->action_time
                ];
            } elseif ($pause->action_type === 'pause_ends_at') {
                $pauseArray[
                    array_key_last($pauseArray)
                ]['ends_at'] =  $pause->action_time;
            }
        });

        $this->pauseTimes = $pauseArray;

        return $this;
    }

    public function timeTracking()
    {
        return [
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt
        ];
    }

    public function pauseTimes()
    {
        return $this->pauseTimes;
    }
}
