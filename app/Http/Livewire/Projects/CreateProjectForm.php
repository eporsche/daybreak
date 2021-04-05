<?php

namespace App\Http\Livewire\Projects;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Project;
use Livewire\Component;
use App\Contracts\SendsBilling;
use App\Formatter\DateFormatter;
use Illuminate\Support\Facades\Auth;
use App\Contracts\GeneratesBillingTable;
use Illuminate\Support\Facades\Validator;

class CreateProjectForm extends Component
{

    /**
     * Holds the account instance
     */
    public $account;

    public $addProjectForm = [
        'name' => '',
        'customer_order_number' => '',
        'time_budget' => 0,
        'hourly_rate' => 0,
        'billing_description' => '',
        'billing_type' => ''
    ];

    public $projectIdToBeBilled = null;

    public $confirmingSendProjectBilling = false;

    public $billingFrom = null;

    public $billingTo = null;

    public $hideDetails = true;

    protected $billingTable = null;

    /**
     * Holds the open / closed state of the add project modal window
     */
    public $addProject = false;

    public function mount(Account $account, DateFormatter $dateFormatter)
    {
        $this->account = $account;
        $this->billingFrom = $dateFormatter->formatDateForView(
            new Carbon('first day of last month')
        );

        $this->billingTo = $dateFormatter->formatDateForView(
            new Carbon('last day of last month')
        );
    }

    public function addProject()
    {
        $this->addProject = true;
    }

    public function confirmBillingProject($projectIdToBeBilled)
    {
        $this->projectIdToBeBilled = $projectIdToBeBilled;

        $this->confirmingSendProjectBilling = true;

        $this->generateBillingTable(
            app(GeneratesBillingTable::class)
        );
    }

    public function sendBillingProject(SendsBilling $sender)
    {
        $this->resetErrorBag();

        $sender->send(
            $this->account,
            $this->projectIdToBeBilled,
            $this->billingFrom,
            $this->billingTo
        );

        $this->projectIdToBeBilled = null;

        $this->confirmingSendProjectBilling = false;
    }

    public function generateBillingTable(GeneratesBillingTable $generator)
    {
        $this->resetErrorBag();

        $this->billingTable = $generator->filter(
            $this->account,
            $this->projectIdToBeBilled,
            $this->billingFrom,
            $this->billingTo
        );
    }

    public function confirmAddProject()
    {
        $this->resetErrorBag();

        Validator::make($this->addProjectForm, [
            'name' => ['required', 'string', 'max:255'],
            'customer_order_number' => ['nullable', 'string', 'max:255'],
            'time_budget' => ['required', 'numeric'],
            'hourly_rate' => ['required', 'numeric'],
            'billing_description' => ['nullable', 'string'],
            'billing_type' => ['nullable', 'string']
        ])->validateWithBag('createProject');

        (new Project())->forceFill(
            array_merge([
                'account_id' => $this->account->id,
                'owned_by' => Auth::user()->id
            ], $this->addProjectForm)
        )->save();

        $this->reset(['addProjectForm']);

        $this->account = $this->account->fresh();

        $this->emit('saved');

        $this->addProject = false;
    }

    public function cancelAddProject()
    {
        $this->addProject = false;
    }

    public function render()
    {
        return view('projects.create-project-form',[
            'billingTable' => $this->billingTable
        ]);
    }
}
