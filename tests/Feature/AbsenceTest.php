<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Location;
use App\Actions\AddAbsence;
use App\Models\AbsenceType;
use Illuminate\Validation\ValidationException;

class AbsenceTest extends TestCase
{
    protected $user;

    protected $account;

    protected $location;

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
            'time_zone' => 'Europe/Berlin'
        ]);

        $this->user->switchLocation($this->location);
    }

    public function test_end_date_must_be_after_start_date()
    {
        $this->expectException(ValidationException::class);

        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 14:00',
            'ends_at' => '20.11.2020 13:30',
            'full_day' => false
        ]);
    }

    public function test_can_create_half_day_vacation()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 14:00',
            'ends_at' => '20.11.2020 16:30',
            'full_day' => false
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 0.5,
            'paid_hours' => 2.5
        ]);
    }

    public function test_can_create_half_days_vacation()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '19.11.2020 22:00',
            'ends_at' => '20.11.2020 02:30',
            'full_day' => false
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 1,
            'paid_hours' => 4.5
        ]);
    }

    public function test_can_create_three_days_illness()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Krankheit',
            'affect_vacation_times' => false,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '18.11.2020 22:00',
            'ends_at' => '20.11.2020 02:30',
            'full_day' => false
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'vacation_days' => 0,
            'paid_hours' => 12.5
        ]);
    }

    public function test_can_create_half_day_illness()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Krankheit',
            'affect_vacation_times' => false,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 14:00',
            'ends_at' => '20.11.2020 16:30',
            'full_day' => false
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'paid_hours' => 2.5,
            'vacation_days' => 0
        ]);
    }

    public function test_can_approve_two_week_vacation()
    {
        $absenceType = AbsenceType::forceCreate([
            'location_id' => $this->location->id,
            'title' => 'Urlaub',
            'affect_vacation_times' => true,
            'affect_evaluations' => true,
            'evaluation_calculation_setting' => 'absent_to_target'
        ]);

        $absenceType->users()->sync($this->user);

        $action = new AddAbsence();

        $action->add($this->user, [
            'absence_type_id' => $absenceType->id,
            'starts_at' => '20.11.2020 14:00',
            'ends_at' => '27.11.2020 16:30',
            'full_day' => true
        ]);

        $this->assertDatabaseHas('absences',[
            'absence_type_id' => $absenceType->id,
            'paid_hours' => 48,
            'vacation_days' => 6
        ]);
    }
}
