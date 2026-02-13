<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchForm extends Component
{
    public $action;
    public $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct($action, $placeholder = 'Search...')
    {
        $this->action = $action;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.search-form');
    }
}
