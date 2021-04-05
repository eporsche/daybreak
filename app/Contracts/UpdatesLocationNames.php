<?php

namespace App\Contracts;

interface UpdatesLocationNames
{
    /**
     * Validate and update the given location's name.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  array  $input
     * @return void
     */
    public function update($user, $location, array $input);
}
