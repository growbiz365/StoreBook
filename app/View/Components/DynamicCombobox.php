<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DynamicCombobox extends Component
{
    public $label;
    public $id;
    public $placeholder;
    public $fetchUrl;
    public $defaultValue;

    public function __construct($label, $id, $placeholder = 'Search...', $fetchUrl, $defaultValue = null)
    {
        $this->label = $label;
        $this->id = $id;
        $this->placeholder = $placeholder;
        $this->fetchUrl = $fetchUrl;
        $this->defaultValue = $defaultValue; // Add defaultValue
    }

    public function render()
    {
        return view('components.dynamic-combobox');
    }
}
