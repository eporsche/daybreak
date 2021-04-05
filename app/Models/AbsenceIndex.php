<?php

namespace App\Models;

use App\Models\User;
use App\Models\Absence;
use App\Casts\BigDecimalCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceIndex extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'hours',
        'absence_type_id',
        'user_id',
        'location_id'
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => BigDecimalCast::class
    ];

    protected $table = 'absence_index';

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
