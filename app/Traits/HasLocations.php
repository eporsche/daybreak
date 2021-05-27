<?php

namespace App\Traits;

use App\Daybreak;
use App\Models\Location;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\OwnerRole;
use Laravel\Sanctum\HasApiTokens;

trait HasLocations
{
    public function ownedLocations()
    {
        return $this->hasMany(Daybreak::locationModel(), 'owned_by');
    }

    public function ownsLocation($location)
    {
        if (!$location) {
            return false;
        }
        return $this->id == $location->owned_by;
    }

    /**
     * Determine if the given location is the current location.
     *
     * @param  mixed  $location
     * @return bool
     */
    public function isCurrentLocation($location)
    {
        return $location->id === $this->currentLocation->id;
    }

    /**
     * Get the current location of the user's context.
     */
    public function currentLocation()
    {
        return $this->belongsTo(Daybreak::locationModel(), 'current_location_id');
    }

    /**
     * Switch the user's context to the given location.
     *
     * @return bool
     */
    public function switchLocation(Location $location)
    {
        if (! $this->belongsToLocation($location)) {
            return false;
        }

        $this->forceFill([
            'current_location_id' => $location->id,
        ])->save();

        $this->setRelation('currentLocation', $location);

        return true;
    }

    /**
     * Get all of the locations the user owns or belongs to.
     *
     * @return \Illuminate\Collections\Collection
     */
    public function allLocations()
    {
        return $this->ownedLocations->merge($this->locations)->sortBy('name');
    }

    /**
     * Get all of the locations the user belongs to.
     */
    public function locations()
    {
        return $this->belongsToMany(Daybreak::locationModel(), Daybreak::membershipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Determine if the user belongs to the given location.
     *
     * @param  mixed  $location
     * @return bool
     */
    public function belongsToLocation($location)
    {
        return $this->locations->contains(function ($l) use ($location) {
            return $l->id === $location->id;
        }) || $this->ownsLocation($location);
    }

    /**
     * Get the role that the user has on the location.
     *
     * @param  mixed  $location
     * @return \Laravel\Jetstream\Role
     */
    public function locationRole($location)
    {
        if ($this->ownslocation($location)) {
            return new OwnerRole;
        }

        if (! $this->belongsToLocation($location)) {
            return;
        }

        return Jetstream::findRole($location->users->where(
            'id', $this->id
        )->first()->membership->role);
    }

    /**
     * Determine if the user has the given role on the given location.
     *
     * @param  mixed  $location
     * @param  string  $role
     * @return bool
     */
    public function hasLocationRole($location, string $role)
    {
        if ($this->ownsLocation($location)) {
            return true;
        }

        return $this->belongsToLocation($location) && optional(Jetstream::findRole($location->users->where(
            'id', $this->id
        )->first()->membership->role))->key === $role;
    }

    /**
     * Get the user's permissions for the given location.
     *
     * @param  mixed  $location
     * @return array
     */
    public function locationPermissions($location)
    {
        if ($this->ownsLocation($location)) {
            return ['*'];
        }

        if (! $this->belongsToLocation($location)) {
            return [];
        }

        return $this->locationRole($location)->permissions;
    }

    /**
     * Determine if the user has the given permission on the given location.
     *
     * @param  mixed  $location
     * @param  string  $permission
     * @return bool
     */
    public function hasLocationPermission($location, string $permission)
    {
        if ($this->ownslocation($location)) {
            return true;
        }

        if (! $this->belongsTolocation($location)) {
            return false;
        }

        if (in_array(HasApiTokens::class, class_uses_recursive($this)) &&
            ! $this->tokenCan($permission) &&
            $this->currentAccessToken() !== null) {
            return false;
        }

        $permissions = $this->locationPermissions($location);

        return in_array($permission, $permissions) ||
               in_array('*', $permissions) ||
               (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
               (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }

    public function isLocationAdmin($location)
    {
        return $this->hasLocationRole($location, 'admin');
    }
}
