<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public $title;

    public function __construct($title = '')
    {
        $this->title = $title;
    }

    public function render(): View
    {
        return view('layouts.guest');
    }
}
