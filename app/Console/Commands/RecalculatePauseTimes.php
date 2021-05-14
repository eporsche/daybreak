<?php

namespace App\Console\Commands;

use App\Models\TimeTracking;
use Illuminate\Console\Command;
use App\Models\DefaultRestingTime;

class RecalculatePauseTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daybreak:recalculate-pause-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and update time tracking pause times based on given pause times and default resting times';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TimeTracking::with(['user.defaultRestingTimes','pauseTimes'])->get()->each->updatePauseTime();
    }
}
