<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Location;
use App\Models\TimeTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TimeTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location_id' => Location::factory(),
            'user_id' => User::factory(),
            'starts_at' => "2021-05-18 9:00:00",
            'ends_at' => "2021-05-18 17:00:00",
            'description' => "nothing",
        ];
    }
}
