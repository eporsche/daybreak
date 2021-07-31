<?php
namespace App\Converter;

class DateTimeConverter
{

    private $utcDateTime;

    private $localTimezone;

    public function setUtcDateTime($utcDateTime)
    {
        $this->utcDateTime = $utcDateTime;
        return $this;
    }

    public function setLocalTimezone($localTimezone)
    {
        $this->localTimezone = $localTimezone;
        return $this;
    }

    public function fromLocalDateTime($dateTime)
    {
        return (new static())
            ->setLocalTimezone($dateTime->getTimezone()->getName())
            ->setUtcDateTime($dateTime->setTimezone(config('app.timezone')));
    }

    public function toUTC()
    {
        return $this->utcDateTime;
    }

    public function toLocalDateTime()
    {
        if (!$this->localTimezone) {
            throw new \Exception('Timezone not set.');
        }
        return $this->utcDateTime->setTimezone($this->localTimezone);
    }
}
