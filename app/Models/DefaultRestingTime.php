<?php

namespace App\Models;

use App\Casts\DurationCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultRestingTime extends Model
{
    use HasFactory;

    protected $casts = [
        'min_hours' => DurationCast::class,
        'duration' => DurationCast::class
    ];

    protected $fillable = [
        'min_hours', 'duration', 'location_id'
    ];
}
