<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Location;
use App\Contracts\RemovesLocation;
use App\Contracts\DeletesLocations;

class RemoveLocation implements RemovesLocation
{
    public $deleter;

    public function __construct(DeletesLocations $deleter)
    {
        $this->deleter = $deleter;
    }

    public function remove(User $employee, $removeLocationId)
    {
        $this->deleter->delete(
            Location::findOrFail($removeLocationId)
        );
    }
}
