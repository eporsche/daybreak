<?php

namespace App\Actions;

use App\Formatter\DateFormatter;
use App\Contracts\UpdatesEmployeeProfile;
use Illuminate\Support\Facades\Validator;

class UpdateEmployeeProfile implements UpdatesEmployeeProfile
{
    private $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    /**
     * Update employee profile
     *
     * @param  mixed  $user
     * @param  array  $data
     * @return void
     */
    public function update($user, $data)
    {
        Validator::make($data, [
            'name' => ['required','string','max:255'],
            'date_of_employment' => ['nullable', $this->dateFormatter->dateFormatRule()],
            'opening_overtime_balance' => ['nullable','numeric']
        ])->validateWithBag('saveEmployee');

        $user->forceFill([
            'name' => $data['name'],
            'date_of_employment' =>
                $this->dateFormatter->strToDate($data['date_of_employment']),
            'opening_overtime_balance' =>
                $data['opening_overtime_balance']
        ])->save();
    }
}
