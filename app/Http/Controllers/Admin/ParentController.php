<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
// use App\Models\Fund;
// use App\Models\Payout;
// use App\Models\Payment;
// use App\Models\PayoutLog;
use Illuminate\Http\Request;
use App\Models\ParentGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class ParentController extends Controller
{
    // const TELEGRAM_BOT_TOKEN = '7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4';
    // const PENDING_MESSAGE = 'Your request has been sent and is in a pending state. Please contact the administrator!';
    // const TRANSACTION_STATUS_MESSAGES = [
    //     1 => 'The transaction has been completed and callback sent.',
    //     3 => 'The transaction has been rejected and callback sent.',
    //     'Complete' => 'The transaction has been completed and callback sent.',
    //     'Reject' => 'The transaction has been rejected and callback sent.',
    // ];

    public function parant(Request $request)
    {
        $records = ParentGroup::paginate(20);
        $pageTitle = $title = "Manage Parant Groups";

        return view('admin.parant.parant', compact('records', 'pageTitle', 'title'));
    }


}
