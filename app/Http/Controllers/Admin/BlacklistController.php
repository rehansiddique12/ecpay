<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Http\Request;
use App\Models\Blacklist;
use App\Models\BlacklistRemoval;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class BlacklistController extends Controller
{
    // List and search blacklisted member_ids
    public function index(Request $request)
    {
        $query = Blacklist::query();
        if ($request->filled('member_id')) {
            $query->where('member_id', 'like', '%' . $request->member_id . '%');
        }
        $blacklists = $query->where('status',1)->orderBy('created_at', 'desc')->paginate(20);
        $pageTitle = 'Black List';
        $merchants = Api::get();
        // dd($members);
        return view('admin.blacklist.index', compact('blacklists','pageTitle','merchants'));
    }

    // Remove a member_id from blacklist
    public function destroy($id)
    {
        $blacklist = Blacklist::findOrFail($id);
        $blacklist->status = 0;
        $blacklist->removed_at = now();
        $blacklist->save();
        // $member_id = $blacklist->member_id;

        // Check if removal record already exists
        // $blacklist_removal = BlacklistRemoval::where('member_id', $member_id)->first();

        // Delete the blacklist record
        // $blacklist->delete();

        // if ($blacklist_removal) {
            // If record exists, update the timestamp
            // $blacklist_removal->touch(); // updates only the updated_at field
        // } else {
            // Create new removal log
        //     BlacklistRemoval::create([
        //         'member_id' => $member_id,
        //         'removed_at' => now(),
        //         'admin_id' => Auth::id(),
        //         'reason' => 'Removed by admin',
        //         'api_id' => $blacklist->api_id,
        //     ]);
        // }

        return redirect()->route('admin.blacklist.index')->with('success', 'Blacklist entry removed and counters reset.');
    }


    public function store(Request $request)
    {
    //  dd($request->All());
        $request->validate([
            'api_id' => 'required|exists:apis,id',
            'member_id' => 'required',
            'reason' => 'nullable',
            'type' => 'nullable|in:consecutive,total', // validate type if passed
        ]);

        $blacklist = \App\Models\Blacklist::where('member_id', $request->member_id)
            ->where('api_id', $request->api_id)
            ->first();

        if ($blacklist) {
            if ($request->type === 'consecutive') {
                $blacklist->consecutive_count += 1;
            } else {
                $blacklist->total_count += 1;
            }

            // Update reason only if it's empty
            if (!$blacklist->reason && $request->reason) {
                $blacklist->reason = $request->reason;
            }

            $blacklist->status = 1;
            $blacklist->save();
        } else {
            // Create new blacklist record
            \App\Models\Blacklist::create([
                'api_id' => $request->api_id,
                'member_id' => $request->member_id,
                'reason' => $request->reason,
                'status' => 1,
                'admin_id' => Auth::id(),
                'consecutive_count' => $request->type === 'consecutive' ? 1 : 0,
                'total_count' => $request->type === 'total' ? 1 : 0,
            ]);
        }

        return back()->with('success', 'Member successfully blacklisted.');
    }



}
