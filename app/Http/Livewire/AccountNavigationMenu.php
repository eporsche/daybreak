<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AccountNavigationMenu extends Component
{

    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'refresh-navigation-dropdown' => '$refresh',
    ];

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('account-navigation-menu');
    }
}
