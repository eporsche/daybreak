<?php

namespace App\Actions;

use App\Holiday\Client;
use App\Models\Location;
use App\Models\PublicHoliday;
use App\Contracts\ImportsPublicHolidays;
use Illuminate\Support\Facades\Validator;

class ImportPublicHolidays implements ImportsPublicHolidays
{
    public function import(Location $location, $year, $countryCode)
    {
        Validator::make([
            'import_country' => $countryCode,
            'import_year' => $year
            ], [
            'import_country' => [
                'required',
                'string',
                'in:'.collect(config('public_holidays.countries'))->implode('code',','),
            ],
            'import_year' => [
                'required',
                'numeric',
                'digits:4',
                'min:'.date('Y',strtotime('-2 year')),
                'max:'.date('Y',strtotime('+1 year'))
                ]
        ])->validateWithBag('importPublicHolidays');

        $holidays = (new Client(config('public_holidays')))
            ->request($countryCode, $year);

        PublicHoliday::upsert(
            $this->addLocationIdToArray(
                $holidays->toCollection()->toArray(),
                $location
            ),
            ['location_id', 'title','day']
        );
    }

    protected function addLocationIdToArray(array $array, $location)
    {
        $merged = [];
        foreach ($array as &$row) {
            $merged[] = array_merge($row, [
                'location_id' => $location->id
            ]);
        }
        return $merged;
    }
}
