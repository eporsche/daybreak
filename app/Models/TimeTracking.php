<?php

namespace App\Models;

use App\Models\User;
use App\Traits\HasPeriod;
use Brick\Math\BigDecimal;
use App\Casts\BigDecimalCast;
use App\Casts\DurationCast;
use App\Traits\FiltersEmployees;
use App\Facades\PeriodCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeTracking extends Model
{
    use HasFactory, HasPeriod, FiltersEmployees;

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'hourly_rate' => BigDecimalCast::class,
        'balance' => BigDecimalCast::class,
        'min_billing_increment' => BigDecimalCast::class,
        'pause_time' => DurationCast::class
    ];

    protected $fillable = [
        'description',
        'user_id',
        'location_id',
        'starts_at',
        'ends_at',
        'pause_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pauseTimes()
    {
        return $this->hasMany(PauseTime::class);
    }

    public function getDayAttribute()
    {
        return $this->starts_at->translatedFormat('D d.m');
    }

    public function getTimeAttribute()
    {
        return __(":start - :end o'clock", [
            'start' => $this->starts_at->translatedFormat('H:i'),
            'end' => $this->ends_at->translatedFormat('H:i')
        ]);
    }

    public function getDurationAttribute()
    {
        return PeriodCalculator::fromPeriod($this->period)->toHours();
    }

    public function getPauseTimeForHumansAttribute()
    {
        return $this->pause_time->inHours();
    }

    public function getBalanceAttribute()
    {
        return BigDecimal::of($this->duration)
            ->minus($this->pause_time->inHours());
    }

    public function updatePauseTime()
    {
        $this->update([
            'pause_time' => $this->calculatePauseTime(
                PeriodCalculator::fromTimesArray(
                    $this->pauseTimes()->select('starts_at','ends_at')->get()
                )
            )
        ]);
    }

    protected function calculatePauseTime($pauseTimePeriodCalculator)
    {
        if (!$pauseTimePeriodCalculator->hasPeriods()) {
            $workingTimeInSeconds = PeriodCalculator::fromPeriod($this->period)->toSeconds();
            return optional(optional(
                $this->user->defaultRestingTimes()->firstWhere('min_hours','<=',$workingTimeInSeconds)
            )->duration)->inSeconds();
        } else {
            return $pauseTimePeriodCalculator->toSeconds();
        }
    }
}
