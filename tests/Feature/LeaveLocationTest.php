<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;

class LeaveLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_locations()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->create()
            ->ownedLocations()
            ->save($location = Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'admin']
        );

        $this->assertCount(1, $location->fresh()->users);

        $this->actingAs($otherUser);

        Livewire::test(LocationMemberManager::class, ['location' => $location])
            ->call('leaveLocation');

        $this->assertCount(0, $location->fresh()->users);
    }

    public function test_location_owners_cant_leave_their_own_location()
    {

        $this->actingAs(
            $user = User::factory([
                'date_of_employment' => '2020-11-01 07:47:05',
            ])->withOwnedAccount()->hasTargetHours([
                'start_date' => '2020-11-01'
            ])->hasAttached($location = Location::factory()->state(
                function (array $attributes, User $user) {
                    return ['owned_by' => $user->id];
                }
            ), [
                'role' => 'admin'
            ])->create()
        );

        Livewire::test(LocationMemberManager::class, ['location' => $user->ownedLocations->first()])
            ->call('leaveLocation')
            ->assertHasErrors(['location']);

        $this->assertNotNull($user->fresh()->ownedLocations);
    }

    public function test_user_will_be_logged_out_when_location_gets_deleted()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $this->assertGuest();
    }
}
