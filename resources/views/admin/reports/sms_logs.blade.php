<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.sms.logs') }}" method="get">
            <div class="row align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

                <div class="col-md-3">
                    <label>{{ __('reports.from_date') }}</label>
                    <input type="datetime-local" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label>{{ __('reports.to_date') }}</label>
                    <input type="datetime-local" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3">
                    <label>{{ __('reports.e_wallet_name') }}</label>
                    <select class="form-control select2" name="e_wallet_name" data-placeholder="{{ __('reports.select_e_wallet_name') }}">
                        <option value="">{{ __('reports.all') }}</option>
                        @foreach ($distinctWalletNames as $name)
                            <option value="{{ $name }}" {{ request('e_wallet_name') == $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>{{ __('reports.type') }}</label>
                    <select name="type" class="form-control select2" data-placeholder="{{ __('reports.select_type') }}">
                        <option value="">{{ __('reports.all') }}</option>
                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>{{ __('reports.deposit') }}</option>
                        <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>{{ __('reports.withdrawal') }}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>{{ __('reports.ewallet_cust_txn') }}</label>
                    <input type="text" name="search_any" class="form-control" value="{{ request('search_any') }}" placeholder="Enter customer acc or txn">
                </div>        

                <div class="col-md-2">
                    <br>
                    <button type="submit" class="btn btn-primary mt-1">
                        <i class="icon-base ti tabler-search me-1"></i> {{ __('reports.search') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('reports.id') }}</th>
                                <th>{{ __('reports.e_wallet_name') }}</th>
                                <th>{{ __('reports.e_wallet_no') }}</th>
                                <th>{{ __('reports.customer_acc_no') }}</th>
                                <th>{{ __('reports.txn') }}</th>
                                <th>{{ __('reports.last_balance') }}</th>
                                <th>{{ __('reports.amount') }}</th>
                                <th>{{ __('reports.comm') }}</th>
                                <th>{{ __('reports.charge') }}</th>
                                <th>{{ __('reports.final_amount') }}</th>
                                <th>{{ __('reports.sms_type') }}</th>
                                <th>{{ __('reports.type') }}</th>
                                <th>{{ __('reports.matched') }}</th>
                                <th>{{ __('reports.sent') }}</th>
                                <th>{{ __('reports.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>{{ $log->e_wallet_name }}</td>
                                    <td>{{ $log->e_wallet_no }}</td>
                                    <td>{{ $log->customer_acc_no }}</td>
                                    <td>{{ $log->txn }}</td>
                                    <td>{{ number_format($log->account_last_amount, 2) }}</td>
                                    <td>{{ number_format($log->amount, 2) }}</td>
                                    <td>{{ number_format($log->comm, 2) }}</td>
                                    <td>{{ number_format($log->charge, 2) }}</td>
                                    <td>{{ number_format($log->final_amount, 2) }}</td>
                                    <td>{{ $log->sms_type }}</td>
                                    <td>
                                        @if ($log->type == 1)
                                            <span class="badge bg-success">{{ __('reports.deposit') }}</span>
                                        @elseif ($log->type == 2)
                                            <span class="badge bg-danger">{{ __('reports.withdrawal') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->matched == 1)
                                            <span class="badge bg-success">{{ __('reports.yes') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('reports.no') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->sent)
                                            <span class="badge bg-success">{{ __('reports.sent') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('reports.not_sent') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">{{ __('reports.no_sms_logs_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="card-footer text-center">
                        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            let $select = $('.select2').select2({
                allowClear: true,
                selectOnClose: true,
            });

            $select.on('select2:unselecting', function(e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function(e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
                }
            });
        </script>
    @endpush
</x-admin-layout>
