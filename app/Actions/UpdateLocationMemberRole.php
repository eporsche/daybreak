<?php

namespace App\Actions;

use Laravel\Jetstream\Rules\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Contracts\UpdatesLocationMembersRole;

class UpdateLocationMemberRole implements UpdatesLocationMembersRole
{
    /**
     * Update the role for the given location member.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  string  $locationMemberId
     * @param  string  $role
     * @return void
     */
    public function update($user, $location, $locationMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateLocationMember', $location);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $location->users()->updateExistingPivot($locationMemberId, [
            'role' => $role,
        ]);
    }
}
