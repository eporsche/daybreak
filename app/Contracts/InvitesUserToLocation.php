<?php

namespace App\Contracts;

interface InvitesUserToLocation
{
    /**
     * Add a new team member to the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  string  $email
     * @return void
     */
    public function invite($user, $location, string $email, string $role = null);
}
