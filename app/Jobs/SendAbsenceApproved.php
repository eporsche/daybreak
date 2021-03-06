<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Absence;
use Illuminate\Bus\Queueable;
use App\Mail\AbsenceStatusApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAbsenceApproved implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $absence;

    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         $users = User::all()->filter
            ->hasLocationRole($this->absence->location, 'admin')
            ->merge(collect([$this->absence->employee]));

         Mail::to($users)
            ->send(new AbsenceStatusApproved($this->absence->employee, $this->absence));
    }
}
