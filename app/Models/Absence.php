<?php

namespace App\Models;

use App\Models\User;
use App\Models\Location;
use App\Models\AbsenceType;
use App\Models\AbsenceIndex;
use App\Casts\BigDecimalCast;
use App\Casts\LocalizedDateTimeCast;
use App\Contracts\HasTimeZone;
use App\Formatter\DateFormatter;
use App\Traits\FiltersEmployees;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model implements HasTimeZone
{
    use HasFactory, FiltersEmployees;

    protected $casts = [
        'starts_at' => LocalizedDateTimeCast::class,
        'ends_at' => LocalizedDateTimeCast::class,
        'vacation_days' => BigDecimalCast::class
    ];

    protected $fillable = [
        'location_id',
        'user_id',
        'absence_type_id',
        'starts_at',
        'ends_at',
        'full_day',
        'force_calc_custom_hours',
        'paid_hours',
        'vacation_days',
        'status',
        'timezone'
    ];

    public function absenceType()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function index()
    {
        return $this->hasMany(AbsenceIndex::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Mark absence as confirmed.
     *
     * @return void
     */
    public function markAsConfirmed()
    {
        $this->update([
            'status' => 'confirmed'
        ]);
    }

    /**
     * Mark absence as Pending.
     *
     * @return void
     */
    public function markAsPending()
    {
        $this->update([
            'status' => 'pending'
        ]);
    }

    /**
     * Determine if the absence is confirmed
     *
     * @return bool
     */
    public function isConfimred()
    {
        return $this->status == 'confirmed';
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'indigo',
            'confirmed' => 'green'
        ][$this->status] ?? 'cool-gray';
    }

    public function getStartsAtForHumansAttribute()
    {

        $dateFormatter = app(DateFormatter::class);
        if ($this->full_day) {
            return $dateFormatter->formatDateForView($this->starts_at);
        }

        return $dateFormatter->formatDateTimeForView($this->starts_at);
    }

    public function getEndsAtForHumansAttribute()
    {
        $dateFormatter = app(DateFormatter::class);

        if ($this->full_day) {
            return $dateFormatter->formatDateForView($this->ends_at);
        }

        return $dateFormatter->formatDateTimeForView($this->ends_at);
    }
}
