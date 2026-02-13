<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavigationLink extends Component
{
    public $href;
    public $icon;
    public $icon2;
    public $active;
    public $topliclass;

    /**
     * Create a new component instance.
     *
     * @param string $href
     * @param string $icon
     */
    public function __construct($href, $icon, $icon2, $topliclass)
    {
        $this->href = $href;
        $this->topliclass = $topliclass;
        $this->icon = $icon;
        $this->icon2 = $icon2;
        $this->active = request()->url() === $href; // Determine active state
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.navigation-link');
    }
}
