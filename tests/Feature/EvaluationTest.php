<?php

namespace Tests\Feature;

use App\Daybreak;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Report\Report;
use Livewire\Livewire;
use App\Models\Absence;
use App\Models\Account;
use App\Models\Location;
use App\Models\AbsenceType;
use App\Contracts\AddsAbsences;
use App\Contracts\ApprovesAbsence;
use Illuminate\Support\Facades\Bus;
use App\Contracts\FiltersEvaluation;
use Daybreak\Caldav\Jobs\CreateCaldavEvent;
use App\Http\Livewire\Absence\AbsenceManager;

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
            'name' => "A Location"
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

        // $user = User::factory([
        //     'date_of_employment' => '2020-11-01 07:47:05',
        //     'current_location_id' => $location = Location::factory()->create()
        // ])->withOwnedAccount()->hasTargetHours([
        //     'start_date' => '2020-11-01'
        // ])->hasAttached($location, [
        //     'role' => 'admin'
        // ])->create()

        $this->actingAs($this->user);

        Livewire::test(AbsenceManager::class)
            ->set([
                'addAbsenceForm' => [
                    'absence_type_id' => $absenceType->id,
                    'full_day' => true
                ]
            ])->set([
                'startDate' => "20.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 0
            ])->set([
                'startMinutes' => 0
            ])->set([
                'endHours' => 23
            ])->set([
                'endMinutes' => 59
            ])->call('addAbsence');

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

    //Wunschfrei
}
