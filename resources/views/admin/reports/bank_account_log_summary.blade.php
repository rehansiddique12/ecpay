<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.bank_account_log_summary') }}" method="get">
        <div class="row align-items-center">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="from_date" />
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="to_date" />
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Gateways</label>
                    <select name="bank_name" class="form-control" id="bank-name-select">
                        <option value="">Select Gateway</option>

                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Account</label>
                    <select name="account_number" class="form-control" id="account-number-select">
                    <option value="">All Account</option>
                    </select>
                </div>
            </div>



            <div class="col-md-3">
                <div class="form-group">
                    <label>Filter By Status</label>
                    <select name="filter_status" class="form-control">
                        <option value="">All</option>
                        <option value="2" {{ isset($filter_status) && $filter_status == 2 ? 'selected' : '' }}>Completed</option>
                        <option value="3" {{ isset($filter_status) && $filter_status == 3 ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="form_submit" value="form_submit">

            <div class="col-md-3">
                <div class="form-group">
                    <label>Merchants</label>
                    <select name="merchants" class="form-control" id="bank-name-select">
                        <option value="">Select Merchant</option>
                        @foreach ($apisList as  $key => $value)
                        <option value="{{ $key }}" {{ isset($key) && $key == $merchant ? 'selected' : '' }}>
                            {{$value}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i class="fas fa-search"></i> @lang('Search')</button>
                    <a href="" class="btn waves-effect waves-light btn-success">
                        <i class="fas fa-file-export"></i> @lang('Export')
                    </a>
                </div>
            </div>

        </div>
    </form>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h4>{{ __('reports.transection_report') }}</h4>
                    <!-- <h3>Deposit Report</h3> -->
                     <div class="row mb-4">
    <div class="col-md-4">
        <p>Total Deposit Count: {{ $summary['total_deposit_count'] }}</p>
        <p>Total Withdrawal Count: {{ $summary['total_withdrawal_count'] }}</p>
        <p>Total Transactions: {{ $summary['total_transactions'] }}</p>
    </div>
    <div class="col-md-4">
        <p>Total Deposit Amount: {{ number_format($summary['total_deposit_amount'], 2) }}</p>
        <p>Total Withdrawal Amount: {{ number_format($summary['total_withdrawal_amount'], 2) }}</p>
    </div>
    <div class="col-md-4">
        <p>Total Transfer In: {{ number_format($summary['transfer_in'], 2) }}</p>
        <p>Total Transfer Out: {{ number_format($summary['transfer_out'], 2) }}</p>
        <p>Total Transfer Transaction: {{ $summary['total_transfer_transactions'] }}</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <p>Total Completed Deposit: {{ $summary['total_completed_deposit'] }}</p>
        <p>Total Rejected Deposit: {{ $summary['total_rejected_deposit'] }}</p>
        @php
            $successRate = $summary['total_deposit_count'] > 0
                ? ($summary['total_completed_deposit'] / $summary['total_deposit_count']) * 100
                : 0;
        @endphp
        <p>Deposit Success Rate: {{ number_format($successRate , 2) }}%</p>
    </div>
    <div class="col-md-4">
        <p>Total Completed Withdrawal: {{ $summary['total_completed_withdrawal'] }}</p>
        <p>Total Rejected Withdrawal: {{ $summary['total_rejected_withdrawal'] }}</p>
    </div>
    <div class="col-md-4">
        <p>Total Settlement: {{ number_format($summary['total_settlement'] ?? 0, 2) }}</p>
    </div>
</div>

                    <div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Bank</th>
            <th>Account Number</th>
            <th>Creation Date Time</th>
            <th>Completion Date Time</th>
            <th>Previous Balance</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Diff</th>
            <th>Partner Name</th>
            <th>Transaction Number</th>
            <th>Type</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
{{-- @dd($transaction) --}}
            <tr>
                {{-- Bank --}}
                <td>{{ $transaction->bank ?? 'N/A' }}</td>

                {{-- Account Number --}}
                <td>{{ $transaction->account_number ?? 'N/A' }}</td>

                {{-- Creation Date Time --}}
                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</td>

                {{-- Completion Date Time --}}
                <td>
                    {{ isset($transaction->completion_date_time)
                        ? \Carbon\Carbon::parse($transaction->completion_date_time)->format('d M Y H:i')
                        : '' }}
                </td>

                {{-- Previous Balance --}}
                <td>{{ $transaction->previous_balance ?? '-' }}</td>

                {{-- Amount --}}
                <td>{{ number_format($transaction->amount ?? 0, 2) }}</td>

                {{-- Final Balance --}}
                <td>{{ $transaction->balance !== null ? number_format($transaction->balance, 2) : '-' }}</td>

                {{-- Diff --}}
                <td>{{ $transaction->diff !== null ? number_format($transaction->diff, 2) : '-' }}</td>

                {{-- Partner Name --}}
                <td>{{ $transaction->partner_name ?? '-' }}</td>

                {{-- Transaction Number --}}
                <td>{{ $transaction->transaction_number ?? '-' }}</td>

                {{-- Type --}}
                <td>
                    @switch($transaction->type)
                        @case('deposit') Deposit @break
                        @case('withdrawal') Withdrawal @break
                        @case('transfer_in') Transfer In @break
                        @case('transfer_out') Transfer Out @break
                        @default {{ ucfirst($transaction->type) }}
                    @endswitch
                </td>

                {{-- Status --}}
                <td>
                    @switch($transaction->status)
                        @case(1) Completed @break
                        @case(3) Rejected @break
                        @default Pending
                    @endswitch
                </td>

                {{-- Remarks --}}
                <td>{{ $transaction->remarks ?? '-' }}</td>
            </tr>
        @endforeach

    </tbody>
</table>

<div class="mt-3">

    {{ $transactions->appends($_GET)->links('partials.pagination') }}
</div>
</div>

                </div>
            </div>
        </div>

    </div>
    @push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadGateways() {
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $.ajax({
                    url: "{{ route('admin.bank_account.gateways') }}",
                    type: "GET",
                    data: { from_date, to_date },
                    success: function(data) {
                        let gatewaySelect = $('#bank-name-select');
                        gatewaySelect.empty();
                        gatewaySelect.append('<option value="">Select Gateway</option>');

                        $.each(data, function(index, item) {
                            gatewaySelect.append('<option value="'+ item +'">'+ item +'</option>');
                        });
                    }
                });
            }

            // Load gateways when date changes
            $('#from_date, #to_date').on('change', function() {
                loadGateways();
            });

            // When gateway is selected, load accounts
            $('#bank-name-select').on('change', function() {
                let bank_name = $(this).val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $.ajax({
                    url: "{{ route('admin.bank_account.accounts') }}",
                    type: "GET",
                    data: { bank_name, from_date, to_date },
                    success: function(data) {
                        let accountSelect = $('#account-number-select');
                        accountSelect.empty();
                        accountSelect.append('<option value="">All Accounts</option>');

                        $.each(data, function(index, item) {
                            accountSelect.append('<option value="'+ item +'">'+ item +'</option>');
                        });
                    }
                });
            });

            // Load gateways initially
            loadGateways();
        });
        </script>

    @endpush
</x-admin-layout>
