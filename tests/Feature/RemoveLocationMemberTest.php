<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;

class RemoveLocationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_members_can_be_removed_from_locations()
    {
        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->withOwnedLocation()
                ->create()
        );

        $user->switchLocation($user->ownedLocations()->first());

        $user->currentLocation->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
                        ->set('locationMemberIdBeingRemoved', $otherUser->id)
                        ->call('removeLocationMember');

        $this->assertCount(0, $user->currentLocation->fresh()->users);
    }

    public function test_only_location_owner_can_remove_location_members()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->withOwnedLocation()
            ->create();

        $user->switchLocation($user->ownedLocations()->first());

        $user->currentLocation->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
                        ->set('locationMemberIdBeingRemoved', $user->id)
                        ->call('removeLocationMember')
                        ->assertStatus(403);
    }
}
