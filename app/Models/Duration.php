<?php

namespace App\Models;

use Brick\Math\BigDecimal;

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
        if (!$this->seconds->remainder(60)->isZero()) {
            throw new \Exception("Error converting seconds to minutes");
        }
        return $this->seconds->dividedBy(60);
    }

    public function inHours()
    {
        return $this->inMinutes()->dividedBy(60,2);
    }
}
