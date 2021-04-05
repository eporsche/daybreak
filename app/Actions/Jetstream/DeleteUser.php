<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use App\Contracts\DeletesAccounts;
use Illuminate\Support\Facades\DB;
use App\Contracts\DeletesLocations;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * The account deleter implementation.
     *
     * @var \App\Contracts\DeletesAccounts
     */
    protected $deletesAccounts;

    /**
     * The location deleter implementation.
     *
     * @var \App\Contracts\DeletesLocations
     */
    protected $deletesLocations;

    /**
     * Create a new action instance.
     *
     * @param  \App\Contracts\DeletesAccounts  $deletesAccounts
     * @param  \App\Contracts\DeletesLocations  $deletesLocations
     * @return void
     */
    public function __construct(DeletesAccounts $deletesAccounts, DeletesLocations $deletesLocations)
    {
        $this->deletesAccounts = $deletesAccounts;
        $this->deletesLocations = $deletesLocations;
    }

    /**
     * Delete the given user.
     *
     * @param  User  $user
     * @return void
     */
    public function delete($user)
    {
        DB::transaction(function () use ($user) {
            $this->deleteLocations($user);
            $this->deleteAccounts($user);

            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
        });
    }

    /**
     * Delete the accounts and account associations attached to the user.
     *
     * @param  mixed  $user
     * @return void
     */
    protected function deleteAccounts($user)
    {
        if ($user->ownedAccount) {
            $this->deletesAccounts->delete($user->ownedAccount);
        }
    }

    /**
     * Delete the teams and team associations attached to the user.
     *
     * @param  mixed  $user
     * @return void
     */
    protected function deleteLocations($user)
    {
        $user->locations()->detach();

        $user->ownedLocations->each(function ($location) {
            $this->deletesLocations->delete($location);
        });
    }
}
