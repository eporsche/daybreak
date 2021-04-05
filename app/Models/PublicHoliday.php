<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $dates = ['day'];

    protected $fillable = [
        'title',
        'day',
        'public_holiday_half_day',
        'location_id'
    ];
}
