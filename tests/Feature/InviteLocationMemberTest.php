<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Mail\LocationInvitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager;

class InviteLocationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_be_invited_to_location()
    {
        Mail::fake();

        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->withOwnedLocation()
                ->create()
        );

        $user->switchLocation($user->ownedLocations()->first());

        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
                        ->set('addLocationMemberForm', [
                            'email' => 'test@example.com',
                            'role' => 'admin',
                        ])->call('addLocationMember');

        Mail::assertQueued(LocationInvitation::class);

        $this->assertCount(1, $user->currentLocation->fresh()->locationInvitations);
    }

    public function test_member_invitations_can_be_cancelled()
    {
        $this->actingAs(
            $user = User::factory()
                ->withOwnedAccount()
                ->withOwnedLocation()
                ->create()
        );

        $user->switchLocation($user->ownedLocations()->first());

        // Add the location member...
        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
            ->set('addLocationMemberForm', [
                'email' => 'test@example.com',
                'role' => 'admin',
            ])->call('addLocationMember');

        $invitationId = $user->currentLocation->fresh()->locationInvitations->first()->id;

        // Cancel the location invitation...
        $component->call('cancelLocationInvitation', $invitationId);

        $this->assertCount(0, $user->currentLocation->fresh()->locationInvitations);
    }
}
