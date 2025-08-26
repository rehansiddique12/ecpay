<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\IpWhitelist;
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

    // (verifyed by iftikhar)
    public function whitelist()
    {
        $pageTitle = "IP White List";
        $whitelists = IpWhitelist::with('admin')->latest()->get();
        $admins = Admin::all();
        return view('admin.track.whitelist', compact('pageTitle', 'whitelists', 'admins'));
    }

    // Store new IP (verifyed by iftikhar)
    public function whitelistStore(Request $request)
    {
        $request->validate([
            'ip_address'  => 'required|ip',
            'user_id'     => 'required|exists:admins,id',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        IpWhitelist::create([
            'ip_address'  => $request->ip_address,
            'user_id'     => $request->user_id,
            'description' => $request->description,
            'is_active'   => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'IP added successfully!');
    }

    // Update IP (verifyed by iftikhar)
    public function whitelistUpdate(Request $request, $id)
    {
        $request->validate([
            'ip_address'  => 'required|ip',
            'user_id'     => 'required|exists:admins,id',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $ip = IpWhitelist::findOrFail($id);
        $ip->update([
            'ip_address'  => $request->ip_address,
            'user_id'     => $request->user_id,
            'description' => $request->description,
            'is_active'   => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'IP updated successfully!');
    }

    // Delete IP (verifyed by iftikhar)
    public function whitelistDelete($id)
    {
        $ip = IpWhitelist::findOrFail($id);
        $ip->delete();

        return redirect()->back()->with('success', 'IP deleted successfully!');
    }
}
