<?php

namespace App\Models;

use App\Traits\HasAbsences;
use App\Traits\HasAccounts;
use App\Traits\HasLocations;
use App\Traits\HasVacations;
use App\Traits\HasEvaluation;
use App\Traits\HasTargetHours;
use App\Traits\HasTimeTrackings;
use App\Casts\BigDecimalCast;
use App\Formatter\DateFormatter;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasDefaultRestingTimes;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Exceptions\NoCurrentLocationSetForUserException;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        HasProfilePhoto,
        HasAccounts,
        HasLocations,
        HasEvaluation,
        HasVacations,
        HasAbsences,
        HasTargetHours,
        HasTimeTrackings,
        HasDefaultRestingTimes,
        Notifiable,
        TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_employment',
        'opening_overtime_balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'current_location_id' => 'integer',
        'account_id' => 'integer',
        'date_of_employment' => 'date',
        'opening_overtime_balance' => BigDecimalCast::class
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function hasEmail($email)
    {
        return $this->email === $email;
    }

    public function getDateOfEmploymentForHumansAttribute()
    {
        return app(DateFormatter::class)
            ->formatDateForView($this->date_of_employment);
    }

    public function workingSessions()
    {
        return $this->hasMany(WorkingSession::class);
    }

    public function currentWorkingSession()
    {
        if (!$this->currentLocation) {
            throw new NoCurrentLocationSetForUserException('Location not set for user');
        }

        if ($this->hasWorkingSession($this->currentLocation)) {
            return $this->workingSessions()
                ->where('location_id', $this->currentLocation->id)
                ->latest()
                ->sole();
        }

        return $this->createWorkingSession($this->currentLocation);
    }

    protected function createWorkingSession(Location $location)
    {
        return $this->workingSessions()->create([
            'location_id' => $location->id
        ]);
    }

    protected function hasWorkingSession(Location $location)
    {
        return $this->workingSessions()
            ->where('location_id', $location->id)
            ->exists();
    }

    public function currentTimezone()
    {
        if ($this->timezone) {
            return $this->timezone;
        }

        if ($this->currentLocation && $this->currentLocation->timezone) {
            return $this->currentLocation->timezone;
        }

        return 'UTC';
    }
}
