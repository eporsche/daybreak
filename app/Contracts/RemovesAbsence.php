<?php

namespace App\Contracts;

use App\Models\User;

interface RemovesAbsence
{
    /**
     * Remove a absence.
     *
     * @param  mixed  $user
     * @param  mixed  $removesAbsenceId
     * @return void
     */
    public function remove(User $employee, $removesAbsenceId);
}
