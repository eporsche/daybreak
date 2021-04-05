<?php

namespace App\Traits;

use App\Models\Account;

trait HasAccounts
{
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function ownedAccount()
    {
        return $this->hasOne(Account::class, 'owned_by');
    }

    public function ownsAccount(Account $account)
    {
        return $this->id == $account->owned_by;
    }

    /**
     * Switch the user's context to the given account.
     *
     * @return bool
     */
    public function switchAccount(Account $account)
    {
        if (! $this->belongsToAccount($account)) {
            return false;
        }

        $this->forceFill([
            'account_id' => $account->id,
        ])->save();

        $this->setRelation('account', $account);

        return true;
    }

    /**
     * Determine if the user belongs to the given account.
     *
     * @param  mixed  $account
     * @return bool
     */
    public function belongsToAccount($account)
    {
        return $this->locations->contains(function ($l) use ($account) {
            return $l->id === $account->id;
        }) || $this->ownsAccount($account);
    }


}
