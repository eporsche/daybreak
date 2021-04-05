<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class VacationEntitlementTest extends TestCase
{
    public function testCanCreateNotExpiringVacation()
    {
        Carbon::setTestNow(Carbon::parse('2020-01-01'));
    }
}
