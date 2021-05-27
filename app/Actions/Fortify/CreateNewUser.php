<?php

namespace App\Actions\Fortify;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Duration;
use App\Models\Location;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\Rules\Password;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                 Password::min(8)->letters()->mixedCase()->numbers()->uncompromised(2),
            ],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'date_of_employment' => Carbon::today()
            ]), function (User $user) {
                /*
                |--------------------------------------------------------------------------
                | Set up a new user environment.
                |--------------------------------------------------------------------------
                */

                //create new account for user
                $this->createAccount($user);

                //create default target hours for user
                $this->createDefaultTargetHours($user);

                //create new location for user
                $location = $this->createLocation($user);

                //create and assign default absent types
                $this->createDefaultAbsentTypes($user, $location);

                //create default resting times for user
                $this->createDefaultRestingTime($user, $location);
            });
        });
    }

    public function createDefaultRestingTime($user, $location)
    {
        $defaultRestingTimes = $location->defaultRestingTimes()->createMany([
            [
                'min_hours' => new Duration(21600), //6*60*60
                'duration' => new Duration(1800) //30*60
            ], [
                'min_hours' => new Duration(39600), //11*60*60
                'duration' => new Duration(2700) //45*60
            ]
        ]);

        $user->defaultRestingTimes()->sync($defaultRestingTimes);
    }

    /**
     * Create the default Location.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function createAccount(User $user)
    {
        $account = Account::forceCreate([
            'owned_by' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Account",
        ]);

        $user->ownedAccount()->save($account);

        $user->account()->associate($account)->save();
    }

    /**
     * Create the default Location.
     *
     * @param \App\Models\User $user
     * @return Location
     */
    public function createLocation(User $user)
    {
        $location = new Location([
            'owned_by' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Location",
            'locale' => config('app.locale'),
            'time_zone' => config('app.timezone')
        ]);

        //associate location to user account
        $user->ownedAccount->locations()->save($location);

        //associate new user to current location
        $user->switchLocation($location);

        return $location;
    }

    protected function createDefaultAbsentTypes(User $user, Location $location)
    {
        $newAbsentTypes = $location->absentTypes()->createMany([
            [
                'title' => 'Krankheit',
                'affect_vacation_times' => false,
                'affect_evaluations' => true,
                'evaluation_calculation_setting' => 'absent_to_target'
            ],
            [
                'title' => 'Urlaub',
                'affect_vacation_times' => true,
                'affect_evaluations' => true,
                'evaluation_calculation_setting' => 'absent_to_target'
            ],
            [
                'title' => 'Überstundenabbau',
                'affect_evaluations' => false,
                'affect_vacation_times' => false
            ],
            [
                'title' => 'Wunschfrei',
                'affect_evaluations' => false,
                'affect_vacation_times' => false
            ]
        ]);

        $user->absenceTypes()->sync($newAbsentTypes);
    }

    public function createDefaultTargetHours($user)
    {
        $user->targetHours()->create([
            "start_date" => Carbon::today(),
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
        ]);
    }
}
