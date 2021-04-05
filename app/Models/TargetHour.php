<?php

namespace App\Models;

use App\Casts\BigDecimalCast;
use App\Formatter\DateFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TargetHour extends Model
{
    use HasFactory;

    protected $casts = [
        'is_mon' => 'boolean',
        'is_tue' => 'boolean',
        'is_wed' => 'boolean',
        'is_thu' => 'boolean',
        'is_fri' => 'boolean',
        'is_sat' => 'boolean',
        'is_sun' => 'boolean',
        'target_hours' => BigDecimalCast::class,
        'mon' => BigDecimalCast::class,
        'tue' => BigDecimalCast::class,
        'wed' => BigDecimalCast::class,
        'thu' => BigDecimalCast::class,
        'fri' => BigDecimalCast::class,
        'sat' => BigDecimalCast::class,
        'sun' => BigDecimalCast::class,
        'start_date' => 'date',
        'week_days' => WeekDay::class
    ];

    protected $fillable = [
        'is_mon',
        'is_tue',
        'is_wed',
        'is_thu',
        'is_fri',
        'is_sat',
        'is_sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun',
        'start_date',
        'week_days',
        'target_hours',
        'hours_per'
    ];

    public function createTargetHourSummary()
    {
        return "{$this->target_hours} / {$this->hours_per}";
    }

    public function getStartDateForHumansAttribute()
    {
        return app(DateFormatter::class)->formatDateForView(
            $this->start_date
        );
    }
}
