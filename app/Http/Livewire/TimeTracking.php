<?php

namespace App\Http\Livewire;

use App\Daybreak;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Arr;
use Livewire\WithPagination;
use App\Formatter\DateFormatter;
use App\Contracts\AddsTimeTrackings;
use Illuminate\Support\Facades\Auth;
use App\Contracts\RemovesTimeTracking;
use App\Contracts\UpdatesTimeTracking;
use Daybreak\Project\Contracts\AddsTimeTrackingWithProjectInfo;
use Daybreak\Project\Contracts\UpdatesTimeTrackingWithProjectInfo;

class TimeTracking extends Component
{
    use WithPagination, TrimAndNullEmptyStrings;

    /**
     * Indicates if the application is currently adding or editin time trackings
     *
     * @var bool
     */
    public $manageTimeTracking = false;

    /**
     * Indicates if the application is confirming if a time tracking should be removed.
     *
     * @var bool
     */
    public $confirmingTimeTrackingRemoval = false;

    /**
     * The ID of the time tracking being removed.
     *
     * @var int|null
     */
    public $timeTrackingIdBeingRemoved = null;

    public $timeTrackingIdBeingUpdated = null;

    public $employee;

    protected $listeners = ['changedTime' => 'updatePause'];

    public $hours;

    public $minutes;

    public $workingSession;

    public $timeTrackingForm = [
        'description' => null,
        'date' => null,
        'start_hour' => 9,
        'start_minute' => 0,
        'end_hour' => 17,
        'end_minute' => 0,
    ];

    public $pauseTimeForm = [];

    public $employeeFilter;

    public $employeeOptions;

    public function mount(User $employee, DateFormatter $dateFormatter)
    {
        $this->employee = $employee;

        $this->employeeOptions = $employee
            ->currentLocation
            ->allUsers()->pluck('name', 'id')
            ->mapToMultipleSelect();

        $this->employeeFilter = collect($this->employeeOptions)
            ->filterMultipleSelect(fn($item) => $item['id'] === $this->employee->id);

        $this->timeTrackingForm = array_merge_when(array_merge($this->timeTrackingForm,[
            'date' => $dateFormatter->formatDateTimeForView(Carbon::today())
        ]), fn() => $this->projectFormFields(), Daybreak::hasProjectBillingFeature());

        $this->hours = range(0, 23);
        $this->minutes = range(0, 59);

        $this->workingSession = $employee->currentWorkingSession();
    }

    public function switchEmployee()
    {
        $this->employee = $this->employee->currentLocation->allUsers()->first(function ($user) {
            return $user->id === (int)$this->employeeIdToBeSwitched;
        });
    }

    protected function generatePauseTimeArray($date, array $pauseTimes, DateFormatter $dateFormatter)
    {
        return array_map(function ($pause) use ($date, $dateFormatter) {
            return [
                'starts_at' => $dateFormatter->generateTimeStr(
                    $date,
                    $pause['start_hour'],
                    $pause['start_minute']
                ),
                'ends_at' => $dateFormatter->generateTimeStr(
                    $date,
                    $pause['end_hour'],
                    $pause['end_minute']
                )
            ];
        }, $pauseTimes, []);
    }

    public function confirmAddTimeTracking(DateFormatter $dateFormatter)
    {
        $this->resetErrorBag();

        $generatedPauseTimeArray = $this->generatePauseTimeArray($this->timeTrackingForm['date'], $this->pauseTimeForm, $dateFormatter);

        if (Daybreak::hasProjectBillingFeature()) {
            app(AddsTimeTrackingWithProjectInfo::class)->add(
                $this->employee, array_merge([
                    'starts_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['start_hour'], $this->timeTrackingForm['start_minute']),
                    'ends_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['end_hour'], $this->timeTrackingForm['end_minute']),
                ], $this->filteredTimeTrackingFormFields()),
                $generatedPauseTimeArray
            );
        } else {
            app(AddsTimeTrackings::class)->add(
                $this->employee, array_merge([
                    'starts_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['start_hour'], $this->timeTrackingForm['start_minute']),
                    'ends_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['end_hour'], $this->timeTrackingForm['end_minute']),
                ], $this->filteredTimeTrackingFormFields()),
                $generatedPauseTimeArray
            );
        }

        $this->employee = $this->employee->fresh();

        $this->manageTimeTracking = false;
    }

