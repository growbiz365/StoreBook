<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Combobox extends Component
{
    public $label;
    public $id;
    public $items;
    public $placeholder;

    public function __construct($label, $id, $items = [], $placeholder = 'Search...')
    {
        $this->label = $label;
        $this->id = $id;
        $this->items = $items;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.combobox');
    }
}
