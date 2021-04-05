<?php

namespace App\Contracts;

use App\Models\Location;

interface RemovesAbsenceType
{
    public function remove(Location $location, $absenceTypeId);
}
