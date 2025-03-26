<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component
{

  public $title;  // Declare the title variable

    // Accept the title as a parameter
    public function __construct($title = 'ECPay System') // Default to 'ECPay System' if no title is provided
    {
        $this->title = $title;
    }
    public function render()
    {
        return view('admin.layouts.app');
    }
}
