<?php
namespace App\Report;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\Carbon;

class ReportRow
{
    /**
     * @var Row
     */
    protected $previousRow;

    /**
     * The current day
     *
     * @var Carbon
     */
    public $date;

    protected $employee;

    protected $balance;

    protected $plannedHours;

    protected $workingHours;

    protected $diff;

    protected $startingBalance;

    protected $absentHours;

    protected $absentHoursCollection;

    public function __construct(ReportRowBuilder $builder)
    {
        $this->previousRow = $builder->previousRow;
        $this->date = $builder->date;
        $this->employee = $builder->employee;
        $this->location = $builder->location;
        $this->startingBalance = $builder->startingBalance;
        $this->absentHoursCollection = collect();
    }

    public function name() : string
    {
        return $this->employee->name;
    }

    public function label() : string
    {
        return $this->date->translatedFormat('D d.m.Y');
    }

    public function labelColor() : string
    {
        return [
            'Sat' => 'bg-gray-50',
            'Sun' => 'bg-gray-50'
        ][$this->date->shortEnglishDayOfWeek] ?? '';
    }

    public function date() : Carbon
    {
        return $this->date;
    }

    protected function calculateRowBalance()
    {
        if (is_null($this->previousRow)) {
            $this->balance = BigDecimal::zero()
                ->plus($this->diff)
                ->plus($this->startingBalance ?: BigDecimal::zero());
        } else {
            $this->balance = $this->previousRow
                ->balance()
                ->plus($this->diff)
                ->plus($this->startingBalance ?: BigDecimal::zero());
        }
    }

    protected function calculateTargetHours()
    {
        $day = $this->employee->getTargetHour($this->date)
            ->week_days->getDayForDate($this->date);
        $this->plannedHours = $day->state ? $day->hours : BigDecimal::zero();
    }

    protected function calculateWorkingHours()
    {
        $this->workingHours = $this->employee->workingHoursForDate($this->date) ?: BigDecimal::zero();
    }

    protected function calculateAbsentHours()
    {
        $this->absentHoursCollection = $this->employee->absentHoursForDate($this->date)->get();

        $publicHoliday = $this->publicHoliday();

        if (!$publicHoliday) {
            $this->absentHours = $this->absentHoursCollection->sumBigDecimals('hours');
        } else {
            if ($publicHoliday->public_holiday_half_day) {
                $this->absentHours = $this->plannedHours()
                    ->divideBy('2', 2, RoundingMode::HALF_EVEN);
            }
            $this->absentHours = $this->plannedHours();
        }
    }

    protected function calculateDifference()
    {
        $this->diff = $this->workingHours
            ->minus($this->plannedHours)
            ->plus($this->absentHours);
    }

    public function generate() : self
    {
        $this->calculateTargetHours();
        $this->calculateWorkingHours();
        $this->calculateAbsentHours();
        $this->calculateDifference();

        $this->calculateRowBalance();

        return $this;
    }

    /**
     * Zeiterfassungen, also gearbeitete Stunden in der Vergangenheit
     */
    public function workingHours()
    {
        return $this->workingHours;
    }

    /**
     * Anzahl der Stunden, die der Mitarbeiter pro Tag arbeiten soll
     */
    public function plannedHours()
    {
        return $this->plannedHours;
    }

    protected function publicHoliday()
    {
        return $this->location->publicHolidayForDate($this->date);
    }

    public function publicHolidayLabel()
    {
        $publicHoliday = $this->publicHoliday();
        if ($publicHoliday) {
            return $publicHoliday->title;
        }
        return '';
    }

    /**
     * Ist - Soll + Abwesend
     */
    public function diff()
    {
        return $this->diff;
    }

    public function balance(): BigDecimal
    {
        return $this->balance;
    }

    public function absentHours()
    {
        return $this->absentHours;
    }

    public function absentHoursCollection()
    {
        return $this->absentHoursCollection;
    }
}
