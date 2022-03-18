<?php

namespace App\Http\Livewire\Employees;

use App\Daybreak;
use App\Models\User;
use Livewire\Component;
use Brick\Math\BigDecimal;
use App\Formatter\DateFormatter;
use App\Contracts\AddsTargetHours;
use App\Contracts\RemovesTargetHour;
use App\Contracts\UpdatesEmployeeProfile;
use App\Contracts\AddsVacationEntitlements;
use App\Contracts\RemovesVacationEntitlements;
use Illuminate\Validation\ValidationException;
use App\Contracts\TransfersVacationEntitlements;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\RoundingNecessaryException;
use Daybreak\Payroll\Contracts\UpdatesEmployeeProfileWithPayrollId;

class EditUserProfile extends Component
{
    /**
     * Indicates if the application is confirming if a target hours should be removed.
     *
     * @var bool
     */
    public $confirmingTargetHourRemoval = false;

    /**
     * The ID of the target hours being removed.
     *
     * @var int|null
     */
    public $targetHourIdBeingRemoved = null;

    /**
     * Holds the employee profile
     */
    public $employee;

    public $currentlyManagingTargetHours = false;

    public $currentlyManagingVacationEntitlement = false;

    public $targetHourForm = [
        'start_date' => null,
        'hours_per' => 'week',
        'target_hours' => 40,
        'target_limited' => false,
    ];

    public $vacationEntitlementForm = [
        'name' => '',
        'starts_at' => null,
        'ends_at' => null,
        'days' => null,
        'expires' => true,
        'transfer_remaining' => false,
        'end_of_transfer_period' => null
    ];

    public $editUserProfileForm = [
        'name' => null,
        'date_of_employment' => null,
        'opening_overtime_balance' => null,
        'is_account_admin' => false

    ];

    public $days = [
        'mon' => [
            'state' => true,
            'hours' => 8
        ],
        'tue' => [
            'state' => true,
            'hours' => 8
        ],
        'wed' => [
            'state' => true,
            'hours' => 8
        ],
        'thu' => [
            'state' => true,
            'hours' => 8
        ],
        'fri' => [
            'state' => true,
            'hours' => 8
        ],
        'sat' => [
            'state' => false,
            'hours' => 0
        ],
        'sun' => [
            'state' => false,
            'hours' => 0
        ],
    ];

    public $availbleDays;

    public $vacationEntitlementIdBeingRemoved = null;

    public $confirmingVacationEntitlementRemoval = false;

    public function confirmVactationEntitlementRemoval($vacationEntitlementIdBeingRemoved)
    {
        $this->vacationEntitlementIdBeingRemoved = $vacationEntitlementIdBeingRemoved;

        $this->confirmingVacationEntitlementRemoval = true;
    }

    public function removeVacationEntitlement(RemovesVacationEntitlements $remover)
    {
        $remover->remove($this->employee, $this->vacationEntitlementIdBeingRemoved);

        $this->confirmingVacationEntitlementRemoval = false;

        $this->vacationEntitlementIdBeingRemoved = null;

        $this->employee = $this->employee->fresh();

        $this->emit('removedVacationEntitlement');
    }

    public function transferVacationEntitlement($vacationEntitlementId, TransfersVacationEntitlements $transferrer)
    {
        $transferrer->transfer(
            $this->employee,
            $vacationEntitlementId
        );

        $this->employee = $this->employee->fresh();
    }

    public function setHoursPerMode(string $mode)
    {
        $this->resetErrorBag();

        $this->targetHourForm['hours_per'] = $mode;

        switch ($mode) {
            case 'month':
                foreach ($this->days as $key => &$day) {
                    $day['hours'] = 0;
                }
                break;
            default:
                $this->updateDailyHours();
                break;
        }
    }

    public function changeTargetHours(string $value)
    {
        $this->resetErrorBag();

        if (isset($this->targetHourForm['target_hours'])
            && BigDecimal::of($this->targetHourForm['target_hours'])->isEqualTo($value)
        ) {
            return;
        }

        // set current value
        $this->targetHourForm['target_hours'] = $value;

        if ($this->targetHourForm['hours_per'] == 'month') {
            return;
        }

        $this->updateDailyHours();
    }

