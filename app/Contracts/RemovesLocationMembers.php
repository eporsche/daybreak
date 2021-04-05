<?php

namespace App\Contracts;

interface RemovesLocationMembers
{
    /**
     * Update the role for the given location member.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  mixed  $teamMember
     * @return void
     */
    public function remove($user, $location, $teamMember);
}
