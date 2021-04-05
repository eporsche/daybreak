<?php

namespace App\Models;

use App\Models\User;
use App\Models\Location;
use Spatie\ModelStates\HasStates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\StateMachine\WorkingSession\Places\WorkingSessionState;

class WorkingSession extends Model
{
    use HasFactory;
    use HasStates;

    protected $fillable = [
        'user_id',
        'location_id',
        'status'
    ];

    protected $casts = [
        'status' => WorkingSessionState::class
    ];

    public function actions()
    {
        return $this->hasMany(WorkingSessionAction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
