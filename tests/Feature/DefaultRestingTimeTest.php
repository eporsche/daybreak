<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Duration;
use App\Models\Location;
use App\Models\TimeTracking;
use App\Actions\AddTimeTracking;
use App\Formatter\DateFormatter;
use App\Contracts\AddsTimeTrackings;
use App\Contracts\AddsDefaultRestingTime;

class DefaultRestingTimeTest extends TestCase
{
    protected $user;

    protected $location;

    protected $dateFormatter;

    public function setUp(): void
    {
        parent::setUp();

        $this->dateFormatter = app(DateFormatter::class);

        $this->user = User::factory([
            'date_of_employment' => Carbon::make('2020-11-01'),
            'current_location_id' => $this->location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => Carbon::make('2020-11-01')
        ])->hasAttached(
            $this->location, [
                'role' => 'admin'
            ]
        )->create();
    }

    public function test_creates_default_resting_time()
    {
        $action = app(AddsDefaultRestingTime::class);
        $action->add($this->location, [
            'min_hours' => new Duration(21600), //6*60*60
            'duration' => new Duration(1800) //30*60
        ]);

        $action->add($this->location, [
            'min_hours' => new Duration(39600), //11*60*60
            'duration' => new Duration(2700) //45*60
        ]);

        $this->assertDatabaseHas('default_resting_times',[
            'min_hours' => '21600',
            'location_id' => $this->location->id
        ]);

        $this->assertDatabaseHas('default_resting_time_users',[
            'user_id' => $this->user->id
        ]);
    }

    public function test_adds_default_resting_time_to_time_tracking()
    {
        $action = app(AddsDefaultRestingTime::class);
        $action->add($this->location, [
            'min_hours' => new Duration(21600), //6*60*60
            'duration' => new Duration(1800) //30*60
        ]);

        $startsAt = Carbon::make('2020-11-17 09:00');
        $endsAt = Carbon::make('2020-11-17 17:00');

        $action = app(AddsTimeTrackings::class);
        $action->add($this->user, $this->location, $this->user->id, [
            'starts_at' => $this->dateFormatter->formatDateTimeForView($startsAt),
            'ends_at' => $this->dateFormatter->formatDateTimeForView($endsAt),
        ],[]);

        $timeTracking = TimeTracking::where('starts_at', $startsAt)->firstOrFail();

        $this->assertSame("1800", (string) $timeTracking->pause_time->inSeconds());
    }

    public function test_do_not_add_default_resting_time_if_pause_time_has_been_given()
    {
        $action = app(AddsDefaultRestingTime::class);
        $action->add($this->location, [
            'min_hours' => new Duration(21600), //6*60*60
            'duration' => new Duration(1800) //30*60
        ]);

        $action = app(AddsTimeTrackings::class);
        $action->add($this->user, $this->location, $this->user->id,  [
            'starts_at' => $this->dateFormatter->formatDateTimeForView(Carbon::make('2020-11-17 09:00')),
            'ends_at' => $this->dateFormatter->formatDateTimeForView(Carbon::make('2020-11-17 17:00')),
        ],[
            [
                'starts_at' => $this->dateFormatter->formatDateTimeForView(Carbon::make('2020-11-17 10:00')),
                'ends_at' => $this->dateFormatter->formatDateTimeForView(Carbon::make('2020-11-17 10:15')),
            ]
        ]);

        $timeTracking = TimeTracking::where('starts_at','2020-11-17 09:00:00')->first();

        $this->assertSame("900", (string) $timeTracking->pause_time->inSeconds());
    }
}
