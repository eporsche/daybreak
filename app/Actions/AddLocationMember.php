<?php

namespace App\Actions;

use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;
use App\Contracts\DeletesAccounts;

use Illuminate\Support\Facades\DB;
use App\Contracts\DeletesLocations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Contracts\AddsLocationMembers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddLocationMember implements AddsLocationMembers
{
    public $deletesAccounts;

    public $deletesLocations;

    public function __construct(DeletesAccounts $deletesAccounts, DeletesLocations $deletesLocations)
    {
        $this->deletesAccounts = $deletesAccounts;
        $this->deletesLocations = $deletesLocations;
    }
    /**
     * Add a new team member to the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function add($user, $location, string $email, string $role = null)
    {
        Gate::forUser($user)->authorize('addLocationMember', $location);

        $this->validate($location, $email, $role);

        $this->ensureAuthenticatedUserHasBeenInvited($email);

        $newLocationMember = Jetstream::findUserByEmailOrFail($email);

        DB::transaction(function () use ($newLocationMember, $location, $role) {

            // we need to purge the users account / locations once he enters an invitation
            $this->deletesAccounts->delete($newLocationMember->ownedAccount);

            $newLocationMember->ownedLocations->each(function ($location) {
                $this->deletesLocations->delete($location);
            });

            // assign location and role to user
            $location->users()->attach(
                $newLocationMember,
                ['role' => $role]
            );

            // update current location for new member
            $newLocationMember->switchLocation($location);

            // update current account for new member
            $newLocationMember->switchAccount($location->account);

            // assign existing absence types to user
            $newLocationMember->absenceTypes()->sync(
                $location->absentTypesToBeAssignedToNewUsers
            );
        });
    }

    protected function ensureAuthenticatedUserHasBeenInvited(string $email)
    {
        if (!Auth::user()->hasEmail($email)) {
            throw ValidationException::withMessages([
                'email' => [__('Sorry, you were not invited.')],
            ])->errorBag('addLocationMember');
        }
    }

    /**
     * Validate the add member operation.
     *
     * @param  mixed  $team
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    protected function validate($location, string $email, ?string $role)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyInLocation($location, $email)
        )->validateWithBag('addLocationMember');
    }

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'email' => ['required', 'email', 'exists:users'],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ];
    }

    /**
     * Ensure that the user is not already on the location.
     *
     * @param  mixed  $location
     * @param  string  $email
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyInLocation($location, string $email)
    {
        return function ($validator) use ($location, $email) {
            $validator->errors()->addIf(
                $location->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the location.')
            );
        };
    }
}
