<?php

namespace App\Contracts;

use App\Models\Location;

interface AddsAbsenceType
{
    public function add(Location $location, array $data, array $assignedUsers = null);
}
