<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasUser
{
    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }
}
