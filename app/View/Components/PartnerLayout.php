<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PartnerLayout extends Component
{
    public $title;
    public function __construct($title = 'ECPay System')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('partner.layouts.app');
    }
}
