<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MultipleSelect extends Component
{
    public $options = [];

    public $selected = [];

    public $trackBy;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($options, $selected = [], $trackBy = 'id', $label = 'name')
    {
        $this->options = $options;
        $this->selected = $selected;
        $this->trackBy = $trackBy;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.multiple-select');
    }
}
