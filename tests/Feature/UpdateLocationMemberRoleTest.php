<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
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
                ->create()
        );

        $location = $user->ownedLocations()
            ->save(Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'admin']
        );

        $component = Livewire::test(LocationMemberManager::class, ['location' => $location->fresh()])
            ->set('managingRoleFor', $otherUser)
            ->set('currentRole', 'employee')
            ->call('updateRole');

        $this->assertTrue($otherUser->fresh()->hasLocationRole(
            $location,
            'employee'
        ));
    }

    public function test_only_location_admin_can_update_location_owner_roles()
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

        $component = Livewire::test(LocationMemberManager::class, ['location' => $location->fresh()])
                        ->set('managingRoleFor', $otherUser)
                        ->set('currentRole', 'admin')
                        ->call('updateRole')
                        ->assertStatus(403);

        $this->assertTrue(
            $otherUser->fresh()->hasLocationRole($location, 'employee')
        );
    }
}
