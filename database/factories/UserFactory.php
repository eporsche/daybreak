<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use LogicException;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'date_of_employment' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10)
        ];
    }

    /**
     * Indicate that the user should be owner of a location.
     *
     * @return $this
     */
    public function withOwnedLocation()
    {
        return $this->has(
            Location::factory()
                ->state(
                    function (array $attributes, User $user) {
                        if (is_null($user->ownedAccount)) {
                            throw new \LogicException("withOwnedAccount should be called before withOwnedLocation");
                        }

                        return [
                            'name' => $user->name.'\'s Location',
                            'owned_by' => $user->id,
                            'account_id' =>  $user->ownedAccount->id
                        ];
                    }),
            'ownedLocations'
        );
    }

    /**
     * Indicate that the user should have own an account.
     *
     * @return $this
     */
    public function withOwnedAccount()
    {
        return $this->has(
            Account::factory()
                ->state(function (array $attributes, User $user) {
                    return [
                        'name' => $user->name.'\'s Account',
                        'owned_by' => $user->id,
                    ];
                }),
            'ownedAccount'
        );
    }
}
