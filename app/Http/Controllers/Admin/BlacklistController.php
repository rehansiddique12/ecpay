<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blacklist;
use App\Models\BlacklistRemoval;
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
        $blacklists = $query->orderBy('created_at', 'desc')->paginate(20);
        $pageTitle = 'Black List';
        return view('admin.blacklist.index', compact('blacklists','pageTitle'));
    }

    // Remove a member_id from blacklist
    public function destroy($id)
    {
        $blacklist = Blacklist::findOrFail($id);
        $member_id = $blacklist->member_id;

        // Check if removal record already exists
        $blacklist_removal = BlacklistRemoval::where('member_id', $member_id)->first();

        // Delete the blacklist record
        $blacklist->delete();

        if ($blacklist_removal) {
            // If record exists, update the timestamp
            $blacklist_removal->touch(); // updates only the updated_at field
        } else {
            // Create new removal log
            BlacklistRemoval::create([
                'member_id' => $member_id,
                'removed_at' => now(),
                'admin_id' => Auth::id(),
                'reason' => 'Removed by admin',
            ]);
        }

        return redirect()->route('admin.blacklist.index')->with('success', 'Blacklist entry removed and counters reset.');
    }

}
