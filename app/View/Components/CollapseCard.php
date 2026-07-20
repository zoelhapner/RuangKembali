<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CollapseCard extends Component
{
    public $title;
    public $target;
    public $sticky;

    public function __construct(
        $title = '',
        $target = '',
        $sticky = false
    ) {
        $this->title = $title;
        $this->target = $target;
        $this->sticky = $sticky;
    }

    public function render()
    {
        return view('components.collapse-card');
    }
}