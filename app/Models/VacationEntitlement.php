<?php

namespace App\Models;

use App\Models\Absence;
use App\Casts\BigDecimalCast;
use App\Formatter\DateFormatter;
use Illuminate\Database\Eloquent\Model;
use App\Models\AbsenceVacationEntitlement;
use App\Models\VacationEntitlementTransfer;
use Brick\Math\BigDecimal;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VacationEntitlement extends Model
{
    use HasFactory;

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'days' => BigDecimalCast::class,
        'transfer_remaining' => 'boolean',
        'expires' => 'boolean',
        'end_of_transfer_period' => 'date'
    ];

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'days',
        'expires',
        'transfer_remaining',
        'end_of_transfer_period',
        'transferred_days',
        'status', //expires, does_not_expire, used, expired
    ];

    public function getStartsAtForHumansAttribute()
    {
        return app(DateFormatter::class)->formatDateForView(
            $this->starts_at
        );
    }

    public function getEndsAtForHumansAttribute()
    {
        return app(DateFormatter::class)->formatDateForView(
            $this->ends_at
        );
    }

    public function getUsedDaysAttribute()
    {
        return $this->usedVacationDays->sumBigDecimals(
            fn ($item) => $item->usedVacationDays->used_days
        );
    }

    public function hasEnoughUnusedVacationDays($days)
    {
        return $this->used_days
            ->plus($days)
            ->isLessThanOrEqualTo($this->available_days);
    }

    public function usedVacationDays()
    {
        return $this->belongsToMany(Absence::class, AbsenceVacationEntitlement::class)
            ->as('usedVacationDays')
            ->withPivot('used_days')
            ->withTimestamps();
    }

    public function scopeShouldExpire($query)
    {
        $today = now()->startOfDay();

        return $query
            ->whereIn('status', ['expires'])
            ->whereNotIn('status', ['used', 'does_not_expire','expired'])
            ->where('expires', 1)
            ->where('ends_at','<=',$today);
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function expire()
    {
        return $this->update([
            'status' => 'expired'
        ]);
    }

    /**
     * Can be transferred if not yet transferred and if enabled
     *
     * @return boolean
     */
    public function canBeTransferred()
    {
        return !$this->transferVacationDays()->exists() &&
            $this->transfer_remaining;
    }

    public function transferVacationDays()
    {
        return $this->belongsToMany(
            VacationEntitlement::class,
            'vacation_entitlements_transfer',
            'transferred_from_id',
            'transferred_to_id'
        )
            ->using(VacationEntitlementTransfer::class)
            ->as('transfer')
            ->withPivot('days')
            ->withTimestamps();
    }

    public function transferredVacationDays()
    {
        return $this->belongsToMany(
            VacationEntitlement::class,
            'vacation_entitlements_transfer',
            'transferred_to_id',
            'transferred_from_id'
        )
            ->using(VacationEntitlementTransfer::class)
            ->as('transferred')
            ->withPivot('days')
            ->withTimestamps();
    }

    public function daysTransferrable()
    {
        $daysTransferrable = $this->available_days->minus($this->used_days);
        if ($daysTransferrable->isNegativeOrZero()) {
            return BigDecimal::zero();
        }

        return $daysTransferrable;
    }

    public function useVacationDays(Absence $absence)
    {
        if (!$absence->absenceType->affectsVacation()) {
            return;
        }

        if ($this->isUsed()) {
            throw ValidationException::withMessages([
                'error' => [__('Vacation entitlement has wrong status.')],
            ]);
        }

        $this->usedVacationDays()->attach($absence->id, [
            'used_days' => $absence->vacation_days
        ]);

        if ($this->used_days->isGreaterThanOrEqualTo($this->available_days)) {
            $this->markAsUsed();
        }
    }

    public function markAsUsed()
    {
        $this->update([
            'status' => 'used'
        ]);
    }

    public function isUsed()
    {
        return $this->status === 'used';
    }

    public function isTransferred()
    {
        return $this->transferVacationDays()->exists();
    }

    public function scopeNotUsed($query)
    {
        return $query->whereNotIn('status', ['used']);
    }

    public function getStatusColorAttribute()
    {
        return [
            'does_not_expire' => 'green',
            'expires' => 'indigo',
            'expired' => 'red',
            'used' => 'red'
        ][$this->status] ?? 'cool-gray';
    }

    public function getTransferredDaysAttribute()
    {
        return $this->transferredVacationDays->sumBigDecimals(
            fn($pivot) => $pivot->transferred->days
        );
    }

    public function getTransferDaysAttribute()
    {
        return $this->transferVacationDays->sumBigDecimals(
            fn($pivot) => $pivot->transfer->days
        );
    }

    public function getAvailableDaysAttribute()
    {
        return $this->days->plus($this->transferred_days);
    }
}
