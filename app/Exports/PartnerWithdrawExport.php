<?php

namespace App\Exports;

use App\Models\Payout;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PartnerWithdrawExport implements FromView
{
    protected $from_date, $to_date, $partner_transection_id, $account_no, $gateway, $status, $domain;

    public function __construct($from_date, $to_date, $partner_transection_id, $account_no, $gateway, $status, $domain)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->partner_transection_id = $partner_transection_id;
        $this->account_no = $account_no;
        $this->gateway = $gateway;
        $this->status = $status;
        $this->domain = $domain;
    }

    public function view(): View
    {

        $records = Payout::with('api');

        if ($this->from_date) {
            $records->whereDate('created_at', '>=', $this->from_date);
        }
        if ($this->to_date) {
            $records->whereDate('created_at', '<=', $this->to_date);
        }
        if ($this->partner_transection_id) {
            $records->where('partner_transection_id', $this->partner_transection_id);
        }
        if ($this->account_no) {
            $records->where('user_account_no', 'like', '%' . $this->account_no . '%');
        }
        if ($this->gateway) {
            $records->where('e_wallet_name', $this->gateway);
        }
        if (!is_null($this->status)) {
            $records->where('transfer_status', $this->status);
        }
        if ($this->domain) {
            $records->where('api_id', 'like', '%' . $this->domain . '%');
        }

        $recordss =$records->orderByDesc('id')->get();

        // dd($recordss);



        return view('admin.payout.withdrawl_export', [
            'records' => $recordss,
        ]);
    }
}

