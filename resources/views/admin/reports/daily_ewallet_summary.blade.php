<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.daily_ewallet_summary') }}" method="get">
            <div class="row align-items-center">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.select_date') }}</label>
                        <input type="datetime-local" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                    <div class="form-group">
                        <label>{{ __('reports.select_date') }}</label>
                        <input type="datetime-local" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
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
                                    <th scope="col">{{ __('reports.date') }}</th>
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
                                @php
                                    $grandDeposit = 0;
                                    $grandWithdrawal = 0;
                                    $grandTransferIn = 0;
                                    $grandTransferOut = 0;
                                @endphp
                        
                                @if (isset($data))
                                    @forelse($data as $key => $date)
                                        @foreach($date as $key2 => $item)
                                            @php
                                                $grandDeposit += $item['total_deposit'];
                                                $grandWithdrawal += $item['total_withdrawal'];
                                                $grandTransferIn += $item['transfer_in'];
                                                $grandTransferOut += $item['transfer_out'];
                                            @endphp
                                            <tr>
                                                <td>{{ $item['date'] ?? $key2 }}</td>
                                                <td>{{ $item['e_wallet_name'] }}</td>
                                                <td>{{ $item['account_no'] }}</td>
                                                <td>{{ getAmount($item['opening_balance'], 2) }}</td>
                                                <td>{{ getAmount($item['total_deposit'], 2) }}</td>
                                                <td>{{ getAmount($item['total_withdrawal'], 2) }}</td>
                                                <td>{{ getAmount($item['transfer_in'], 2) }}</td>
                                                <td>{{ getAmount($item['transfer_out'], 2) }}</td>
                                                <td>{{ getAmount($item['closing_balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('reports.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                        
                                {{-- Total Row --}}
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="4" class="text-right">{{ __('reports.total') }}</td>
                                    <td>{{ getAmount($grandDeposit, 2) }}</td>
                                    <td>{{ getAmount($grandWithdrawal, 2) }}</td>
                                    <td>{{ getAmount($grandTransferIn, 2) }}</td>
                                    <td>{{ getAmount($grandTransferOut, 2) }}</td>
                                    <td>{{ getAmount($grandDeposit-$grandWithdrawal+$grandTransferIn-$grandTransferOut, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>

    </div>
    @push('js')
    @endpush
</x-admin-layout>
