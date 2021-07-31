<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Location;
use App\Formatter\DateFormatter;
use Illuminate\Support\Facades\Validator;
use App\Contracts\AddsVacationEntitlements;

class AddVacationEntitlement implements AddsVacationEntitlements
{
    protected $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }
    public function add(User $employee, array $data)
    {
        Validator::make($data,[
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', $this->dateFormatter->dateFormatRule()],
            'ends_at' => ['required', $this->dateFormatter->dateFormatRule(), 'after_or_equal:starts_at'],
            'days' => ['required','numeric','gte:0'],
            'expires' => ['required','boolean'],
            'transfer_remaining' => ['required', 'boolean'],
            'end_of_transfer_period' => ['required_if:transfer_remaining,1','nullable', $this->dateFormatter->dateFormatRule(), 'after_or_equal:ends_at']
        ])->validateWithBag('vacationEntitlement');

        return $employee->vacationEntitlements()->create(
            array_merge($data, [
                'status' => $this->resolveStatus($data)
            ])
        );
    }

    public function resolveStatus(array $data)
    {
        if (!$data['expires']) {
            return 'does_not_expire';
        }

        if ($this->dateFormatter->dateStrToDate($data['ends_at'])->isPast()) {
            return 'expired';
        }

        return 'expires';
    }
}
