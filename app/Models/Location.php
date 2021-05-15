<?php

namespace App\Models;

use App\Models\User;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\TimeTracking;
use App\Models\PublicHoliday;
use Laravel\Jetstream\Jetstream;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    protected $casts = [
        'owned_by' => 'integer'
    ];

    protected $fillable = [
        'account_id',
        'owned_by',
        'name'
    ];

    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(User::class, 'owned_by');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all of the users that belong to the team.
     */
    public function users()
    {
        return $this->belongsToMany(Jetstream::userModel(), Jetstream::membershipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function timeTrackings()
    {
        return $this->hasMany(TimeTracking::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function absentTypes()
    {
        return $this->hasMany(AbsenceType::class);
    }

    public function defaultRestingTimes()
    {
        return $this->hasMany(DefaultRestingTime::class);
    }

    public function absenceTypeById($id)
    {
        return $this->absentTypes()->findOrFail($id);
    }

    public function absentTypesToBeAssignedToNewUsers()
    {
        return $this->hasMany(AbsenceType::class)->where('assign_new_users', true);
    }

    public function publicHolidays()
    {
        return $this->hasMany(PublicHoliday::class)->orderBy('day','DESC');
    }

    public function publicHolidayForDate($date)
    {
        return $this->publicHolidays()->firstWhere('day', $date);
    }

    /**
     * Determine if the given user belongs to the team.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsLocation($this);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     *
     * @param  string  $email
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the team.
     *
     * @param  \App\Models\User  $user
     * @param  string  $permission
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasLocationPermission($this, $permission);
    }

    /**
     * Get all of the pending user invitations for the team.
     */
    public function locationInvitations()
    {
        return $this->hasMany(LocationInvitation::class);
    }

    /**
     * Remove the given user from the team.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_location_id === $this->id) {
            $user->forceFill([
                'current_location_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }


    public function workingSessions()
    {
        return $this->hasMany(WorkingSession::class);
    }

    /**
     * Purge all of the location's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->absentTypes->each->delete();

        $this->publicHolidays->each->delete();

        $this->owner()->where('current_location_id', $this->id)
            ->update(['current_location_id' => null]);

        $this->users()->where('current_location_id', $this->id)
            ->update(['current_location_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
