<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuestCombobox extends Component
{
    public $label;
    public $id;
    public $placeholder;
    public $fetchUrl;
    public $defaultValue;

    public function __construct($label, $id, $placeholder = 'Search Guest by Name or CNIC...', $fetchUrl, $defaultValue = null)
    {
        $this->label = $label;
        $this->id = $id;
        $this->placeholder = $placeholder;
        $this->fetchUrl = $fetchUrl;
        $this->defaultValue = $defaultValue;
    }

    public function render()
    {
        return view('components.guest-combobox');
    }
}
