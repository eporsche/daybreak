<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use App\Http\Livewire\TimeTracking\TimeTrackingManager;

class ChangeTimeTrackingForOtherUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_change_time_for_other_user()
    {
        $user = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => '2020-11-01'
        ])->hasAttached($location, [
            'role' => 'admin'
        ])->create();

        $location->users()->attach(
            $otherUser = User::factory([
                'current_location_id' => $location->id,
                'date_of_employment' => '2020-11-01 07:47:05',
            ])->create(),
            ['role' => 'admin']
        );

        $timeTracking = $user->timeTrackings()->create([
            'location_id' => $location->id,
            'starts_at' => '2020-11-01 08:00:00',
            'ends_at' => '2020-11-01 17:00:00'
        ]);

        $this->actingAs($otherUser);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $user->id,
            'timeTrackingIdBeingUpdated' => $timeTracking->id,
            'timeTrackingForm' => [
                'description' => 'testing',
                'date' => '01.11.2020',
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
            'user_id' => $user->id,
            'starts_at' => '2020-11-01 09:00:00'
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_employee_cannot_change_time_for_other_user()
    {
        $user = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => '2020-11-01'
        ])->hasAttached($location, [
            'role' => 'admin'
        ])->create();

        $location->users()->attach(
            $otherUser = User::factory([
                'current_location_id' => $location->id,
                'date_of_employment' => '2020-11-01 07:47:05',
            ])->create(),
            ['role' => 'employee']
        );

        $timeTracking = $user->timeTrackings()->create([
            'location_id' => $location->id,
            'starts_at' => '2020-11-01 08:00:00',
            'ends_at' => '2020-11-01 17:00:00'
        ]);

        $this->actingAs($otherUser);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $user->id,
            'timeTrackingIdBeingUpdated' => $timeTracking->id,
            'timeTrackingForm' => [
                'description' => 'testing',
                'date' => '01.11.2020',
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
        ])->call('confirmUpdateTimeTracking')->assertStatus(403);

        $this->assertDatabaseHas('time_trackings', [
            'user_id' => $user->id,
            'starts_at' => '2020-11-01 08:00:00'
        ]);
    }

    public function test_admin_cannot_create_time_for_other_location_user()
    {
        $user = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => '2020-11-01'
        ])->hasAttached($location, [
            'role' => 'employee'
        ])->create();

        $otherUser = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $otherLocation = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => '2020-11-01'
        ])->hasAttached($otherLocation, [
            'role' => 'employee'
        ])->create();

        $timeTracking = $user->timeTrackings()->create([
            'location_id' => $location->id,
            'starts_at' => '2020-11-01 08:00:00',
            'ends_at' => '2020-11-01 17:00:00'
        ]);

        $this->actingAs($otherUser);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $user->id,
            'timeTrackingIdBeingUpdated' => $timeTracking->id,
            'timeTrackingForm' => [
                'description' => 'testing',
                'date' => '01.11.2020',
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
        ])->call('confirmUpdateTimeTracking')->assertStatus(403);


        $this->assertDatabaseHas('time_trackings', [
            'user_id' => $user->id,
            'starts_at' => '2020-11-01 08:00:00'
        ]);
    }
}
