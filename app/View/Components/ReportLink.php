<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReportLink extends Component
{
    public $href;
    public $icon;

    /**
     * Create a new component instance.
     *
     * @param string $href
     * @param string $icon
     */
    public function __construct($href = '#', $icon = '📋')
    {
        $this->href = $href;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.report-link');
    }
}
