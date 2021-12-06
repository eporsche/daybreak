<?php

namespace Tests\Feature;

use App\Daybreak;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Report\Report;
use App\Models\Absence;
use App\Models\Account;
use App\Models\Location;
use App\Models\AbsenceType;
use App\Contracts\AddsAbsences;
use App\Contracts\AddsVacationEntitlements;
use App\Contracts\ApprovesAbsence;
use Illuminate\Support\Facades\Bus;
use App\Contracts\FiltersEvaluation;
use Daybreak\Caldav\Jobs\CreateCaldavEvent;

class EvaluationTest extends TestCase
{
    public $user;
    public $account;
    public $location;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory([
            "date_of_employment" => '2020-11-16'
        ])->hasTargetHours([
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
            'time_zone' => 'Europe/Berlin'
        ]);

        $this->user->switchLocation($this->location);
    }

    //Krankheit
    public function test_can_submit_fullday_illness()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Krankheit',
            'affect_vacation_times' => false,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $this->user->absenceTypes()->sync($absenceType);


        /**
         * @var AddsAbsences
         */
        $action = app(AddsAbsences::class);

        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 00:00',
            'ends_at' => '20.11.2020 00:00',
            'full_day' => true,
            'status' => 'confirmed'
        ]);

        $this->assertDatabaseHas('absences', [
            'starts_at' => '2020-11-20 00:00:00',
            'ends_at' => '2020-11-20 23:59:59'
        ]);

        Bus::fake();

        /**
         * @var ApprovesAbsence
         */
        $approver = app(ApprovesAbsence::class);

        $approver->approve(
            $this->user,
            $this->location,
            Absence::first()->id
        );

        $this->assertDatabaseHas("absence_index",[
            'date' => '2020-11-20 00:00:00',
            'hours' => 8,
        ]);

        if (Daybreak::hasCaldavFeature()) {
            Bus::assertDispatched(CreateCaldavEvent::class);
        }

        /**
         * @var FiltersEvaluation
         */
        $action = app(FiltersEvaluation::class);

        /**
         * @var Report
         */
        $report = $action->filter(
            $this->user,
            $this->location,
            [
                'fromDate' => '20.11.2020',
                'toDate' => '20.11.2020'
            ]
        );

        $this->assertTrue(
            $report->reportRows->first()->generate()->plannedHours()->isEqualTo(
                $report->reportRows->first()->generate()->absentHours()
            )
        );

        $this->assertEquals(
            '0',
            (string) $report->reportRows->first()->generate()->diff()
        );
    }

    //Überstunden

    //Urlaub
    public function test_can_use_vacation_entitlement()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $this->user->absenceTypes()->sync($absenceType);

        /**
         * @var AddsVacationEntitlements
         */
        $action = app(AddsVacationEntitlements::class);
        $action->add($this->user, [
            'name' => "yearly allowance",
            'starts_at' => "01.01.2021",
            'ends_at' => "31.12.2021",
            'days' => 2,
            'expires' => false,
            'transfer_remaining' => false
        ]);

        /**
         * @var AddsAbsences
         */
        $action = app(AddsAbsences::class);
        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '29.11.2021 00:00',
            'ends_at' => '30.11.2021 00:00',
            'full_day' => true,
            'status' => 'confirmed'
        ]);

        Bus::fake();

        /**
         * @var ApprovesAbsence
         */
        $approver = app(ApprovesAbsence::class);

        $approver->approve(
            $this->user,
            $this->location,
            Absence::first()->id
        );

        $this->assertDatabaseHas("absences",[
            'id' =>  Absence::first()->id,
            'status' => 'confirmed',
            'vacation_days' => 2,
            'paid_hours' => 16
        ]);

        $this->assertDatabaseHas("vacation_entitlements",[
            'name' => 'yearly allowance',
            'status' => 'used',
        ]);
    }

    public function test_can_submit_future_vacation_entitlement()
    {
        $this->travelTo(Carbon::parse('2021-01-01'));

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $this->user->absenceTypes()->sync($absenceType);

        /**
         * @var AddsVacationEntitlements
         */
        $action = app(AddsVacationEntitlements::class);
        $action->add($this->user, [
            'name' => "yearly allowance",
            'starts_at' => "01.01.2022",
            'ends_at' => "31.12.2022",
            'days' => 10,
            'expires' => false,
            'transfer_remaining' => false
        ]);

        /**
         * @var AddsAbsences
         */
        $action = app(AddsAbsences::class);

        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '29.11.2022 00:00',
            'ends_at' => '30.11.2022 00:00',
            'full_day' => true,
            'status' => 'confirmed'
        ]);

        Bus::fake();

        /**
         * @var ApprovesAbsence
         */
        $approver = app(ApprovesAbsence::class);

        $approver->approve(
            $this->user,
            $this->location,
            Absence::first()->id
        );

    }

    public function test_can_submit_vacation()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $this->user->absenceTypes()->sync($absenceType);

        /**
         * @var AddsVacationEntitlements
         */
        $action = app(AddsVacationEntitlements::class);
        $action->add($this->user, [
            'name' => "yearly allowance",
            'starts_at' => "01.01.2021",
            'ends_at' => "31.12.2021",
            'days' => 2,
            'expires' => false,
            'transfer_remaining' => false
        ]);

        /**
         * @var AddsAbsences
         */
        $action = app(AddsAbsences::class);

        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '29.11.2021 00:00',
            'ends_at' => '30.11.2021 00:00',
            'full_day' => true,
            'status' => 'confirmed'
        ]);

        Bus::fake();

        /**
         * @var ApprovesAbsence
         */
        $approver = app(ApprovesAbsence::class);

        $approver->approve(
            $this->user,
            $this->location,
            Absence::first()->id
        );

        $this->assertDatabaseHas("absence_index",[
            'date' => '2021-11-29 00:00:00',
            'hours' => 8,
        ]);

        $this->assertDatabaseHas("absence_index",[
            'date' => '2021-11-30 00:00:00',
            'hours' => 8,
        ]);

        /**
         * @var AddsVacationEntitlements
         */
        $action = app(AddsVacationEntitlements::class);
        $action->add($this->user, [
            'name' => "additional allowance",
            'starts_at' => "01.01.2021",
            'ends_at' => "31.12.2021",
            'days' => 2,
            'expires' => false,
            'transfer_remaining' => false
        ]);

        /**
         * @var AddsAbsences
         */
        $action = app(AddsAbsences::class);

        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '06.12.2021 00:00',
            'ends_at' => '07.12.2021 00:00',
            'full_day' => true,
            'status' => 'confirmed'
        ]);

        /**
         * @var ApprovesAbsence
         */
        $approver = app(ApprovesAbsence::class);

        $approver->approve(
            $this->user,
            $this->location,
            Absence::where('starts_at','2021-12-06 00:00:00')->first()->id
        );
    }

    //Wunschfrei
}
