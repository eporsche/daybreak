<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\VacationEntitlement;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExpireVacationEntitlements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        VacationEntitlement::shouldExpire()->get()->each->expire();
    }
}
