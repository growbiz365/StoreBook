<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $href;

    /**
     * Create a new component instance.
     */
    public function __construct($href)
    {
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.button');
    }
}
