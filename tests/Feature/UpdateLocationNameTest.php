<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\UpdateLocationNameForm;

class UpdateLocationNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_names_can_be_updated()
    {
        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->withOwnedLocation()
                ->create()
        );

        $user->switchLocation(
            $user->ownedLocations->first()
        );

        Livewire::test(UpdateLocationNameForm::class, ['location' => $user->currentLocation])
                    ->set(['state' => ['name' => 'Test Location']])
                    ->call('updateLocationName');

        $this->assertCount(1, $user->fresh()->ownedLocations);
        $this->assertEquals('Test Location', $user->currentLocation->fresh()->name);
    }
}
