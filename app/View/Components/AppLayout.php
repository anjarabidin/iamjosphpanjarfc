<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $title;

    public function __construct($title = null)
    {
        $this->title = $title;
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
