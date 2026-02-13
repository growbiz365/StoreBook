<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $breadcrumbs;

    /**
     * Create a new component instance.
     *
     * @param array $breadcrumbs
     */
    public function __construct($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.breadcrumb');
    }
}
