<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditController extends Controller
{

        public function index(Request $request)
        {
            $logs = AuditLog::with('user')
                ->when($request->user_id, function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                })
                ->when($request->module, function ($q) use ($request) {
                    $q->where('module', 'like', '%' . $request->module . '%');
                })
                ->when($request->date, function ($q) use ($request) {
                    $q->whereDate('created_at', $request->date);
                })
                ->orderBy('id','DESC')
                ->paginate(20);

            $users = \App\Models\Admin::select('id', 'name')->orderBy('name')->get();
            $pageTitle = "Audit Page";

            return view('admin.audit_logs.index', compact('logs', 'users','pageTitle'));
        }



}
