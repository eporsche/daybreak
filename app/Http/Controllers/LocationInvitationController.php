<?php

namespace App\Http\Controllers;

use App\Contracts\AddsLocationMembers;
use Illuminate\Http\Request;
use App\Models\LocationInvitation;

class LocationInvitationController extends Controller
{
    public function accept(Request $request, LocationInvitation $invitation, AddsLocationMembers $addsLocationMembers)
    {
        $addsLocationMembers->add(
            $invitation->location->owner,
            $invitation->location,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :location team.', ['location' => $invitation->location->name]),
        );
    }
}
