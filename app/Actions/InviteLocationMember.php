<?php

namespace App\Actions;

use App\Mail\LocationInvitation;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Contracts\InvitesLocationMembers;
use Illuminate\Support\Facades\Validator;

class InviteLocationMember implements InvitesLocationMembers
{
    public function invite($user, $location, string $email, string $role = null)
    {
        Gate::forUser($user)->authorize('addLocationMember', $location);

        $this->validate($location, $email, $role);

        $invitation = $location->locationInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new LocationInvitation($invitation));
    }

    /**
     * Validate the invite member operation.
     *
     * @param  mixed  $location
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
            'email.unique' => __('This user has already been invited.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnLocation($location, $email)
        )->validateWithBag('addLocationMember');
    }

    /**
     * Get the validation rules for inviting a location member.
     *
     * @return array
     */
    protected function rules()
    {
        return array_filter([
            'email' => ['required', 'email', 'unique:location_invitations'],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the location.
     *
     * @param  mixed  $location
     * @param  string  $email
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyOnLocation($location, string $email)
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
