<?php

namespace App\Contracts;

interface DeletesLocations
{
    /**
     * Delete the given location.
     *
     * @param  mixed  $location
     * @return void
     */
    public function delete($location);
}
