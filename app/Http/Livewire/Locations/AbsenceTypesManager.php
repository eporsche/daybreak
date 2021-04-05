<?php

namespace App\Http\Livewire\Locations;

use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Contracts\AddsAbsenceType;
use App\Contracts\RemovesAbsenceType;
use App\Contracts\UpdatesAbsenceType;

class AbsenceTypesManager extends Component
{
    public $location;

    public $addAbsenceType = false;

    public $currentAbsenceType;

    public $confirmingAbsenceTypeRemoval = false;

    public $absenceTypeIdBeingRemoved = null;

    public $addAbsenceTypeForm = [
        'title' => '',
        'affect_vacation_times' => false,
        'affect_evaluations' => false,
        'regard_holidays' => true,
        'assign_new_users' => true,
        'remove_working_sessions_on_confirm' => false,
        'evaluation_calculation_setting' => null
    ];

    public $evaluationOptions = [];

    public $selectedEmployees = [];

    public $employees;

    public function manageAbsenceType()
    {
        // have a look here on why we should convert input values to string
        // https://github.com/livewire/livewire/issues/788
        $this->selectedEmployees = $this->location->allUsers()
            ->map(function (User $user) {
                return (string) $user->id;
            })->toArray();

        $this->addAbsenceType = true;
    }

    public function stopManagingAbsenceType()
    {
        $this->addAbsenceType = false;

        $this->currentAbsenceType = null;

        $this->reset(['addAbsenceTypeForm', 'selectedEmployees']);
    }

    public function updateAbsenceType($absenceTypeId)
    {
        $this->currentAbsenceType = $this->location->absenceTypeById($absenceTypeId);

        $this->addAbsenceTypeForm = $this->currentAbsenceType->toArray();

        // have a look here on why we should convert input values to string
        // https://github.com/livewire/livewire/issues/788
        $this->selectedEmployees = $this->currentAbsenceType->users()->orderBy('users.id','ASC')
            ->select('users.id')
            ->get()
            ->map(function (User $user) {
                return (string) $user->id;
            })->toArray();

        $this->addAbsenceType = true;
    }

    public function createAbsenceType(AddsAbsenceType $adder)
    {
        $this->resetErrorBag();

        $adder->add(
            $this->location,
            $this->addAbsenceTypeForm,
            $this->selectedEmployees
        );

        $this->location = $this->location->fresh();

        $this->stopManagingAbsenceType();
    }

    public function confirmUpdateAbsenceType(UpdatesAbsenceType $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            $this->currentAbsenceType,
            $this->addAbsenceTypeForm,
            $this->selectedEmployees
        );

        $this->location = $this->location->fresh();

        $this->stopManagingAbsenceType();
    }


    public function mount(Location $location)
    {
        $this->location = $location;

        $this->employees = $location->allUsers()->pluck('name','id')->toArray();

        $this->evaluationOptions = $this->getDefaultEvaluationOptions();
    }

    public function toggleVacationContingent()
    {
        $this->addAbsenceTypeForm['affect_vacation_times'] = $this->addAbsenceTypeForm['affect_vacation_times'] ? false : true;
    }

    public function toggleAffectsEvaluation()
    {
        $this->addAbsenceTypeForm['affect_evaluations'] = $this->addAbsenceTypeForm['affect_evaluations'] ? false : true;

        if (!$this->addAbsenceTypeForm['affect_evaluations']) {
            $this->addAbsenceTypeForm['evaluation_calculation_setting'] = null;
        }
    }

    public function toggleRegardHolidays()
    {
        $this->addAbsenceTypeForm['regard_holidays'] = $this->addAbsenceTypeForm['regard_holidays'] ? false : true;
    }

    public function toggleAssignNewUsers()
    {
        $this->addAbsenceTypeForm['assign_new_users'] = $this->addAbsenceTypeForm['assign_new_users'] ? false : true;
    }

    public function toggleRemoveWorkingSessionsOnConfirm()
    {
        $this->addAbsenceTypeForm['remove_working_sessions_on_confirm'] = $this->addAbsenceTypeForm['remove_working_sessions_on_confirm'] ? false : true;
    }

   public function confirmAbsenceTypeRemoval($absenceTypeId)
   {
        $this->absenceTypeIdBeingRemoved = $absenceTypeId;

        $this->confirmingAbsenceTypeRemoval = true;
   }

    public function removeAbsenceType(RemovesAbsenceType $remover)
    {
        $remover->remove($this->location, $this->absenceTypeIdBeingRemoved);

        $this->absenceTypeIdBeingRemoved = null;

        $this->confirmingAbsenceTypeRemoval = false;

        $this->location = $this->location->fresh();
    }

    public function getDefaultEvaluationOptions()
    {
        return [
            'target_to_zero' => [
                'label' => __('Target to zero (to be implemented)'),
                'description' => __('The target hours will be set to zero in the evaluation.')
            ],
            'absent_to_target' => [
                'label' => __('Absent to target'),
                'description' => __('The absence hours will be summed up.')
            ],
            'fixed_value'  => [
                'label' => __('Fixed value (to be implemented)'),
                'description' => __('Caclulate a fixed value.')
            ],
            'custom_value'=> [
                'label' => __('Custom value (to be implemented)'),
                'description' => __('Employee specific values.')
            ]
        ];
    }

    public function render()
    {
        return view('livewire.locations.absence-types-manager');
    }
}
