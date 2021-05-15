<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class Duration
{
    /**
     * Holds the internal duration represenation in seconds
     *
     * @var BigDecimal
     */
    protected $seconds;

    public function __construct(int $seconds)
    {
        $this->seconds = BigDecimal::of($seconds);
    }

    public function inSeconds()
    {
        return $this->seconds;
    }

    public function inMinutes()
    {
        return $this->seconds->dividedBy(60, 2, RoundingMode::HALF_EVEN);
    }

    public function inHours()
    {
        return $this->inMinutes()->dividedBy(60, 2, RoundingMode::HALF_EVEN);
    }
}
