<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filterData;

    // Constructor to accept the filtered data
    public function __construct($filterData)
    {
        $this->filterData = $filterData;
    }

    // The collection of data to export
    public function collection()
    {
        // Return the filtered data collection
        return collect($this->filterData);
    }

    // The headings for the columns
    public function headings(): array
    {
        return [
            'ID',
            'Partner',
            'Date Time',
            'Final Amount',
            'Balance',
            'Transaction Type',
            'Transaction ID',
            'Partner ID',
            'Created At',
            'Updated At',
            'Source',
            'Amount',
            'Charge',
            'Sender',
            'E-Wallet Name',
            'E-Wallet Phone Number',
            'E-Wallet Type',
            'Partner Transaction ID',
            'Transaction ID (External)',
            'Transaction Created At',
        ];
    }

    // Map the data to the correct columns
    public function map($row): array
    {
        return [
            $row['id'],
            $row['partner'],
            $row['date_time'],
            $row['final_amount'],
            $row['balance'],
            $this->getTransactionType($row['transection_type']),
            $row['transection_id'],
            $row['partner_id'],
            $row['created_at'],
            $row['updated_at'],
            $row['source'],
            $row['amount'],
            $row['charge'],
            $row['sender'],
            $row['e_wallet_name'],
            $row['e_wallet_phone_number'],
            $row['e_wallet_type'],
            $row['partner_transection_id'],
            $row['txn_id'],
            $row['txn_created_at'],
        ];
    }

    // Helper function to map the transaction type ID to a readable format
    private function getTransactionType($type)
    {
        $transactionTypes = [
            1 => 'Payment',
            2 => 'Payout',
            3 => 'API Transaction',
            4 => 'Settlement',
            5 => 'Partner Commission',
            7 => 'Payout (Again)',
        ];

        return $transactionTypes[$type] ?? 'Unknown';
    }
}
