<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SettingLink extends Component
{
    public $initials;
    public $url;
    public $title;
    public $subtitle;
    public $bgColor;

    /**
     * Create a new component instance.
     *
     * @param string $initials
     * @param string $url
     * @param string $title
     * @param string $subtitle
     * @param string $bgColor
     */
    public function __construct($initials, $url, $title, $subtitle, $bgColor = 'bg-blue-800')
    {
        $this->initials = $initials;
        $this->url = $url;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->bgColor = $bgColor;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.setting-link');
    }
}
