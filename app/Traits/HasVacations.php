<?php

namespace App\Traits;

use DateTime;
use Brick\Math\BigDecimal;
use App\Models\VacationEntitlement;

trait HasVacations
{
    public function hasVacationEntitlement()
    {
        return $this->vacationEntitlements()->exists();
    }

    public function vacationEntitlements()
    {
        return $this->hasMany(VacationEntitlement::class);
    }

    public function availableVacationEntitlements()
    {
        return $this->vacationEntitlements()->notUsed();
    }

    public function currentVacationEntitlement()
    {
        $today = now()->startOfDay();
        $available = $this->availableVacationEntitlements()
            ->where('starts_at','<=',$today)
            ->where('ends_at','>=',$today)
            ->orderBy('ends_at','ASC')
            ->get();

        $feasibleEntitlements = $available->filter(function (VacationEntitlement $entitlement) {
            if(!$entitlement->isExpired() && !$entitlement->isUsed()) {
                return $entitlement;
            }
        });

        return $feasibleEntitlements->first();

    }

    public function latestVacationEntitlement()
    {
        return $this->availableVacationEntitlements()
            ->orderBy('ends_at','DESC')
            ->limit(1)
            ->first();
    }

    /**
     * Overal entitled vacation days, except transferred days
     *
     * @param DateTime $until
     * @param boolean  $excludeExpired
     * @return BigDecimal
     */
    public function overallVacationDays(DateTime $until = null, $excludeExpired = false)
    {
        return once(function () use ($until, $excludeExpired) {
            return $this->vacationEntitlements()
                ->when($excludeExpired, function ($query) {
                    $query->whereNotIn('status', ['expired']);
                })
                ->when(isset($until), function ($query) use ($until) {
                    $query->where('ends_at', '<=', $until);
                })->get()->sumBigDecimals('available_days');
        });
    }

    public function transferredDays()
    {
        return once(function () {
            return $this->vacationEntitlements
                ->sumBigDecimals('transfer_days');
        });
    }

    public function usedVacationDays(DateTime $until = null)
    {
        return once(function () use ($until) {
            return $this->vacationEntitlements()
                ->when(isset($until), function ($query) use ($until) {
                    $query->where('ends_at', '<=',$until);
                })->get()->sumBigDecimals('used_days');
        });
    }

    public function availableVacationDays($endsAt = null, $excludeExpired = false)
    {
        return $this->overallVacationDays($endsAt, $excludeExpired)
            ->minus($this->usedVacationDays());
    }
}
