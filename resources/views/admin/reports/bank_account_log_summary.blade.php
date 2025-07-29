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
                    <label>Banks</label>
                    <select name="bank_name" class="form-control" id="bank-name-select">
                        <option value="">Select Bank</option>
                        @foreach ($EwalletNames as  $bank)
                        <option value="{{ $bank }}" {{ isset($bank_name) && $bank_name == $bank ? 'selected' : '' }}>
                            {{strtoupper($bank)}}
                        </option>
                        @endforeach
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
                    <label>Filter By Type</label>
                    <select name="filter_type" class="form-control">
                        <option value="">All</option>
                        <option value="1" {{ isset($filter_type) && $filter_type == 1 ? 'selected' : '' }}>Deposit</option>
                        <option value="2" {{ isset($filter_type) && $filter_type == 2 ? 'selected' : '' }}>Withdrawal</option>
                        <option value="3" {{ isset($filter_type) && $filter_type == 3 ? 'selected' : '' }}>Transfer In</option>
                        <option value="4" {{ isset($filter_type) && $filter_type == 4 ? 'selected' : '' }}>Transfer Out</option>

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
                {{-- <th>Account Name</th> --}}
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
            @php $printedDomains = []; @endphp
            @foreach ($transactions as $transaction)
                <tr>
                    <td>
                        @if (!in_array($transaction->account_domain, $printedDomains))
                            {{ strtoupper($transaction->account_domain ?? '') }}
                            @php $printedDomains[] = $transaction->account_domain; @endphp
                        @endif
                    </td>

                    <td>
                        @if ($transaction->transaction_type != 2)
                            {{ $transaction->acc_no ?? '' }} &nbsp;
                            ({{ $bankAccountList[$transaction->acc_no] ?? $transaction->acc_no }})
                        @else
                            {{ $transaction->withdrawal_acc_no ?? '' }} &nbsp;
                            ({{ $bankAccountList[$transaction->withdrawal_acc_no] ?? $transaction->withdrawal_acc_no }})
                        @endif
                    </td>

                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') ?? '' }}</td>

                    <td>
                        {{ isset($transaction->bankAccountLog->updated_at) ? \Carbon\Carbon::parse($transaction->bankAccountLog->updated_at)->format('d M Y H:i') : '' }}
                    </td>

                    <td>{{ $transaction->bankAccountLog->previous_balance ?? '' }}</td>

                    <td>{{ number_format($transaction->amount, 2) }}</td>

                    <td>{{ $transaction->bankAccountLog->final_balance ?? '' }}</td>

                    <td>
                        @php
                            $prev = $transaction->bankAccountLog->previous_balance ?? null;
                            $amt = $transaction->amount ?? null;
                            $final = $transaction->bankAccountLog->final_balance ?? null;
                            $type = $transaction->transaction_type ?? null;
                        @endphp
                        @if (!is_null($prev) && !is_null($amt) && !is_null($final) && !is_null($type))
                            @php
                                $expected = in_array($type, [2, 4]) ? $prev - $amt : $prev + $amt;
                                $difference = $expected != $final ? number_format($final - $expected, 2) : null;
                            @endphp

                            @if (!is_null($difference) && $difference > 0)
                                <br><small class="text-danger">Diff: {{ $difference }}</small>
                            @endif
                        @endif
                    </td>

                    <td>{{ $apisList[$transaction->api_id] ?? '' }}</td>
                    <td>{{ $transaction->partner_transection_id }}</td>

                    <td>
                        @switch($transaction->transaction_type)
                            @case(1) Deposit @break
                            @case(2) Withdrawal @break
                            @case(3) Transfer In @break
                            @case(4) Transfer Out @break
                            @default Unknown
                        @endswitch
                    </td>

                    <td>
                        @switch($transaction->status)
                            @case(2) Complete @break
                            @case(3) Rejected @break
                            @default Pending
                        @endswitch
                    </td>
                    <td>{{ $transaction->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                </div>
            </div>
        </div>

    </div>
    @push('js')
    @endpush
</x-admin-layout>
