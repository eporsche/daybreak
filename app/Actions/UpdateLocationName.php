<?php

namespace App\Actions;

use Illuminate\Support\Facades\Gate;
use App\Contracts\UpdatesLocationNames;
use Illuminate\Support\Facades\Validator;

class UpdateLocationName implements UpdatesLocationNames
{
    /**
     * Validate and update the given team's name.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  array  $input
     * @return void
     */
    public function update($user, $location, array $input)
    {
        Gate::forUser($user)->authorize('update', $location);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateLocationName');

        $location->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
