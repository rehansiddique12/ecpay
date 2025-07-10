<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Search Adjustment History";
        return view('Webhook.webhook', compact('pageTitle'));
    }

    public function webhook(Request $request, $source, $acc, $type){
        $string = file_get_contents('php://input');
        LaravelLog::info('new-sms-webhook Source:'.$source.' Acc:'.$acc.' Type:'.$type.' Message:'.$string);

    }
}
