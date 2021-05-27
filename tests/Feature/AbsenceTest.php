<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Contracts\AddsAbsences;
use App\Contracts\AddsVacationEntitlements;
use App\Contracts\ApprovesAbsence;
use App\Http\Livewire\AbsenceManager;
use App\Models\AbsenceType;
use Livewire\Livewire;

class AbsenceTest extends TestCase
{
    protected $user;

    protected $account;

    protected $location;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory([
            'date_of_employment' => '2020-11-01 07:47:05',
            'current_location_id' => $this->location = Location::factory()->create()
        ])->withOwnedAccount()->hasTargetHours([
            "start_date" => '2020-11-01'
        ])->hasAttached(
            $this->location, [
                'role' => 'admin'
            ]
        )->create();
    }

    public function test_end_date_must_be_after_start_date()
    {
        $this->actingAs($this->user);

        $absenceType = AbsenceType::factory([
            'location_id' => $this->location->id,
        ])->create();

        $absenceType->users()->sync($this->user);

        Livewire::test(AbsenceManager::class)
            ->set(['addAbsenceForm' => [
                'absence_type_id' => $absenceType->id,
                'full_day' => false
            ]])->set([
                'startDate' => "20.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 13
            ])->set([
                'startMinutes' => 30
            ])->set([
                'endHours' => 12
            ])->set([
                'endMinutes' => 0
            ])->call('addAbsence')->assertHasErrors(['ends_at']);
    }

    public function test_can_create_half_day_vacation()
    {
        $this->actingAs($this->user);

        $absenceType = AbsenceType::factory([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ])->create();

        $absenceType->users()->sync($this->user);

        Livewire::test(AbsenceManager::class)
            ->set(['addAbsenceForm' => [
                'absence_type_id' => $absenceType->id,
                'full_day' => false
            ]])->set(['hideTime' => false])
            ->set([
                'startDate' => "20.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 14
            ])->set([
                'startMinutes' => 00
            ])->set([
                'endHours' => 16
            ])->set([
                'endMinutes' => 30
            ])->call('addAbsence');

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 0.5,
            'paid_hours' => 2.5
        ]);
    }

    public function test_can_create_half_days_vacation()
    {
        $this->actingAs($this->user);

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        Livewire::test(AbsenceManager::class)
            ->set(['addAbsenceForm' => [
                'absence_type_id' => $absenceType->id,
                'full_day' => false
            ]])->set(['hideTime' => false])
            ->set([
                'startDate' => "19.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 22
            ])->set([
                'startMinutes' => 00
            ])->set([
                'endHours' => 2
            ])->set([
                'endMinutes' => 30
            ])->call('addAbsence');

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 1,
            'paid_hours' => 4.5
        ]);
    }

    public function test_can_create_three_days_illness()
    {
        $this->actingAs($this->user);

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Krankheit',
            'affect_vacation_times' => false,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        Livewire::test(AbsenceManager::class)
            ->set(['addAbsenceForm' => [
                'absence_type_id' => $absenceType->id,
                'full_day' => false
            ]])->set(['hideTime' => false])
            ->set([
                'startDate' => "18.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 22
            ])->set([
                'startMinutes' => 00
            ])->set([
                'endHours' => 2
            ])->set([
                'endMinutes' => 30
            ])->call('addAbsence');

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 0,
            'paid_hours' => 12.5
        ]);
    }

    public function test_can_create_half_day_illness()
    {
        $this->actingAs($this->user);

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Krankheit',
            'affect_vacation_times' => false,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        Livewire::test(AbsenceManager::class)
            ->set(['addAbsenceForm' => [
                'absence_type_id' => $absenceType->id,
                'full_day' => false
            ]])->set(['hideTime' => false])
            ->set([
                'startDate' => "20.11.2020"
            ])->set([
                'endDate' => "20.11.2020"
            ])->set([
                'startHours' => 14
            ])->set([
                'startMinutes' => 00
            ])->set([
                'endHours' => 16
            ])->set([
                'endMinutes' => 30
            ])->call('addAbsence');

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'paid_hours' => 2.5,
            'vacation_days' => 0
        ]);
    }

    public function test_can_approve_two_week_vacation()
    {
        $this->actingAs($this->user);

        $this->travelTo($this->user->date_of_employment);

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = app(AddsVacationEntitlements::class);

        $action->add($this->user, [
            'name' => 'Jahresurlaub',
            'starts_at' => '01.01.2020',
            'ends_at' => '31.12.2020',
            'days' => 30,
            'expires' => true,
            'transfer_remaining' => 0,
            'end_of_transfer_period' => '01.03.2021'
        ]);

        $action = app(AddsAbsences::class);

        $action->add($this->user, $this->location, $this->user->id, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 14:00',
            'ends_at' => '27.11.2020 16:30',
            'full_day' => true
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'paid_hours' => 48,
            'vacation_days' => 6,
            'status' => 'pending'
        ]);

        $action = app(ApprovesAbsence::class);

        $action->approve(
            $this->user,
            $this->location,
            $this->user->absences->first()->id
        );

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'paid_hours' => 48,
            'vacation_days' => 6,
            'status' => 'confirmed'
        ]);
    }
}
