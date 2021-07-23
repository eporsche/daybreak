<?php

namespace App\Models;

use App\Casts\LocalizedDateTimeCast;
use App\Contracts\HasTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkingSessionAction extends Model implements HasTimeZone
{
    use HasFactory;

    protected $casts = [
        'action_time' => LocalizedDateTimeCast::class
    ];

    protected $fillable = [
        'working_session_id',
        'action_type',
        'action_time',
        'timezone'
    ];
}
