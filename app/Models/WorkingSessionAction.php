<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkingSessionAction extends Model
{
    use HasFactory;

    protected $casts = [
        'action_time' => 'datetime'
    ];

    protected $fillable = [
        'working_session_id',
        'action_type',
        'action_time'
    ];
}
