<?php
namespace App\Exports;

use App\Models\Api;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartnerMerchantExportName implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $from_date;
    protected $to_date;
    protected $userID;
    protected $merchant;
    public function __construct($from_date , $to_date,  $userID , $merchant)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->userID = $userID;
        $this->merchant = $merchant;
    }

    public function collection()
    {

        // Get the total summary (Optional)
        $totalSummary = DB::table('partner_commissions')
            ->select(
                'api_id',
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
                DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_deposit_commission'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
                DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_withdrawal_commission'),
                DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
                DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
                DB::raw('SUM(charges) as total_commission')
            )
            ->where('status', 1)
            ->where('api_id', $this->merchant)
            ->where('from_id', $this->userID)
            ->whereDate('created_at', '>=', $this->from_date)
            ->whereDate('created_at', '<=', $this->to_date)
            ->groupBy('api_id')
            ->first();

        // Get the detailed report
        $results = DB::table('partner_commissions')
            ->select(
                'api_id',
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
                DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
                DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
                DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
                DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
                DB::raw('SUM(charges) as total_commission')
            )
            ->where('status', 1)
            ->where('api_id', $this->merchant)
            ->where('from_id', $this->userID)
            ->whereDate('created_at', '>=', $this->from_date)
            ->whereDate('created_at', '<=', $this->to_date)
            ->groupBy('api_id', DB::raw('DATE(created_at)'))
            ->get();

        // Prepare the final collection to export
        $data = $results->map(function ($row) use ($totalSummary) {

            $partner_ids = PartnerCommission::where('from_id', $this->userID)
            ->distinct()
            ->pluck('api_id')
            ->toArray();
            

            // If no partner IDs are found, set an empty collection
            if (empty($partner_ids)) {
                
                $apis = collect();
            } else {

                    $apis = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                    ->pluck('name', 'id')->all();
            }


            // $apis = DB::table('apis')->where('type', 'Admin')
            // ->where('parent_id', $this->userID)->pluck('name', 'id')->toArray();
            return [
                'Merchant Name' =>  $apis[$row->api_id] ?? 'Unknown',
                'date' => $row->date,
                'total_deposit_transactions' => number_format($row->total_deposit_transactions, 0),
                'total_deposit' => number_format($row->total_deposit, 2, '.', ','),
                'total_charges_deposit' => number_format($row->total_charges_deposit, 2, '.', ','),
                'total_withdrawal_transactions' => number_format($row->total_withdrawal_transactions, 0),
                'total_withdrawal' => number_format($row->total_withdrawal, 2, '.', ','),
                'total_charges_withdrawal' => number_format($row->total_charges_withdrawal, 2, '.', ','),
                'total_commission' => number_format($row->total_commission, 2, '.', ','),
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'Merchant Name',
            'Date',
            'Total Deposit Transactions',
            'Total Deposit',
            'Total Charges Deposit',
            'Total Withdrawal Transactions',
            'Total Withdrawal',
            'Total Charges Withdrawal',
            'Total Commission',
        ];
    }
}