    public function render()
    {
        return view('livewire.time-tracking', [
            'timing' => $this->employee->currentLocation
                ->timeTrackings()
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

    public function addPauseTime()
    {
        $this->pauseTimeForm[] = [
            'start_hour' => 12,
            'start_minute' => 00,
            'end_hour' => 12,
            'end_minute' => 30
        ];
    }

    public function removePause($index)
    {
        unset($this->pauseTimeForm[$index]);
    }

    public function updatePause($pause, $index)
    {
        $this->pauseTimeForm[$index] = $pause;
    }

    public function confirmTimeTrackingRemoval($timeTrackingId)
    {
        $this->confirmingTimeTrackingRemoval = true;

        $this->timeTrackingIdBeingRemoved = $timeTrackingId;
    }

    /**
     * Remove a time tracking from the employee.
     *
     * @param  \App\Contracts\RemovesTimeTracking  $remover
     * @return void
     */
    public function removeTimeTracking(RemovesTimeTracking $remover)
    {
        $remover->remove(
            $this->employee,
            $this->timeTrackingIdBeingRemoved
        );

        $this->confirmingTimeTrackingRemoval = false;

        $this->timeTrackingIdBeingRemoved = null;

        $this->employee = $this->employee->fresh();
    }

    public function manageTimeTracking()
    {
        $this->manageTimeTracking = true;
    }

    public function updatedManageTimeTracking($managing)
    {
        if (!$managing) {
            $this->cancelManagingTimeTracking();
        }
    }

    public function cancelManagingTimeTracking()
    {
        $this->reset([
            'manageTimeTracking',
            'timeTrackingIdBeingUpdated',
            'timeTrackingForm',
            'pauseTimeForm'
        ]);
    }

    public function updateTimeTracking($index)
    {
        $this->timeTrackingIdBeingUpdated = $index;

        $this->updateTimeTrackingForm(
            $this->employee->timeTrackings()
                ->whereKey($index)
                ->with('pauseTimes')
                ->first()
        );

        $this->manageTimeTracking = true;
    }

    public function updateTimeTrackingForm($timeTracking)
    {
        $this->timeTrackingForm = array_merge_when(array_merge($this->timeTrackingForm,[
            'date' => app(DateFormatter::class)->formatDateForView($timeTracking->starts_at),
            'start_hour' => $timeTracking->starts_at->hour,
            'start_minute' => $timeTracking->starts_at->minute,
            'end_hour' => $timeTracking->ends_at->hour,
            'end_minute' => $timeTracking->ends_at->minute,
            'description' => $timeTracking->description
        ]), fn() => $this->updateProjectFormFields($timeTracking), Daybreak::hasProjectBillingFeature());

        $this->pauseTimeForm = [];

        $timeTracking->pauseTimes->each(function ($pauseTime) {
            $this->pauseTimeForm[] = [
                'start_hour' => $pauseTime->starts_at->hour,
                'start_minute' => $pauseTime->starts_at->minute,
                'end_hour' => $pauseTime->ends_at->hour,
                'end_minute' => $pauseTime->ends_at->minute
            ];
        });
    }

    public function confirmUpdateTimeTracking(DateFormatter $dateFormatter)
    {
        $this->resetErrorBag();

        $generatedPauseTimeArray = $this->generatePauseTimeArray($this->timeTrackingForm['date'], $this->pauseTimeForm, $dateFormatter);

        if (Daybreak::hasProjectBillingFeature()) {
            app(UpdatesTimeTrackingWithProjectInfo::class)->update(
                $this->employee,
                $this->timeTrackingIdBeingUpdated,
                array_merge([
                    'starts_at' => $dateFormatter->generateTimeStr(
                        $this->timeTrackingForm['date'],
                        $this->timeTrackingForm['start_hour'],
                        $this->timeTrackingForm['start_minute']
                    ),
                    'ends_at' => $dateFormatter->generateTimeStr(
                        $this->timeTrackingForm['date'],
                        $this->timeTrackingForm['end_hour'],
                        $this->timeTrackingForm['end_minute']
                    ),
                ], $this->filteredTimeTrackingFormFields()),
                $generatedPauseTimeArray
            );
        } else {
            app(UpdatesTimeTracking::class)->update(
                $this->employee,
                $this->timeTrackingIdBeingUpdated,
                array_merge([
                    'starts_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['start_hour'], $this->timeTrackingForm['start_minute']),
                    'ends_at' => $dateFormatter->generateTimeStr($this->timeTrackingForm['date'], $this->timeTrackingForm['end_hour'], $this->timeTrackingForm['end_minute']),

                ], $this->filteredTimeTrackingFormFields()),
                $generatedPauseTimeArray
            );
        }

        $this->reset([
            'manageTimeTracking',
            'timeTrackingIdBeingUpdated',
            'timeTrackingForm',
            'pauseTimeForm'
        ]);
    }

    public function transition($to)
    {
        $state = $this->workingSession->status->resolveStateClass($to);

        if (!is_null($state) && $this->workingSession->status->canTransitionTo($state)) {
            $this->workingSession->status->transitionTo($state);
        }

        $this->workingSession = $this->workingSession->fresh();
    }

    private function filteredTimeTrackingFormFields()
    {
        return Arr::except($this->timeTrackingForm, ['date', 'start_hour', 'start_minute', 'end_hour', 'end_minute']);
    }
}
