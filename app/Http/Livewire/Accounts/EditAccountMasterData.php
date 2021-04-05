<?php

namespace App\Http\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class EditAccountMasterData extends Component
{
    /**
     * The account instance
     *
     * @var Account
     */
    public $account;

    /**
     * The update Account form
     *
     * @var array
     */
    public $updateAccountForm = [
        'name' => ''
    ];

    public function mount($account)
    {
        $this->account = $account;

        $this->updateAccountForm['name'] = $account->name;
    }

    public function updateAccountSettings()
    {
        $this->resetErrorBag();

        Validator::make([
            'name' => $this->updateAccountForm['name'],
        ], [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateAccount');

        $this->account->forceFill([
            'name' => $this->updateAccountForm['name']
        ])->save();

        $this->emit('updated');
    }

    public function render()
    {
        return view('livewire.accounts.edit-account-master-data');
    }
}
