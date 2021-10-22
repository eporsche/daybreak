<?php

namespace App\Actions;

use DateTimeZone;
use App\Models\Location;
use Illuminate\Validation\Rule;
use App\Contracts\UpdatesLocation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateLocation implements UpdatesLocation
{
    public function update($user, $locationIdBeingUpdated, $data)
    {
        Gate::forUser($user)->authorize('update',
            $location = $user->ownedLocations()
                ->whereKey($locationIdBeingUpdated)
                ->first()
        );

        $validated = Validator::make($data, [
            'name' => ['required', 'string'],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers(DateTimeZone::ALL))]

        ])->validate();

        $location->update($validated);
    }
}
