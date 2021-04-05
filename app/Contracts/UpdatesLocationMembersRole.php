<?php

namespace App\Contracts;

interface UpdatesLocationMembersRole
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
    public function update($user, $location, $locationMemberId, string $role);
}
