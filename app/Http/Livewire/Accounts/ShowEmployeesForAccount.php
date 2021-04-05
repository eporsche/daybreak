<?php

namespace App\Http\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;

class ShowEmployeesForAccount extends Component
{
    /**
     * Holds the account instance
     */
    public $account;

    public function mount(Account $account)
    {
        $this->account = $account;
    }

    public function render()
    {
        return view('livewire.accounts.show-employees-for-account');
    }
}