    protected function updateDailyHours()
    {
        $this->availbleDays = collect($this->days)->filter(function ($days) {
            return $days['state'] == true;
        });

        try {
            $hoursPerDay = BigDecimal::of($this->targetHourForm['target_hours'])
                ->dividedBy($this->availbleDays->count(), 2);
        } catch (DivisionByZeroException $ex) {
            throw ValidationException::withMessages([
                'days' => [ __('Please mark at least one day as active.') ],
            ])->errorBag('addTargetHour');
        } catch (RoundingNecessaryException $ex) {
            throw ValidationException::withMessages([
                'target_hours' => [ __('Rounding error occured. Please select another value.') ],
            ])->errorBag('addTargetHour');
        }

        foreach ($this->availbleDays as $key => $day) {
            $this->days[$key]['hours'] = $hoursPerDay;
        }
    }

    public function toggleDayState(string $day)
    {
        $this->days[$day]['state'] = $this->days[$day]['state'] ? false : true;

        if (!$this->days[$day]['state']) {
            $this->days[$day]['hours'] = 0;
        }

        if ($this->targetHourForm['hours_per'] == 'month') {
            return;
        }

        $this->updateDailyHours();
    }

    public function toggleTargetLimited()
    {
        $this->targetHourForm['target_limited'] = $this->targetHourForm['target_limited'] ? false : true;
    }

    public function mount(User $employee)
    {
        $this->employee = $employee;

        $this->editUserProfileForm = array_merge_when([
            'name' =>
                $this->employee->name,
            'date_of_employment' =>
                $this->employee->date_of_employment_for_humans,
            'opening_overtime_balance' =>
                $this->employee->opening_overtime_balance,
            'is_account_admin' => $this->employee->is_account_admin
        ], fn() => $this->fillPayrollFormFields($employee), Daybreak::hasEmployeePayrollFeature());
    }

    public function manageTargetHour()
    {
        $this->currentlyManagingTargetHours = true;
    }

    public function stopManageTargetHour()
    {
        $this->currentlyManagingTargetHours = false;
    }

    public function saveTargetHour(AddsTargetHours $adder)
    {
        $this->resetErrorBag();

        $adder->add(
            $this->employee,
            array_merge(
                $this->targetHourForm,
                $this->flattenDays($this->days)
            ),
            $this->availbleDays
        );

        $this->employee = $this->employee->fresh();

        $this->currentlyManagingTargetHours = false;

        $this->emit('savedTargetHours');
    }

    public function saveEmployeeProfile()
    {
        $this->resetErrorBag();

        if (Daybreak::hasEmployeePayrollFeature()) {
            app(UpdatesEmployeeProfileWithPayrollId::class)->update(
                $this->employee,
                $this->editUserProfileForm
            );
        } else {
            app(UpdatesEmployeeProfile::class)->update(
                $this->employee,
                $this->editUserProfileForm
            );
        }

        $this->employee = $this->employee->fresh();

        $this->emit('savedProfile');
    }


    protected function flattenDays($days): array
    {
        return collect($days)->map(function ($value, $key) {
            return [
                'is_'.$key => $value['state'],
                $key => $value['hours']
            ];
        })->mapWithKeys(function ($flatten) {
            return $flatten;
        })->toArray();
    }

    public function confirmTargetHourRemoval($targetHourId)
    {
        $this->confirmingTargetHourRemoval = true;

        $this->targetHourIdBeingRemoved = $targetHourId;
    }

    /**
     * Remove target hours from the employee.
     *
     * @param \App\Contracts\RemovesTargetHour $remover
     * @return void
     */
    public function removeTargetHour(RemovesTargetHour $remover)
    {
        $remover->remove(
            $this->employee,
            $this->targetHourIdBeingRemoved
        );

        $this->confirmingTargetHourRemoval = false;

        $this->targetHourIdBeingRemoved = null;

        $this->employee = $this->employee->fresh();

        $this->emit('removedTargetHours');
    }

    public function manageVacationEntitlement()
    {
        $this->vacationEntitlementForm = array_merge($this->vacationEntitlementForm, [
            'starts_at' => app(DateFormatter::class)->formatDateForView(now()->startOfYear()),
            'ends_at' => app(DateFormatter::class)->formatDateForView(now()->endOfYear())
        ]);

        $this->currentlyManagingVacationEntitlement = true;
    }

    public function stopManagingVacationEntitlement()
    {
        $this->currentlyManagingVacationEntitlement = false;
    }

    public function saveVacationEntitlement(AddsVacationEntitlements $adder)
    {
        $this->resetErrorBag();

        $adder->add(
            $this->employee,
            $this->vacationEntitlementForm
        );

        $this->employee = $this->employee->fresh();

        $this->currentlyManagingVacationEntitlement = false;

        $this->emit('savedVacationEntitlement');
    }


    public function render()
    {
        return view('livewire.employees.edit-user-profile');
    }
}
