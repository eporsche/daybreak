<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\UpdateLocationNameForm;

class UpdateLocationNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_names_can_be_updated()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->create();

        $location = $user->ownedLocations()
            ->save($location = Location::factory()->make());

        $location->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'admin']
        );

        $this->actingAs($user);

        Livewire::test(UpdateLocationNameForm::class, ['location' => $location->fresh()])
            ->set(['state' => ['name' => 'Test Location']])
            ->call('updateLocationName');

        $this->assertEquals('Test Location', $location->fresh()->name);
    }
}
