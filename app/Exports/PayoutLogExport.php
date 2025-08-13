<?php

namespace App\Exports;

use App\Models\Payout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayoutLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Return the collection of records to export.
     */
    public function collection()
    {
        return $this->records;
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Transaction Number',
            'Partner Transaction ID',
            'Username',
            'Method',
            'Account Number',
            'Amount',
            'Merchant Charge',
            'Net Amount',
            'Status',
            'Remarks',
            'Sent From',
            'Source',
        ];
    }

    /**
     * Map each record to an array for the Excel rows.
     */
    public function map($payout): array
    {
        $basic = config('basic'); // Assuming currency_symbol is in config

        return [
            $payout->id,
            dateTime($payout->created_at, 'd M, Y H:i'),
            $payout->trx_id . ' / ' . ($payout->txn_id ?? '-'),
            $payout->partner_transection_id . ' / ' . ($payout->member_id ?? '-'),
            optional($payout->api)->name . ' (' . (optional($payout->api)->acc_type ?? 'Partner Transaction') . ')',
            $payout->e_wallet_name ?? '-',
            $payout->user_account_no ?? '-',
            getAmount($payout->amount, 2) . ' ' . ($basic['currency_symbol'] ?? ''),
            getAmount($payout->charge, 2) . ' ' . ($basic['currency_symbol'] ?? ''),
            getAmount($payout->amount + $payout->charge, 2) . ' ' . ($basic['currency_symbol'] ?? ''),
            $this->formatStatus($payout),
            $payout->feedback ?? '-',
            ($payout->e_wallet_phone_number ?? '-') . ' / ' . ($payout->e_wallet_type ?? '-'),
            $payout->request_source ?? '-',
        ];
    }

    /**
     * Format the status for display in the Excel file.
     */
    private function formatStatus($payout): string
    {
        $transferStatus = '';
        if ($payout->transfer_status == 2) {
            $transferStatus = 'Request Approved';
        } elseif ($payout->transfer_status == 1) {
            $transferStatus = 'Request Pending';
        } elseif ($payout->transfer_status == 3) {
            $transferStatus = 'Request Rejected';
        }

        $status = '';
        if ($payout->status == 'Complete') {
            $status = 'Transferred';
        } elseif (in_array($payout->status, ['inititate', 'Pending'])) {
            $status = 'Transfer Pending';
        } elseif ($payout->status == 'Reject') {
            $status = 'Transfer Rejected';
        }

        return $transferStatus . ' / ' . $status;
    }
}
