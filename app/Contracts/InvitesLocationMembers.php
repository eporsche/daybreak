<?php

namespace App\Contracts;

interface InvitesLocationMembers
{
    /**
     * Invite a new location member to the given location.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  string  $email
     * @return void
     */
    public function invite($user, $location, string $email, string $role = null);
}
