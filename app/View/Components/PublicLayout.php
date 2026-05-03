<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PublicLayout extends Component
{
    public $journal;

    public function __construct($journal = null)
    {
        $this->journal = $journal;
    }

    public function render(): View
    {
        return view('layouts.public');
    }
}
