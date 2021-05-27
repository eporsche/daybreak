<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Http\Livewire\Locations\LocationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_locations_can_be_created()
    {
        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->create()
        );

        Livewire::test(LocationManager::class, [
            'account' => $user->ownedAccount,
            'employee' => $user
        ])
            ->set([
                'addLocationForm' => ['name' => 'Test Location']
            ])
            ->call('confirmAddLocation');

        $this->assertEquals('Test Location', $user->fresh()->ownedLocations()->latest('id')->first()->name);
    }
}
