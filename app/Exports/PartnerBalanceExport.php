<?php

namespace App\Exports;

use App\Models\ApiTransaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PartnerBalanceExport implements FromView
{
    protected $from_date, $to_date, $partner, $adjustment, $search_by_name;

    public function __construct($from_date=null, $to_date=null, $partner=null, $search_by_name=null, $adjustment=null)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->partner = $partner;
        $this->search_by_name = $search_by_name;
        $this->adjustment = $adjustment;
    }

    public function view(): View
    {
        $records = ApiTransaction::with('api');

        if (!empty($this->from_date) && !empty($this->to_date)) {
            $records->whereDate('created_at', '>=', $this->from_date);
            $records->whereDate('created_at', '<=', $this->to_date);
        } elseif (!empty($this->from_date)) {
            $records->whereDate('created_at', '>=', $this->from_date);
        } elseif (!empty($this->to_date)) {
            $records->whereDate('created_at', '<=', $this->to_date);
        }

        if (!empty($this->partner)) {
            $records->where('partner_id', $this->partner);
        }

        if (!is_null($this->adjustment)) {
            $records->where('adjustment', $this->adjustment);
        }

        if (!empty($this->search_by_name)) {
            $searchTerm = $this->search_by_name;
            $records->whereHas('api', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('website', 'like', '%' . $searchTerm . '%');
            });
        }

        $records = $records->orderBy('id', 'DESC')->get();

        return view('admin.payout.partner_balace_export', [
            'records' => $records
        ]);
    }
}

