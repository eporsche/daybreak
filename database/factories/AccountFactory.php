<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owned_by' => User::factory(),
            'name' => $this->faker->unique()->company,
        ];
    }
}
