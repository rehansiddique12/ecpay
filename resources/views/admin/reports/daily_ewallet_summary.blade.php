<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.daily_ewallet_summary') }}" method="get">
            <div class="row align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
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
    @endpush
    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
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
