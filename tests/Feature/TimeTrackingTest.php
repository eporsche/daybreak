<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Actions\AddTimeTracking;
use App\Http\Livewire\TimeTracking;

class TimeTrackingTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory([
            "date_of_employment" => Carbon::make('2020-11-16')
        ])->hasTargetHours([
            "start_date" => Carbon::make('2020-11-16')
        ])->create();

        $location = Location::factory()->create();

        $location->users()->attach(
            $this->user,
            ['role' => 'admin']
        );

        $this->user->switchLocation($location);
    }

    public function test_creates_time_in_correct_format()
    {
        $action = app(AddTimeTracking::class);
        $action->add($this->user, [
            'starts_at' => '17.11.2020 09:00',
            'ends_at' => '17.11.2020 17:00',
            ], [[
                'starts_at' => '17.11.2020 12:00',
                'ends_at' => '17.11.2020 12:30',
        ]]);

        $this->assertDatabaseHas('time_trackings', [
            'user_id' => $this->user->id,
            'starts_at' => '2020-11-17 09:00:00',
            'pause_time' => 1800
        ]);
    }

    public function test_creates_correct_durations()
    {
        $minutesDecimaArray = [
            '01' => '0.02',
            '02' => '0.03',
            '03' => '0.05',
            '04' => '0.07',
            '05' => '0.08',
            '06' => '0.10',
            '07' => '0.12',
            '08' => '0.13',
            '09' => '0.15',
            '10' => '0.17',
            '11' => '0.18',
            '12' => '0.20',
            '13' => '0.22',
            '14' => '0.23',
            '15' => '0.25',
            '16' => '0.27',
            '17' => '0.28',
            '18' => '0.30',
            '19' => '0.32',
            '20' => '0.33',
            '21' => '0.35',
            '22' => '0.37',
            '23' => '0.38',
            '24' => '0.40',
            '25' => '0.42',
            '26' => '0.43',
            '27' => '0.45',
            '28' => '0.47',
            '29' => '0.48',
            '30' => '0.50',
            '31' => '0.52',
            '32' => '0.53',
            '33' => '0.55',
            '34' => '0.57',
            '35' => '0.58',
            '36' => '0.60',
            '37' => '0.62',
            '38' => '0.63',
            '39' => '0.65',
            '40' => '0.67',
            '41' => '0.68',
            '42' => '0.70',
            '43' => '0.72',
            '44' => '0.73',
            '45' => '0.75',
            '46' => '0.77',
            '47' => '0.78',
            '48' => '0.80',
            '49' => '0.82',
            '50' => '0.83',
            '51' => '0.85',
            '52' => '0.87',
            '53' => '0.88',
            '54' => '0.90',
            '55' => '0.92',
            '56' => '0.93',
            '57' => '0.95',
            '58' => '0.97',
            '59' => '0.98'
        ];

        foreach ($minutesDecimaArray as $minutes => $decimal) {
            $action = app(AddTimeTracking::class);
            $action->add($this->user, [
                'starts_at' => '17.11.2020 09:00',
                'ends_at' => '17.11.2020 09:'.$minutes
            ],[]);

            /**
             * @var TimeTracking
             */
            $time = $this->user->timeTrackings()->where([
                'starts_at' => '2020-11-17 09:00:00',
                'ends_at' => '2020-11-17 09:'.$minutes.':00',
            ])->first();

            $this->assertEquals($decimal, $time->balance);

            $time->delete();
        }
    }
}
