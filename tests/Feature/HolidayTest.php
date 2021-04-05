<?php

namespace Tests\Feature;

use App\Contracts\AddsPublicHoliday;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Location;
use App\Contracts\ImportsPublicHolidays;

class HolidayTest extends TestCase
{
    public $user;
    public $location;
    public $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->hasTargetHours([
            "start_date" => Carbon::make('2020-11-16')
        ])->create();

        $this->account = Account::forceCreate([
            'owned_by' => $this->user->id,
            'name' => "Account"
        ]);

        $this->location = Location::forceCreate([
            'account_id' => $this->account->id,
            'owned_by' => $this->user->id,
            'name' => "A Location",
            'locale' => 'de',
            'time_zone' => 'Europe/Berlin',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRetrieveHolidays()
    {
        //will be green for mysql database
        $this->markTestSkipped();

        $action = app(ImportsPublicHolidays::class);

        $action->import($this->location, 2020, "NW");

        $this->assertDatabaseHas('public_holidays', [
            "title" => "1. Weihnachtstag",
            "day" => "2020-12-25"
        ]);
    }

    public function testCreatePublicHoliday()
    {
        /**
         * @var AddsPublicHoliday
         */
        $action = app(AddsPublicHoliday::class);

        $action->add($this->location, [
            'name' => 'TestHoliday',
            'date' => '02.02.2020',
            'half_day' => false
        ]);

        $this->assertDatabaseHas('public_holidays', [
            "title" => "TestHoliday",
            "day" => "2020-02-02 00:00:00"
        ]);
    }
}
