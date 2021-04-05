<?php

namespace App\Contracts;

interface AddsTargetHours
{
    /**
     * Invite a new location member to the given location.
     *
     * @param  mixed  $user
     * @param  mixed  $targetHour
     * @return void
     */
    public function add($user, $targetHour, $availbleDays);
}
