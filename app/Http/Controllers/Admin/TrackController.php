<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TelegramImageSession;

class TrackController extends Controller
{
    public function ocrimages(){
        $records = TelegramImageSession::orderBy('id', 'desc')->paginate(config('basic.paginate'));
        $pageTitle = "OCR Track";
        return view('admin.track.index', compact('records', 'pageTitle'));
    }
}
