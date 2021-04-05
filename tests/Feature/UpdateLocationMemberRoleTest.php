<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;

class UpdateLocationMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_member_roles_can_be_updated()
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
                        ->set('managingRoleFor', $otherUser)
                        ->set('currentRole', 'employee')
                        ->call('updateRole');

        $this->assertTrue($otherUser->fresh()->hasLocationRole(
            $user->currentLocation->fresh(),
            'employee'
        ));
    }

    public function test_only_location_owner_can_update_location_owner_roles()
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
                        ->set('managingRoleFor', $otherUser)
                        ->set('currentRole', 'editor')
                        ->call('updateRole')
                        ->assertStatus(403);

        $this->assertTrue($otherUser->fresh()->hasLocationRole(
            $user->currentLocation->fresh(), 'admin'
        ));
    }
}
