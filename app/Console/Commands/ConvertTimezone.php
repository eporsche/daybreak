<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\PauseTime;
use DateTimeZone;
use Carbon\Carbon;
use App\Models\TimeTracking;
use App\Models\WorkingSessionAction;
use Illuminate\Console\Command;

class ConvertTimezone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daybreak:convert-timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate time information preceding daybreak v0.9';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info(__("This command will migrate database records created before version v0.9 was released."));

        $this->info(__("You should not run this operation if you don't have any old records."));

        $toTimezone = config('app.timezone');
        if ($toTimezone !== "UTC") {
            $this->error(__("Please set your application default timezone to UTC before running this operation. Aborting."));
            die();
        }

        $possibleTimezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $fromTimezone = $this->anticipate(__("Now tell me in which timezone your time information has been stored in"), $possibleTimezones);

        if (!in_array($fromTimezone, $possibleTimezones)) {
            $this->error(__("Given timezone is not valid."));
            die();
        }

        if (!$this->confirm(__("Your data will now be migrated. Do you want to continue?"))) {
            die();
        }

        $this->line(__("Migrating time trackings records"));
        $this->withProgressBar(TimeTracking::all(), function ($timeTracking) use ($fromTimezone, $toTimezone) {
            $this->migrateTimeTracking($fromTimezone, $toTimezone, $timeTracking);
        });

        $this->newLine(2);
        $this->line(__("Migrating pause times"));
        $this->withProgressBar(PauseTime::all(), function ($pauseTime) use ($fromTimezone, $toTimezone) {
            $this->migratePauseTime($fromTimezone, $toTimezone, $pauseTime);
        });

        $this->newLine(2);
        $this->line(__("Migrating absences"));
        $this->withProgressBar(Absence::all(), function ($pauseTime) use ($fromTimezone, $toTimezone) {
            $this->migrateAbsence($fromTimezone, $toTimezone, $pauseTime);
        });

        $this->newLine(2);
        $this->line(__("Migrate running working sessions"));
        $this->withProgressBar(WorkingSessionAction::all(), function ($workingSession) use ($fromTimezone, $toTimezone) {
            $this->migrateWorkingSessionAction($fromTimezone, $toTimezone, $workingSession);
        });

        $this->newLine(2);
        $this->info(__("Operation successfull."));

        return 0;
    }

    private function migrateWorkingSessionAction($fromTimezone, $toTimezone, $workingSessionAction)
    {
        if ($this->skipRecordsWithExistingTimezoneInformation($workingSessionAction)) {
            return;
        }

        $actionTime = Carbon::createFromFormat('Y-m-d H:i:s',$workingSessionAction->getRawOriginal('action_time'), $fromTimezone);

        $workingSessionAction->update([
            'action_time' => $actionTime->setTimezone($toTimezone),
            'timezone' => $fromTimezone
        ]);
    }

    private function migrateTimeTracking($fromTimezone, $toTimezone, $timeTracking)
    {
        if ($this->skipRecordsWithExistingTimezoneInformation($timeTracking)) {
            return;
        }

        $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$timeTracking->getRawOriginal('starts_at'), $fromTimezone);
        $endsAt = Carbon::createFromFormat('Y-m-d H:i:s',$timeTracking->getRawOriginal('ends_at'), $fromTimezone);

        $timeTracking->update([
            'starts_at' => $startsAt->setTimezone($toTimezone),
            'ends_at' => $endsAt->setTimezone($toTimezone),
            'timezone' => $fromTimezone
        ]);
    }

    private function migratePauseTime($fromTimezone, $toTimezone, $pauseTime): void
    {
        if ($this->skipRecordsWithExistingTimezoneInformation($pauseTime)) {
            return;
        }

        $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$pauseTime->getRawOriginal('starts_at'), $fromTimezone);
        $endsAt = Carbon::createFromFormat('Y-m-d H:i:s',$pauseTime->getRawOriginal('ends_at'), $fromTimezone);

        $pauseTime->update([
            'starts_at' => $startsAt->setTimezone($toTimezone),
            'ends_at' => $endsAt->setTimezone($toTimezone),
            'timezone' => $fromTimezone
        ]);
    }

    private function migrateAbsence($fromTimezone, $toTimezone, $absence)
    {
        if ($this->skipRecordsWithExistingTimezoneInformation($absence)) {
            return;
        }

        $startsAt = Carbon::createFromFormat('Y-m-d H:i:s',$absence->getRawOriginal('starts_at'), $fromTimezone);
        $endsAt = Carbon::createFromFormat('Y-m-d H:i:s',$absence->getRawOriginal('ends_at'), $fromTimezone);

        $absence->update([
            'starts_at' => $startsAt->setTimezone($toTimezone),
            'ends_at' => $endsAt->setTimezone($toTimezone),
            'timezone' => $fromTimezone
        ]);
    }

    private function skipRecordsWithExistingTimezoneInformation($model)
    {
        return !is_null($model->timezone);
    }
}
