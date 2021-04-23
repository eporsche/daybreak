<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\AbsenceType;
use App\Contracts\AddsAbsences;
use App\Formatter\DateFormatter;
use App\Contracts\RemovesAbsence;
use App\Contracts\ApprovesAbsence;
use App\AbsenceCalendar\AbsenceCalculator;
use App\AbsenceCalendar\EmployeeAbsenceCalendar;

class Absence extends Component
{
    public $absenceModal = false;

    public $confirmingAbsenceRemoval = false;

    public $absenceIdBeingRemoved = null;

    public $hours;
    public $minutes;

    public $location;
    public $employee;

    public $startDate;
    public $startHours = 9;
    public $startMinutes = 0;

    public $endDate;
    public $endHours = 17;
    public $endMinutes = 0;

    public $hideTime = true;

    public $hideDetails = true;

    public $addAbsenceForm = [
        'absence_type_id' => null,
        'full_day' => true
    ];

    public $absenceTypes;

    public $vacationInfoPanel = [
        'overall_vacation_days' => 0,
        'used_days' => 0,
        'transferred_days' => 0,
        'available_days' => 0
    ];

    public $vacationInfoPanelUntil = [
        'until' => null,
        'overall_vacation_days' => 0,
        'used_days' => 0,
        'transferred_days' => 0,
        'available_days' => 0
    ];

    public $vacationDays;

    public $paidHours;

    protected $calculatedDays = [];

    public $employeeFilter = [];

    public $employeeOptions;

    public function updated()
    {
        //TODO set to start of day and end of day if full day is activated...
        $calendar = new EmployeeAbsenceCalendar(
            $this->employee,
            $this->location,
            new CarbonPeriod(
                $this->startTimeStr(),
                $this->endTimeStr()
            )
        );

        $calculator = new AbsenceCalculator($calendar, AbsenceType::findOrFail($this->addAbsenceForm['absence_type_id']));

        $this->calculatedDays = $calculator->getDays();
        $this->vacationDays = (string) $calculator->sumVacationDays();
        $this->paidHours = (string) $calculator->sumPaidHours();
    }

    public function refreshAbsenceHours()
    {
        //TODO set to start of day and end of day if full day is activated...
        $calendar = new EmployeeAbsenceCalendar(
            $this->employee,
            $this->location,
            new CarbonPeriod(
                $this->startTimeStr(),
                $this->endTimeStr()
            )
        );

        $calculator = new AbsenceCalculator($calendar, AbsenceType::findOrFail($this->addAbsenceForm['absence_type_id']));

        $this->calculatedDays = $calculator->getDays();
        $this->vacationDays = $calculator->sumVacationDays();
        $this->paidHours = $calculator->sumPaidHours();
    }

    public function mount(User $employee, DateFormatter $dateFormatter)
    {
        $this->employee = $employee;
        $this->absenceTypes = $employee->absenceTypesForLocation($employee->currentLocation);
        $this->hours = range(0,23);
        $this->minutes = range(0,59);
        $this->location = $employee->currentLocation;
        // $this->employeeSwitcher = $employee->currentLocation->allUsers()->pluck('name','id')->toArray();

        $this->employeeOptions = $employee
            ->currentLocation
            ->allUsers()->pluck('name', 'id')
            ->mapToMultipleSelect();

        $this->resetFormFields();
        $this->buildVacationInfoPanel($employee, $dateFormatter);
    }

    protected function buildVacationInfoPanel($employee, $dateFormatter)
    {
        if ($employee->hasVacationEntitlement()) {
            $this->vacationInfoPanel = array_merge($this->vacationInfoPanel, [
                'overall_vacation_days' => $employee->overallVacationDays(),
                'used_days' => $employee->usedVacationDays(),
                'transferred_days' => $employee->transferredDays(),
                'available_days' => $employee->availableVacationDays()
            ]);

            $latestVacationEntitlement = $employee->latestVacationEntitlement();

            if ($latestVacationEntitlement) {
                $this->vacationInfoPanelUntil = array_merge($this->vacationInfoPanelUntil, [
                    'until' => $dateFormatter->formatDateForView($latestVacationEntitlement->ends_at),
                    'overall_vacation_days' => $employee->overallVacationDays($latestVacationEntitlement->ends_at, true),
                    'used_days' => $employee->usedVacationDays($latestVacationEntitlement->ends_at),
                    'available_days' => $employee->availableVacationDays($latestVacationEntitlement->ends_at, true)
                ]);
            }
        }
    }

    public function switchEmployee()
    {
        $this->employee = $this->location->allUsers()->first(function ($user) {
              return $user->id === (int)$this->employeeIdToBeSwitched;
        });
    }

    public function openAbsenceModal()
    {
        $this->absenceModal = true;
    }

    public function closeAbsenceModal()
    {
        $this->absenceModal = false;
    }

    public function startTimeStr()
    {
        return app(DateFormatter::class)->generateTimeStr(
            $this->startDate,
            $this->startHours,
            $this->startMinutes
        );
    }

    public function endTimeStr()
    {
        return app(DateFormatter::class)->generateTimeStr(
            $this->endDate,
            $this->endHours,
            $this->endMinutes
        );
    }

    public function addAbsence(AddsAbsences $adder)
    {
        $this->clearValidation();

        $this->addAbsenceForm['starts_at'] = $this->startTimeStr();

        $this->addAbsenceForm['ends_at'] = $this->endTimeStr();

        $this->addAbsenceForm['full_day'] = $this->hideTime;

        $adder->add($this->employee, $this->location, $this->addAbsenceForm);

        $this->resetFormfields();

        $this->employee = $this->employee->fresh();

        $this->absenceModal = false;
    }

    protected function resetFormFields()
    {
        $this->startDate = $this->endDate = app(DateFormatter::class)
            ->formatDateForView(Carbon::today());

        $this->addAbsenceForm['absence_type_id']
            = $this->absenceTypes->keys()->first();
    }

    public function approveAbsence($absenceId, ApprovesAbsence $approver)
    {
        if (!empty($absenceId)) {
            $approver->approve(
                $this->employee,
                $this->location,
                $absenceId
            );
        }

        $this->employee = $this->employee->fresh();
    }

    public function confirmAbsenceRemoval($absenceId)
    {
        $this->confirmingAbsenceRemoval = true;

        $this->absenceIdBeingRemoved = $absenceId;
    }

    public function removeAbsence(RemovesAbsence $remover)
    {
        $remover->remove(
            $this->employee,
            $this->absenceIdBeingRemoved
        );

        $this->confirmingAbsenceRemoval = false;

        $this->absenceIdBeingRemoved = null;
    }

    public function render()
    {
        return view('livewire.absence', [
            'calculatedDays' => $this->calculatedDays,
            'absences' => $this->employee->currentLocation
                ->absences()
                ->orderBy('status','DESC')
                ->latest()
                ->when(
                    $this->employee->hasLocationPermission($this->employee->currentLocation, 'filterAbsences'),
                    fn($query) => $query->filterEmployees(
                        collect($this->employeeFilter)->pluck('id')->toArray()
                    ),
                    fn($query) => $query->filterEmployees([$this->employee->id])
                )
                ->paginate(10)
        ]);
    }
}
