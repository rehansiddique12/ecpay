<x-admin-layout :title="$pageTitle">

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.reports.master_report') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
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
                            <tr class="text-center">
                            <th rowspan="2">Date</th>
                            <th colspan="5">Deposit</th>
                            <th colspan="5">Withdrawal</th>
                            <th colspan="1">Commission</th>
                            <th colspan="2">Top Up</th>
                            <th colspan="2">Adjustment</th>
                            <th rowspan="2">Transfer Fees (BDT)</th>
                            <th colspan="2">Settlement (BDT)</th>
                            <th rowspan="2">Revenue (BDT)</th>
                            <th rowspan="2">Total Balance (BDT)</th>
                        </tr>
                        <tr >
                            <th>Qty</th>
                            <th>Total (BDT)</th>
                            <th>Merchant Charges (BDT)</th>
                            <th>E-Wallet Fee (BDT)</th>
                            <th>E-Wallet Commission (BDT)</th>
                            <th>Qty</th>
                            <th>Total (BDT)</th>
                            <th>Merchant Charges (BDT)</th>
                            <th>E-Wallet Fee (BDT)</th>
                            <th>E-Wallet Commission (BDT)</th>
                            <th>BDT</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(isset($data))
                            @forelse($data as $key => $item)
                            <tr>

                                <td>{{ $item['date'] }}</td>
                                <td>{{ $item['deposit_record_count'] }}</td>
                                <td>{{ number_format($item['deposit_amount'], 2) }}</td>
                                <td>{{ number_format($item['deposit_charges'], 2) }}</td>
                                <td>{{ number_format($item['deposit_e_wallet_charges'], 2) }}</td>
                                <td>{{ number_format($item['deposit_commission'], 2) }}</td>
                                <td>{{ $item['withdrawal_record_count'] }}</td>
                                <td>{{ number_format($item['withdrawal_amount'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_charges'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_e_wallet_charges'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_commission'], 2) }}</td>
                                <td>{{ number_format($item['commission_amount'], 2) }}</td>
                                <td>{{ number_format($item['top_up_amount'], 2) }}</td>
                                <td>{{ number_format($item['top_up_charges'], 2) }}</td>
                                <td>{{ number_format($item['adjustment_amount'], 2) }}</td>
                                <td>{{ number_format($item['adjustment_charges'], 2) }}</td>
                                <td>{{ number_format($item['transfer_charges'], 2) }}</td>
                                <td>{{ number_format($item['settlement_amount'], 2) }}</td>
                                <td>{{ number_format($item['settlement_charges'], 2) }}</td>
                                <td>{{ number_format($item['revenue'], 2) }}</td>
                                <td>{{ number_format($item['total'], 2) }}</td>
                                {{-- <td>0.00</td> --}}

                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>
                            @endforelse
                            @endif
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
