<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;

class LeaveLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_locatios()
    {
        $user = User::factory()
            ->withOwnedAccount()
            ->withOwnedLocation()
            ->create();

        $user->switchLocation(
            $user->ownedLocations->first()
        );

        $user->currentLocation->users()->attach(
            $otherUser = User::factory()->create(),
            ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
                        ->call('leaveLocation');

        $this->assertCount(0, $user->currentLocation->fresh()->users);
    }

    public function test_location_owners_cant_leave_their_own_location()
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

        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
                        ->call('leaveLocation')
                        ->assertHasErrors(['location']);

        $this->assertNotNull($user->currentLocation->fresh());
    }

    public function test_user_will_be_logged_out_when_location_gets_deleted()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $this->assertGuest();
    }
}
