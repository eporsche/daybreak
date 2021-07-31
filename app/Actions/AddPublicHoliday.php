<?php

namespace App\Actions;

use App\Models\Location;
use App\Formatter\DateFormatter;
use App\Contracts\AddsPublicHoliday;
use Illuminate\Support\Facades\Validator;

class AddPublicHoliday implements AddsPublicHoliday
{
    public $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    public function add(Location $location, array $data): void
    {
        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', $this->dateFormatter->dateFormatRule()],
            'half_day' => ['required', 'boolean']
        ])->validateWithBag('addPublicHoliday');

        $location->publicHolidays()->create([
            'title' => $data['name'],
            'day' => $this->dateFormatter->dateStrToDate($data['date']),
            'public_holiday_half_day' => $data['half_day']
        ]);
    }
}
