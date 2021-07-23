<?php
namespace App\Converter;

use Carbon\Carbon;
use App\Formatter\DateFormatter;

class DateTimeConverter
{

    private $utcDateTime;

    private $localTimezone;

    public $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function fromUTCDateTime($utcDateTime)
    {
        $this->utcDateTime = $utcDateTime;
        return $this;
    }

    public function fromLocalizedDateTime($dateTime, $tz)
    {
        $dt = Carbon::createFromFormat(
            $this->dateFormatter->getLocalizedDateTimeString(),
            $dateTime,
            $tz
        );

        $this->utcDateTime = $dt->copy()->setTimezone(config('app.timezone'));
        $this->localTimezone = $tz;
        return $this;
    }

    public function toUTC()
    {
        return $this->utcDateTime;
    }

    public function toLocalizedDateTime()
    {
        if (!$this->localTimezone) {
            throw new \Exception('Timezone not set.');
        }
        return $this->utcDateTime->setTimezone($this->localTimezone);
    }
}
