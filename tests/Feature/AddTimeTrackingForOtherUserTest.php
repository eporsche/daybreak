<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use App\Http\Livewire\TimeTracking\TimeTrackingManager;

class AddTimeTrackingForOtherUserTest extends TestCase
{
    public function test_can_create_time_for_other_user()
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

        $this->actingAs($user);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $otherUser->id,
            'timeTrackingForm' => [
                'description' => 'testing',
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
        ])->call('confirmAddTimeTracking');

        $this->assertDatabaseHas('time_trackings', [
            'user_id' => $otherUser->id,
            'description' => 'testing'
        ]);
    }

    public function test_employee_cannot_create_time_for_other_user()
    {
        $user = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            'start_date' => '2020-11-01'
        ])->hasAttached($location, [
            'role' => 'employee'
        ])->create();

        $location->users()->attach(
            $otherUser = User::factory([
                'current_location_id' => $location->id,
                'date_of_employment' => '2020-11-01 07:47:05',
            ])->create(),
            ['role' => 'employee']
        );

        $this->actingAs($user);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $otherUser->id,
            'timeTrackingForm' => [
                'description' => 'testing',
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
        ])->call('confirmAddTimeTracking')->assertStatus(403);

        $this->assertDatabaseCount('time_trackings', 0);
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


        $this->actingAs($user);

        Livewire::test(TimeTrackingManager::class)->set([
            'managingTimeTrackingForId' => $otherUser->id,
            'timeTrackingForm' => [
                'description' => 'testing',
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
        ])->call('confirmAddTimeTracking')->assertStatus(403);

        $this->assertDatabaseCount('time_trackings', 0);
    }
}
