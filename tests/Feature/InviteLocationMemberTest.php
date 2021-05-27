<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use App\Mail\LocationInvitation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\Locations\LocationMemberManager;

class InviteLocationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_be_invited_to_location()
    {
        Mail::fake();

        $this->actingAs(
            $user = User::factory([
                'date_of_employment' => '2020-11-01 07:47:05',
                'current_location_id' => $location = Location::factory()->create()
            ])->withOwnedAccount()->hasTargetHours([
                'start_date' => '2020-11-01'
            ])->hasAttached($location, [
                'role' => 'admin'
            ])->create()
        );

        Livewire::test(LocationMemberManager::class, ['location' => $user->currentLocation])
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
            $user = User::factory([
                'date_of_employment' => '2020-11-01 07:47:05',
            ])->withOwnedAccount()->hasTargetHours([
                'start_date' => '2020-11-01'
            ])->hasAttached(Location::factory()->state(function (array $attributes, User $user) {
                return ['owned_by' => $user->id];
            }), [
                'role' => 'admin'
            ])->create()
        );

        // Add the location member...
        $component = Livewire::test(LocationMemberManager::class, ['location' => $user->locations()->first()])
            ->set('addLocationMemberForm', [
                'email' => 'test@example.com',
                'role' => 'admin',
            ])->call('addLocationMember');

        $invitationId = $user->locations()->first()->locationInvitations->first()->id;

        // Cancel the location invitation...
        $component->call('cancelLocationInvitation', $invitationId);

        $this->assertCount(0, $user->locations()->first()->locationInvitations);
    }
}
