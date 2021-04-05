<?php

namespace App\Models;

use App\Models\TimeTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PauseTime extends Model
{
    use HasFactory;

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];

    protected $fillable = ['starts_at', 'ends_at'];

    public function timeTracking()
    {
        return $this->belongsTo(TimeTracking::class);
    }
}
