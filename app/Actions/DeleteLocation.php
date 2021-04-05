<?php

namespace App\Actions;

use App\Contracts\DeletesLocations;

class DeleteLocation implements DeletesLocations
{
    /**
     * Delete the given location.
     *
     * @param  mixed  $location
     * @return void
     */
    public function delete($location)
    {
        $location->purge();
    }
}
