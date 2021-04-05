<?php

namespace App\Contracts;

interface AddsLocationMembers
{
    /**
     * Add a new location member to the given location.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  string  $email
     * @return void
     */
    public function add($user, $location, string $email, string $role = null);
}
