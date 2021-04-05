<?php

namespace App\Actions;

use App\Contracts\DeletesAccounts;

class DeleteAccount implements DeletesAccounts
{
    /**
     * Delete the given account.
     *
     * @param  mixed  $account
     * @return void
     */
    public function delete($account)
    {
        $account->purge();
    }
}
