<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TextInput extends Component
{
    public $type;
    public $name;
    public $id;
    public $placeholder;
    public $value;
    public $autocomplete;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name, 
        $id = null, 
        $type = 'text', 
        $placeholder = '', 
        $value = '', 
        $autocomplete = 'off'
    ) {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->autocomplete = $autocomplete;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.text-input-two');
    }
}
