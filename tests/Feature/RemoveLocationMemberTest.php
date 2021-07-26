<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;

class RemoveLocationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_members_can_be_removed_from_locations()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->create();

        $location = $user->ownedLocations()
            ->save(Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'admin']
        );

        $this->actingAs($user);

        $component = Livewire::test(LocationMemberManager::class, ['location' => $location])
            ->set('locationMemberIdBeingRemoved', $otherUser->id)
            ->call('removeLocationMember');

        $this->assertCount(0, $location->fresh()->users);
    }

    public function test_only_location_admin_can_remove_location_members()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->create();

        $location = $user->ownedLocations()
            ->save(Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'employee']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(LocationMemberManager::class, ['location' => $location])
            ->set('locationMemberIdBeingRemoved', $user->id)
            ->call('removeLocationMember')
            ->assertStatus(403);
    }
}
