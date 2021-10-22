<?php

namespace App\Http\Livewire\Absence;

use Carbon\Carbon;
use App\Casts\DateCast;
use App\Traits\HasUser;
use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\AbsenceType;
use Livewire\WithPagination;
use App\Contracts\AddsAbsences;
use App\Formatter\DateFormatter;
use App\Contracts\RemovesAbsence;
use App\Contracts\ApprovesAbsence;
use App\AbsenceCalendar\AbsenceCalculator;
use App\AbsenceCalendar\EmployeeAbsenceCalendar;

class AbsenceManager extends Component
{
    use WithPagination, HasUser;

    public $absenceModal = false;

    public $confirmingAbsenceRemoval = false;

    public $absenceIdBeingRemoved = null;

    public $hours;
    public $minutes;

    /**
     * The user that is currently having its absence managed.
     *
     * @var mixed
     */
    public $managingAbsenceForId;

    public $startDate;
    public $startHours = 0;
    public $startMinutes = 0;

    public $endDate;
    public $endHours = 23;
    public $endMinutes = 59;

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

    public $employeeSimpleSelectOptions;

    public $employeeMultipleSelectOptions;

    public function updated()
    {
        // dd($this->addAbsenceForm['full_day']);
        if ($this->addAbsenceForm['full_day']) {
            $this->reset('startHours','startMinutes','endHours','endMinutes');
        }

        $calendar = new EmployeeAbsenceCalendar(
            $this->user,
            $this->user->currentLocation,
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

    public function mount(DateFormatter $dateFormatter)
    {
        $this->absenceTypes = $this->user->absenceTypesForLocation($this->user->currentLocation);
        $this->hours = range(0,23);
        $this->minutes = range(0,59);

        $employeeSelectCollection = $this->user->currentLocation->allUsers()->pluck('name', 'id');

        $this->employeeSimpleSelectOptions = $employeeSelectCollection->toArray();
        $this->employeeMultipleSelectOptions = $employeeSelectCollection->mapToMultipleSelect();

        $this->managingAbsenceForId = (string)$this->user->id;

        $this->resetFormFields();
        $this->buildVacationInfoPanel($this->user, $dateFormatter);
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

        if ($this->addAbsenceForm['full_day']) {
            $this->reset('startHours','startMinutes','endHours','endMinutes');
        }

        $this->addAbsenceForm = array_merge($this->addAbsenceForm, [
            'starts_at' => $this->startTimeStr(),
            'ends_at' => $this->endTimeStr()
        ]);

        $adder->add(
            $this->user,
            $this->user->currentLocation,
            $this->managingAbsenceForId,
            $this->addAbsenceForm
        );

        $this->resetFormfields();

        $this->user = $this->user->fresh();

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
                $this->user,
                $this->user->currentLocation,
                $absenceId
            );
        }

        $this->user = $this->user->fresh();
    }

    public function confirmAbsenceRemoval($absenceId)
    {
        $this->confirmingAbsenceRemoval = true;

        $this->absenceIdBeingRemoved = $absenceId;
    }

    public function removeAbsence(RemovesAbsence $remover)
    {
        $remover->remove(
            $this->user,
            $this->user->currentLocation,
            $this->absenceIdBeingRemoved
        );

        $this->confirmingAbsenceRemoval = false;

        $this->absenceIdBeingRemoved = null;
    }

    public function render()
    {
        return view('absences.absence-manager', [
            'calculatedDays' => $this->calculatedDays,
            'absences' => $this->user->currentLocation
                ->absences()
                ->orderBy('status','DESC')
                ->latest()
                ->when(
                    $this->user->hasLocationPermission($this->user->currentLocation, 'filterAbsences'),
                    fn($query) => $query->filterEmployees(
                        collect($this->employeeFilter)->pluck('id')->toArray()
                    ),
                    fn($query) => $query->filterEmployees([$this->user->id])
                )
                ->paginate(10)
        ]);
    }
}
