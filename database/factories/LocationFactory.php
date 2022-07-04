<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company,
            'owned_by' => User::factory(),
            'account_id' => Account::factory(),
            'time_zone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'active' => true
        ];
    }
}
