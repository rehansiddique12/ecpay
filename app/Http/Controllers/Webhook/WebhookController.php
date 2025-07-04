<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Search Adjustment History";
        return view('Webhook.webhook', compact('pageTitle'));
    }
}
