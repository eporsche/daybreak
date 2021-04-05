<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use App\Http\Livewire\Locations\LocationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_locations_can_be_deleted()
    {
        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->withOwnedLocation()
                ->create()
        );

        $user->ownedLocations()
            ->save($location = Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'test-role']
        );

        $component = Livewire::test(
            LocationManager::class, [
            'account' => $user->ownedAccount,
            'employee' => $user
        ])->set([
            'locationIdBeingRemoved' => $location->id
        ])->call('removeLocation');

        $this->assertNull($location->fresh());
        $this->assertCount(0, $otherUser->fresh()->locations);
    }
}
