<?php
namespace App\Exports;

use App\Models\Api;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartnerMerchantExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $from_date;
    protected $userID;
    public function __construct($from_date , $userID)
    {
        $this->from_date = $from_date;
        $this->userID = $userID;
    }

    public function collection()
    {
        // Calculate the total commission for all rows
        $totalCommissionAll = DB::table('partner_commissions')
            ->where('status', 1)
            ->where('from_id' ,  $this->userID)
            ->whereDate('created_at', $this->from_date)
            ->sum('charges');

        // $apiIds = Api::where('type', 'Admin')
        // ->where('parent_id', $this->userID)
        // ->pluck('id')
        // ->toArray();


        $partner_ids = PartnerCommission::where('from_id', $this->userID)
            ->distinct()
            ->pluck('api_id')
            ->toArray();
            

        // If no partner IDs are found, set an empty collection
        if (empty($partner_ids)) {
            $apiIds = collect();
        } else {
            $apiIds = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->pluck('id')
                ->toArray();
        }


        // Fetch the detailed data grouped by `api_id`
        $data = DB::table('partner_commissions')
            ->select(
                'api_id',
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
                DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
                DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
                DB::raw('COUNT(CASE WHEN type = 1 AND amount > 0 THEN 1 END) as total_deposit_transactions'),
                DB::raw('COUNT(CASE WHEN type = 2 AND amount > 0 THEN 1 END) as total_withdrawal_transactions'),
                DB::raw('SUM(charges) as total_commission')
            )
            ->where('status', 1)
            ->where('from_id' , $this->userID)
            ->whereIn('api_id', $apiIds)
            ->whereDate('created_at', $this->from_date)
            ->groupBy('api_id')
            ->get()
            ->map(function ($row) {
                $apis = DB::table('apis')->pluck('name', 'id')->toArray();
                return [
                    'Merchant Name'               => $apis[$row->api_id] ?? 'Unknown',
                    'No. Transaction (Deposit)'   => number_format($row->total_deposit_transactions, 0),
                    'Total Deposit Amount'        => number_format($row->total_deposit, 2, '.', ','),
                    'Deposit Commission'          => number_format($row->total_charges_deposit, 2, '.', ','),
                    'No. Transaction (Withdrawal)' => number_format($row->total_withdrawal_transactions, 0),
                    'Total Withdrawal Amount'     => number_format($row->total_withdrawal, 2, '.', ','),
                    'Withdrawal Commission'       => number_format($row->total_charges_withdrawal, 2, '.', ','),
                    'Total Commission'            => number_format($row->total_commission, 2, '.', ','),
                ];
            });

            return collect([
                ['Total Commission', '', '', '', '', '', '', number_format($totalCommissionAll, 2, '.', ',')], // Total Commission row
                ['Merchant Name', 'No. Transaction (Deposit)', 'Total Deposit Amount', 'Deposit Commission', 'No. Transaction (Withdrawal)', 'Total Withdrawal Amount', 'Withdrawal Commission', 'Total Commission'], // Headings row
            ])->merge($data);
    }


    public function headings(): array
    {
        return [
           
        ];
    }

    public function startCell(): string
    {
        return 'A2'; 
    }
}
