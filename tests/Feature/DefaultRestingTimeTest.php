<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\TimeTracking;
use App\Actions\AddTimeTracking;
use Illuminate\Support\Facades\DB;
use App\Contracts\AddsDefaultRestingTime;

class DefaultRestingTimeTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory([
            "date_of_employment" => Carbon::make('2020-11-16')
        ])->hasTargetHours([
            "start_date" => Carbon::make('2020-11-16')
        ])->create();

        $location = Location::factory()->create();

        $location->users()->attach(
            $this->user,
            ['role' => 'admin']
        );

        $this->user->switchLocation($location);
    }

    public function test_creates_default_resting_time()
    {
        $action = app(AddsDefaultRestingTime::class);
        $action->add($this->user, $this->user->currentLocation, [
            'min_hours' => '21600', //6*60*60
            'duration' => '180' //30*60
        ]);

        $action->add($this->user, $this->user->currentLocation, [
            'min_hours' => 39600, //11*60*60
            'duration' => 2700 //45*60
        ]);

        $this->assertDatabaseHas('default_resting_times',[
            'min_hours' => '21600',
            'location_id' => $this->user->currentLocation->id
        ]);

        $this->assertDatabaseHas('default_resting_time_users',[
            'user_id' => $this->user->id
        ]);
    }

    public function test_adds_default_resting_time_to_time_tracking()
    {
        $action = app(AddsDefaultRestingTime::class);
        $action->add($this->user, $this->user->currentLocation, [
            'min_hours' => 21600, //6*60*60
            'duration' => 1800 //30*60
        ]);

        $action = app(AddTimeTracking::class);
        $action->add($this->user, [
            'starts_at' => '17.11.2020 09:00',
            'ends_at' => '17.11.2020 17:00',
        ],[]);

        $timeTracking = TimeTracking::where('starts_at','2020-11-17 09:00:00')->first();
        $this->assertEquals(1800, $timeTracking->pause_time);
    }
}
