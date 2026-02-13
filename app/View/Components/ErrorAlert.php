<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ErrorAlert extends Component
{
    public $message;

    /**
     * Create a new component instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.error-alert');
    }
}
