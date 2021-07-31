<?php

namespace App\Actions;

use Carbon\Carbon;
use App\Models\User;
use App\Report\Report;
use App\Models\Location;
use Carbon\CarbonPeriod;
use App\Formatter\DateFormatter;
use App\Contracts\FiltersEvaluation;
use App\Report\ReportBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FilterEvaluation implements FiltersEvaluation
{
    protected $dateFormatter;

    public function __construct(DateFormatter $dateFormatter) {
        $this->dateFormatter = $dateFormatter;
    }

    /**
     * Approves employee absence
     *
     * @param  User  $employee
     * @param  Location  $location
     * @param  int  $absenceId
     * @return void
     */
    public function filter(User $employee, Location $location, array $filter)
    {
        Validator::make([
            'fromDate' => $filter['fromDate'],
            'toDate' => $filter['toDate']
        ],[
            'fromDate' => ['required', $this->dateFormatter->dateFormatRule()],
            'toDate' => ['required', $this->dateFormatter->dateFormatRule()]
        ])->validateWithBag('filterEmployeeReport');

        if ($this->dateFormatter->dateStrToDate($filter['fromDate'])->diffInDays(
            $this->dateFormatter->dateStrToDate($filter['toDate'])
        ) > 50) {
                throw ValidationException::withMessages([
                    'fromDate' => [ __('Chosen date interval is too big.') ],
                ])->errorBag('filterEmployeeReport');
        }

        if ($employee->date_of_employment) {
            if ($this->dateFormatter->dateStrToDate($filter['fromDate'])->lt($employee->date_of_employment)) {
                throw ValidationException::withMessages([
                    'fromDate' => [__('The start date should not be set before the employment date.')],
                ])->errorBag('filterEmployeeReport');
            }
        }

        return (new ReportBuilder(
            $employee,
            $location,
            $this->dateFormatter->dateStrToDate($filter['fromDate']),
            $this->dateFormatter->dateStrToDate($filter['toDate'])
        ))->build();
    }
}
