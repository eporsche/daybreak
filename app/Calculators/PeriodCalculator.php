<?php

namespace App\Calculators;

use Carbon\CarbonPeriod;
use Brick\Math\BigDecimal;
use Carbon\CarbonInterval;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Brick\Math\RoundingMode;
use Illuminate\Support\Collection;

class PeriodCalculator
{
    /**
     * Holds array of period instances
     *
     * @var Collection
     */
    public $periods;

    public function fromPeriod(CarbonPeriod $period)
    {
        $this->periods = collect([$period]);

        return $this;
    }

    public function fromTimesArray($timesArray)
    {
        $periods = [];

        foreach ($timesArray as $period) {
            if (!isset($period['starts_at']) || !isset($period['ends_at'])) {
                throw new \Exception("Not a valid times array.");
            }

            $period = new CarbonPeriod(
                new CarbonImmutable($period['starts_at']),
                CarbonInterval::minutes('1'),
                new CarbonImmutable($period['ends_at'])
            );

            $periods[] = $period;
        }
        $this->periods = collect($periods);

        return $this;
    }

    public function toHours() : BigDecimal
    {
        $hours = BigDecimal::zero();
        foreach ($this->periods as $period) {
            $hours =  $hours->plus(
                BigDecimal::of($period->start->diffInMinutes($period->end))
                    ->dividedBy(60, 2, RoundingMode::HALF_EVEN)
            );
        }
        return $hours;
    }

    public function toMinutes() : BigDecimal
    {
        $minutes = BigDecimal::zero();
        foreach ($this->periods as $period) {
            $minutes =  $minutes->plus(
                BigDecimal::of($period->start->diffInMinutes($period->end))
            );
        }
        return $minutes;
    }

    public function toSeconds() : BigDecimal
    {
        $seconds = BigDecimal::zero();
        foreach ($this->periods as $period) {
            $seconds =  $seconds->plus(
                BigDecimal::of($period->start->diffInSeconds($period->end))
            );
        }
        return $seconds;
    }

    public function hasPeriods() : bool
    {
        return count($this->periods) ? true : false;
    }
}
