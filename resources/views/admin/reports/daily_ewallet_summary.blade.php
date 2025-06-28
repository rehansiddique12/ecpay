<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.daily_ewallet_summary') }}" method="get">
            <div class="row align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <p class="text-muted">
                    Showing results from <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong>
                    
                </p>
                <p class="text-end fw-bold">Total Records: {{ $EWalletAccounts->count() }}</p>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <!-- <input type="text" id="from_date" name="from_date" class="form-control flatpickr-with-icon"
                            value="{{ old('from_date', \Carbon\Carbon::parse($fromDate)->format('Y-m-d H:i:S')) }}"> -->
                        <input type="text" id="from_date" name="from_date" class="form-control flatpickr-with-icon"
                            value="{{ request('from_date') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <!-- <input type="text" id="to_date" name="to_date" class="form-control flatpickr-with-icon"
                            value="{{ old('to_date', \Carbon\Carbon::parse($toDate)->format('Y-m-d H:i:S')) }}"> -->
                        <input type="text" id="to_date" name="to_date" class="form-control flatpickr-with-icon"
                            value="{{ request('to_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.e_wallet_name') }}</label>
                        <select class="form-control select2" name="e_wallet_name" data-placeholder="Select E-Wallet Name">
                            <option></option>
                            <option value="">All</option>
                            @foreach ($distinctWalletNames as $name)
                                <option value="{{ $name }}" {{ ($e_wallet_name?? '') == $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.account_no') }}</label>
                        <input type="text" class="form-control" name="account_no" value="{{ request('account_no') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> {{ __('reports.search') }}</button>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">


                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">{{ __('reports.e_wallet_name') }}</th>
                                    <th scope="col">{{ __('reports.account_no') }}</th>
                                    <th scope="col">{{ __('reports.opening_balance') }}</th>
                                    <th scope="col">{{ __('reports.total_deposit') }}</th>
                                    <th scope="col">{{ __('reports.total_withdrawal') }}</th>
                                    <th scope="col">{{ __('reports.transfer_in') }}</th>
                                    <th scope="col">{{ __('reports.transfer_out') }}</th>
                                    <th scope="col">{{ __('reports.closing_balance') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    @forelse($data as $key => $item)
                                        <tr>
                                            <td>{{ $item['e_wallet_name'] }}</td>
                                            <td>{{ $item['account_no'] }}</td>
                                            <td>{{ $item['opening_balance'] }}</td>
                                            <td>{{ getAmount($item['total_deposit'], 2) }}</td>
                                            <td>{{ getAmount($item['total_withdrawal'], 2) }}</td>
                                            <td>{{ $item['transfer_in'] }}</td>
                                            <td>{{ $item['transfer_out'] }}</td>
                                            <td>{{ $item['closing_balance'] }}</td>


                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('reports.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $EWalletAccounts->appends($_GET)->links('partials.pagination') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
        .flatpickr-with-icon {
            background: url("https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/calendar3.svg") no-repeat right 12px center;
            /* background: url("https://img.icons8.com/ios-filled/ffffff/calendar--v1.png") no-repeat right 12px center; */
            background-size: 15px 15px;
            padding-right: 38px; 
            cursor: pointer;
            filter: brightness(0) invert(1); 
            color: white;
        }
    </style>
    @endpush
    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#from_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true,
            allowInput: true,
            allowEmpty: true,
        });
        flatpickr("#to_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true
        });
        let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
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
