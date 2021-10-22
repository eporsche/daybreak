<?php

namespace App\Contracts;

interface UpdatesLocation
{
    public function update($user, $location, $data);
}
