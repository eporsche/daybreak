<?php

namespace App\Http\Livewire\Employee;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Location;
use App\Formatter\DateFormatter;
use App\Contracts\FiltersEvaluation;

class Report extends Component
{
    /**
     * Holds the employee instance
     */
    public $employee;

    /**
     * Holds the location instance
     */
    public $location;

    protected $listeners = [
        'changedDateFilter' => 'filterReport',
        'changedEmployee' => 'filterReport'
    ];

    public $dateFilter = [
        'fromDate' => null,
        'toDate' => null,
    ];

    public $employeeSwitcher;

    public $employeeIdToBeSwitched = null;

    protected $report;

    public function report()
    {
        return $this->report;
    }

    public function mount(User $employee, DateFormatter $dateFormatter)
    {
        $this->employee = $employee;
        $this->location = $employee->currentLocation;
        $this->employeeSwitcher = $employee->currentLocation->allUsers()->pluck('name','id')->toArray();
        $this->dateFilter = [
            'fromDate' =>
                $employee->date_of_employment->gt(now()->startOfMonth()) ?
                $dateFormatter->formatDateForView($employee->date_of_employment) :
                $dateFormatter->formatDateForView(now()->startOfMonth()),
            'toDate' => $dateFormatter->formatDateForView(now()->endOfMonth())
        ];

        $this->filterReport(app(FiltersEvaluation::class));
    }

    public function switchEmployee()
    {
        $this->employee = $this->location->allUsers()->first(function ($user) {
              return $user->id === (int)$this->employeeIdToBeSwitched;
        });

        $this->emit('changedEmployee');
    }

    public function filterUntilToday(DateFormatter $dateFormatter)
    {
        $this->dateFilter['toDate'] = $dateFormatter->formatDateForView(now()->endOfDay());

        $this->emit('changedDateFilter');
    }

    public function filterReport(FiltersEvaluation $evaluation)
    {
        $this->resetErrorBag();

        $this->report = $evaluation->filter(
            $this->employee,
            $this->location,
            $this->dateFilter
        );
    }

    public function render()
    {
        return view('livewire.employee.report');
    }
}
