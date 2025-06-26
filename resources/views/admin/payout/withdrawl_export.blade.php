<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Trx Number</th>
            <th>Partner Trx Number</th>
            <th>Username</th>
            <th>Method</th>
            <th>Account No</th>
            <th>Amount</th>
            <th>Merchant Charge</th>
            <th>Net Amount</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Sent From</th>
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M, Y H:i') }}</td>
                <td>{{ $item->trx_id }} / {{ $item->txn_id }}</td>
                <td>{{ $item->partner_transection_id }} / {{ $item->member_id }}</td>
                <td>{{ optional($item->api)->name }} ({{ optional($item->api)->acc_type }})</td>
                <td>{{ $item->e_wallet_name }}</td>
                <td>{{ $item->user_account_no }}</td>
                <td>{{ getAmount($item->amount, 2) }}</td>
                <td>{{ getAmount($item->charge, 2) }}</td>
                <td>{{ getAmount($item->amount + $item->charge, 2) }}</td>
                <td>
                    @if ($item->transfer_status == 2)
                        Approved /
                    @elseif($item->transfer_status == 1)
                        Pending /
                    @elseif($item->transfer_status == 3)
                        Rejected /
                    @endif

                    @if ($item->status == 'Complete')
                        Transferred
                    @elseif(in_array($item->status, ['initiate', 'Pending']))
                        Transfer Pending
                    @elseif($item->status == 'Reject')
                        Transfer Rejected
                    @endif
                </td>
                <td>{{ $item->feedback }}</td>
                <td>{{ $item->e_wallet_phone_number }} / {{ $item->e_wallet_type }}</td>
                <td>{{ $item->request_source }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
