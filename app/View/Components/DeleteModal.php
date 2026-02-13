<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DeleteModal extends Component
{
    public $title;
    public $message;
    public $actionUrl;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $message, $actionUrl)
    {
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.delete-modal');
    }
}
