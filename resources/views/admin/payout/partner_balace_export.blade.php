<table>
    <thead>
        <tr>
            <th>Created At</th>
            <th>Name</th>
            <th>Username</th>
            <th>Website</th>
            <th>Amount</th>
            <th>Charges</th>
            <th>Adjustment Type</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $item)
            @if (isset($item->api))
                <tr>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->api->name }}</td>
                    <td>{{ $item->api->username }}</td>
                    <td>{{ $item->api->website }}</td>
                    <td>{{ $item->amount }}</td>
                    <td>{{ $item->charges }}</td>
                    <td>
                        @switch($item->adjustment)
                            @case(2)
                                Deposit
                                @break
                            @case(3)
                                Withdrawal
                                @break
                            @case(4)
                                Top Up
                                @break
                            @default
                                Balance
                        @endswitch
                    </td>
                    <td>{{ $item->reason }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
