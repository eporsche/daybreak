<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\TargetHour;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetHourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TargetHour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "start_date" =>  Carbon::today(),
            "hours_per" => "week",
            "target_hours" => 40,
            "target_limited" => false,
            "is_mon" => true,
            "mon" => 8,
            "is_tue" => true,
            "tue" => 8,
            "is_wed" => true,
            "wed" => 8,
            "is_thu" => true,
            "thu" => 8,
            "is_fri" => true,
            "fri" => 8,
            "is_sat" => false,
            "sat" => 0,
            "is_sun" => false,
            "sun" => 0
        ];
    }
}
