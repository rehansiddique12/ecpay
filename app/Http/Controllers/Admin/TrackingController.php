<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CsTracker;

class TrackingController extends Controller
{
    public function index()
    {
        $trackers = CsTracker::with('user')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
                        $pageTitle = __('accounts.all_accounts');
                        $users = \App\Models\Admin::all();

        return view('admin.tracking.index', compact('trackers','pageTitle','users'));
    }

    /**
     * Filter tracking records
     */
    public function filter(Request $request)
    {
        $query = CsTracker::with('user');
        $users = \App\Models\Admin::all();

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by action if provided
        if ($request->has('action') && $request->action) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        $trackers = $query->orderBy('created_at', 'desc')->paginate(15);
        $pageTitle = __('accounts.all_accounts');

        return view('admin.tracking.index', compact('trackers','pageTitle','users'));
    }
}
