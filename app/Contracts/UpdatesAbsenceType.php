<?php

namespace App\Contracts;

use App\Models\AbsenceType;

interface UpdatesAbsenceType
{
    public function update(AbsenceType $absenceType, array $data, array $assignedUsers = null);
}
