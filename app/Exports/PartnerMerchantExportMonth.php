<?php
namespace App\Exports;

use App\Models\Api;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartnerMerchantExportMonth implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $from_date;
    protected $userID;
    protected $merchant;

    public function __construct($from_date , $userID ,$merchant)
    {
        $this->from_date = $from_date;
        $this->userID = $userID;
        $this->merchant = $merchant;
    }

    public function collection()
    {
        // Calculate the total commission for all rows (for the month)
        $totalCommissionAll = DB::table('partner_commissions')
            ->where('status', 1)
            ->where('from_id', $this->userID)
            ->whereYear('created_at', $this->from_date) // Year filter
            ->sum('charges');

        // Get the list of API IDs (merchants)
        // $apiIds = Api::where('type', 'Admin')
        //     ->where('parent_id', $this->userID)
        //     ->pluck('id')
        //     ->toArray();


            $partner_ids = PartnerCommission::where('from_id', $this->userID)
            ->distinct()
            ->pluck('api_id')
            ->toArray();
            

        // If no partner IDs are found, set an empty collection
        if (empty($partner_ids)) {
            $apiIds = collect();
             $apis = collect();
        } else {
            $apiIds = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                ->pluck('id')
                ->toArray();
                 $apis = Api::whereIn('id', $partner_ids) // Filter by partner IDs in the array
                    ->pluck('name', 'id')->all();
        }

        // Start the query for fetching partner commission data
        $query = DB::table('partner_commissions')
            ->select(
                'api_id',
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_deposit'),
                DB::raw('SUM(CASE WHEN type = 1 THEN charges ELSE 0 END) as total_charges_deposit'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_withdrawal'),
                DB::raw('SUM(CASE WHEN type = 2 THEN charges ELSE 0 END) as total_charges_withdrawal'),
                DB::raw('COUNT(CASE WHEN type = 1 THEN 1 END) as total_deposit_transactions'),
                DB::raw('COUNT(CASE WHEN type = 2 THEN 1 END) as total_withdrawal_transactions'),
                DB::raw('SUM(charges) as total_commission')
            )
            ->where('status', 1)
            ->where('from_id', $this->userID)
            ->whereYear('created_at', $this->from_date) // Filter by year
            ->groupBy('api_id', DB::raw('MONTH(created_at)')); // Group by API (Merchant) and month

        // If the merchant is specified (not null), filter by specific api_id
        if ($this->merchant) {
            $query->where('api_id', $this->merchant);
        } else {
            // If merchant is null, use the whereIn clause with $apiIds
            $query->whereIn('api_id', $apiIds);
        }

        // Get the results
        $data = $query->get()->map(function ($row) use ($apis) {
            // Map the API name to the results
            // $apis = DB::table('apis')->pluck('name', 'id')->toArray();
            return [
                'Month' => date('F', mktime(0, 0, 0, $row->month, 10)), // Convert month number to name
                'Merchant Name' => $apis[$row->api_id] ?? 'Unknown',
                'No. Transaction (Deposit)' => number_format($row->total_deposit_transactions, 0),
                'Total Deposit Amount' => number_format($row->total_deposit, 2, '.', ','),
                'Deposit Commission' => number_format($row->total_charges_deposit, 2, '.', ','),
                'No. Transaction (Withdrawal)' => number_format($row->total_withdrawal_transactions, 0),
                'Total Withdrawal Amount' => number_format($row->total_withdrawal, 2, '.', ','),
                'Withdrawal Commission' => number_format($row->total_charges_withdrawal, 2, '.', ','),
                'Total Commission' => number_format($row->total_commission, 2, '.', ','),
            ];
        });

        return collect([
            // Column Headings row
            ['Month', 'Merchant Name', 'No. Transaction (Deposit)', 'Total Deposit Amount', 'Deposit Commission', 'No. Transaction (Withdrawal)', 'Total Withdrawal Amount', 'Withdrawal Commission', 'Total Commission'],
        ])->merge($data);
    }

    public function headings(): array
    {
        // Column headings for Excel
        return [
            // 'Month', 'Merchant Name', 'No. Transaction (Deposit)', 'Total Deposit Amount', 'Deposit Commission', 'No. Transaction (Withdrawal)', 'Total Withdrawal Amount', 'Withdrawal Commission', 'Total Commission'
        ];
    }

    public function startCell(): string
    {
        // Starting cell in Excel (to leave room for any other data)
        return 'A2';
    }
}
