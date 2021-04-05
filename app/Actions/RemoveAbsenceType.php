<?php

namespace App\Actions;

use App\Models\Location;
use App\Contracts\RemovesAbsenceType;

class RemoveAbsenceType implements RemovesAbsenceType
{
    public function remove(Location $location, $absenceTypeId)
    {
        $location->absentTypes()->whereKey($absenceTypeId)->delete();
    }
}
