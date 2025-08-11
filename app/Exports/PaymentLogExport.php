<?php

namespace App\Exports;

use App\Models\Deposit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Transaction No',
            'Partner Trx No',
            'Partner Txn Input',
            'Username',
            'Method',
            'Account No',
            'Amount',
            'Charge',
            'Payable Amount',
            'Status',
            'Source',
            'Completed At',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->created_at->format('d M, Y H:i'),
            $transaction->transaction,
            $transaction->partner_transection_id ?? '',
            !empty($transaction->txn_record) && $transaction->partner_transection_id != 0 ? $transaction->txn_record->txn_no : '',
            optional($transaction->user)->username ?? ($transaction->source == 'Admin Test' ? 'Admin Test' : optional($transaction->api)->name),
            optional($transaction->gateway)->name,
            $transaction->sender,
            getAmount($transaction->amount) . ' ' . optional($transaction->gateway)->currency,
            getAmount($transaction->charge) . ' ' . optional($transaction->gateway)->currency,
            (getAmount($transaction->amount) - getAmount($transaction->charge)) . ' ' . optional($transaction->gateway)->currency,
            $this->getStatusText($transaction),
            optional($transaction->api)->website,
            $transaction->created_at,
        ];
    }

    protected function getStatusText($transaction)
    {
        if ($transaction->status == 'Pending') {
            $createdAt = $transaction->created_at;
            $diffInMinutes = $createdAt->diffInMinutes(now());

            return $diffInMinutes > 10 ? 'Member Not Completed' : 'Pending';
        }

        return $transaction->status == 'Complete' ? 'Completed' :
              ($transaction->status == 'Reject' ? 'Rejected' : $transaction->status);
    }
}
