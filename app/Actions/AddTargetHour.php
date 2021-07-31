<?php

namespace App\Actions;

use App\Models\User;
use App\Formatter\DateFormatter;
use App\Contracts\AddsTargetHours;
use App\Rules\NumberHasCorrectScale;
use Illuminate\Support\Facades\Validator;

class AddTargetHour implements AddsTargetHours
{
    protected $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }
    /**
     * Add target hour profile for user
     *
     * @param  User  $user
     * @param  array  $targetHour
     * @return void
     */
    public function add($user, $targetHour, $availbleDays)
    {
        Validator::make(array_merge($targetHour, [
            'days' => $availbleDays->count()
        ]),[
            'start_date' => ['required', $this->dateFormatter->dateFormatRule()],
            'days' => ['required', 'gte:1'],
            'target_hours' => ['required', 'numeric', new NumberHasCorrectScale($availbleDays->count(), 2)],
            'hours_per' => ['required'],
        ])->validateWithBag('addTargetHour');

        $targetHour['start_date'] = app(DateFormatter::class)
            ->dateStrToDate($targetHour['start_date']);
        $user->targetHours()->create($targetHour);
    }
}
