<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Iframe4Layout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = 'ECPay System') // Default to 'ECPay System' if no title is provided
    {
        $this->title = $title;
    }
    public function render()
    {
        return view('partner.layouts.iframe4');
    }
}
