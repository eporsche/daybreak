<?php
namespace App\Actions;

use Illuminate\Support\Facades\Gate;
use App\Contracts\RemovesLocationMembers;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class RemoveLocationMember implements RemovesLocationMembers
{
    /**
     * Remove the team member from the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  mixed  $locationMember
     * @return void
     */
    public function remove($user, $location, $locationMember)
    {
        $this->authorize($user, $location, $locationMember);

        $this->ensureUserDoesNotOwnLocation($locationMember, $location);

        $location->removeUser($locationMember);
    }

    /**
     * Authorize that the user can remove the team member.
     *
     * @param  mixed  $user
     * @param  mixed  $location
     * @param  mixed  $locationMember
     * @return void
     */
    protected function authorize($user, $location, $locationMember)
    {
        if (! Gate::forUser($user)->check('removeLocationMember', $location)
            && $user->id !== $locationMember->id
        ) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     *
     * @param  mixed  $locationMember
     * @param  mixed  $location
     * @return void
     */
    protected function ensureUserDoesNotOwnLocation($locationMember, $location)
    {
        if ($locationMember->id === $location->owner->id) {
            throw ValidationException::withMessages([
                'location' => [__('You may not leave a location that you created.')],
            ])->errorBag('removeLocationMember');
        }
    }
}
