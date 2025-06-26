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
                    $module = $request->module;

                    $q->where(function ($subQ) use ($module) {
                        if ($module === 'Workboard') {
                            $subQ->where('module', 'like', '%Workboard%');
                        } elseif ($module === 'Deposit Log') {
                            $subQ->where('module', 'like', '%payment%')->where('module', 'not like', '%Workboard%');
                        } elseif ($module === 'Withdrawal Log') {
                            $subQ->where('module', 'like', '%payout%')->where('module', 'not like', '%Workboard%');
                        } elseif ($module === 'Account Management') {
                            $subQ->where(function ($q2) {
                                $q2->where('module', 'like', '%EWalletAccount%')
                                    ->orWhere('module', 'like', '%gateway%');
                            })->where('module', 'not like', '%Workboard%');
                        }
                    });
                })
                ->when($request->date, function ($q) use ($request) {
                    $q->whereDate('created_at', $request->date);
                })
                ->orderBy('id','DESC')
                ->paginate(20);

            $users = \App\Models\Admin::select('id', 'name')->orderBy('name')->get();
            $pageTitle = __('transaction.audit_page');

            return view('admin.audit_logs.index', compact('logs', 'users','pageTitle'));
        }



}
