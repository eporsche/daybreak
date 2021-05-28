<?php

namespace App\Http\Livewire\TimeTracking;

use Livewire\Component;

class PauseTimes extends Component
{
    public $pause;

    public $index;

    public $hours;

    public $minutes;

    public function mount(array $pause, int $index)
    {
        $this->pause = $pause;
        $this->index = $index;
        $this->hours = range(0,23);
        $this->minutes = range(0,59);
    }

    public function render()
    {
        return view('time_trackings.pause-times');
    }

    public function changedTime()
    {
        $this->emitUp(
            'changedTime',
            $this->pause,
            $this->index
        );
    }
}
