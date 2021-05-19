<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\TimeTracking;
use App\Http\Livewire\TimeTrackingManager;
use Livewire\Livewire;

class UpdateTimeTrackingTest extends TestCase
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

    public function test_creates_time_in_correct_format()
    {
        $timeTracking = TimeTracking::factory([
            'user_id' => $this->user,
            'location_id' => $this->user->currentLocation
        ])->create();

        Livewire::test(TimeTrackingManager::class, [
            'employee' => $this->user
        ])->set([
            'timeTrackingIdBeingUpdated' => $timeTracking->id,
            'timeTrackingForm' => [
                'description' => 'updated',
                'date' => '17.11.2020',
                'start_hour' => 9,
                'start_minute' => 0,
                'end_hour' => 17,
                'end_minute' => 0
            ],
            'pauseTimeForm' => [
                [
                    'start_hour' => 12,
                    'start_minute' => 00,
                    'end_hour' => 12,
                    'end_minute' => 30
                ]
            ]
        ])->call('confirmUpdateTimeTracking');

        $this->assertDatabaseHas('time_trackings', [
            'user_id' => $this->user->id,
            'starts_at' => '2020-11-17 09:00:00',
            'pause_time' => 1800
        ]);
    }
}
