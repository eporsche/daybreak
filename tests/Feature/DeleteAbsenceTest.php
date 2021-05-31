<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Location;
use App\Http\Livewire\Absence\AbsenceManager;

class DeleteAbsenceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_delete_absence()
    {
        $this->actingAs($user = User::factory()->withOwnedAccount()->create());

        $location = Location::factory()->create();

        $location->users()->attach(
            $user,
            ['role' => 'admin']
        );

        $user->switchLocation($location);

        $absentType = $user->allLocations()->first()->absentTypes()->create([
            'title' => 'Illness',
            'location_id' => $user->allLocations()->first()->id,
            'affect_vacation_times' => 1,
            'affect_evaluations' => 1,
            'evaluation_calculation_setting' => 'absent_to_target',
            'regard_holidays' => 0,
            'assign_new_users' => 0,
            'remove_working_sessions_on_confirm' => 0
        ]);

        $absentType->users()->sync($user);

        $absence = $user->absences()->create([
            'location_id' => $user->allLocations()->first()->id,
            'vacation_days' => 2,
            'paid_hours' => 16,
            'starts_at' => '2020-12-12',
            'ends_at' => '2020-12-12',
            'full_day' => false,
            'absence_type_id' => 1,
        ]);

        Livewire::test(AbsenceManager::class, ['employee' => $user])->set([
            'absenceIdBeingRemoved' => $absence->id
        ])->call('removeAbsence');

        $this->assertCount(0, $user->fresh()->absences);
    }
}
