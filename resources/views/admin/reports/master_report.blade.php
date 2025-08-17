<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.reports.master_report') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mt-2">
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
                                <tr class="text-center">
                                    <th rowspan="2">{{ __('reports.date') }}</th>
                                    <th colspan="5">{{ __('reports.deposit') }}</th>
                                    <th colspan="5">{{ __('reports.withdrawal') }}</th>
                                    <th colspan="1">{{ __('reports.commission') }}</th>
                                    <th colspan="2">{{ __('reports.top_up') }}</th>
                                    <th colspan="2">{{ __('reports.adjustment') }}</th>
                                    <th rowspan="2">{{ __('reports.transfer_fees') }}</th>
                                    <th colspan="2">{{ __('reports.settlement') }}</th>
                                    <th rowspan="2">{{ __('reports.revenue') }}</th>
                                    {{-- <th rowspan="2">{{ __('reports.total_balance') }}</th> --}}
                                </tr>
                                <tr>
                                    <th>{{ __('reports.qty') }}</th>
                                    <th>{{ __('reports.total') }}</th>
                                    <th>{{ __('reports.merchant_charges') }}</th>
                                    <th>{{ __('reports.e_wallet_fee') }}</th>
                                    <th>{{ __('reports.e_wallet_commission') }}</th>
                                    <th>{{ __('reports.qty') }}</th>
                                    <th>{{ __('reports.total') }}</th>
                                    <th>{{ __('reports.merchant_charges') }}</th>
                                    <th>{{ __('reports.e_wallet_fee') }}</th>
                                    <th>{{ __('reports.e_wallet_commission') }}</th>
                                    <th>{{ __('reports.bdt') }}</th>
                                    <th>{{ __('reports.total') }}</th>
                                    <th>{{ __('reports.charges') }}</th>
                                    <th>{{ __('reports.total') }}</th>
                                    <th>{{ __('reports.charges') }}</th>
                                    <th>{{ __('reports.total') }}</th>
                                    <th>{{ __('reports.charges') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    @forelse($data as $key => $item)
                                        <tr>

                                            <td>{{ $item['date'] }}</td>
                                            <td>{{ $item['deposit_record_count'] }}</td>
                                            <td>{{ number_format($item['deposit_amount'], 2) }}</td>
                                            <td>{{ number_format($item['deposit_charges'], 2) }}</td>
                                            <td>{{ number_format($item['deposit_e_wallet_charges'], 2) }}</td>
                                            <!-- <td>{{ number_format($item['deposit_commission'], 2) }}</td> -->
                                            <td>
                                                <a href="#" class="show-commission" data-date="{{ $item['date'] }}" data-type="deposit">
                                                    {{ number_format($item['deposit_commission'], 2) }}
                                                </a>
                                            </td>
                                            <td>{{ $item['withdrawal_record_count'] }}</td>
                                            <td>{{ number_format($item['withdrawal_amount'], 2) }}</td>
                                            <td>{{ number_format($item['withdrawal_charges'], 2) }}</td>
                                            <td>{{ number_format($item['withdrawal_e_wallet_charges'], 2) }}</td>
                                            <!-- <td>{{ number_format($item['withdrawal_commission'], 2) }}</td> -->
                                            <td>
                                                <a href="#" class="show-commission" data-date="{{ $item['date'] }}" data-type="withdrawal">
                                                    {{ number_format($item['withdrawal_commission'], 2) }}
                                                </a>
                                            </td>
                                            <td>{{ number_format($item['commission_amount'], 2) }}</td>
                                            <td>{{ number_format($item['top_up_amount'], 2) }}</td>
                                            <td>{{ number_format($item['top_up_charges'], 2) }}</td>
                                            <td>{{ number_format($item['adjustment_amount'], 2) }}</td>
                                            <td>{{ number_format($item['adjustment_charges'], 2) }}</td>
                                            <td>{{ number_format($item['transfer_charges'], 2) }}</td>
                                            <td>{{ number_format($item['settlement_amount'], 2) }}</td>
                                            <td>{{ number_format($item['settlement_charges'], 2) }}</td>
                                            <td>{{ number_format($item['revenue'], 2) }}</td>
                                            {{-- <td>{{ number_format($item['total'], 2) }}</td> --}}

                                            {{-- <td>0.00</td> --}}

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('reports.no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                                <tr style="background-color: #7367f0;">
                                    <td id="new_total" style=" color:white;">Total</td>
                                    <td style=" color:white;">{{ $total_deposit_qty ?? '' }}</td>
                                    <td style=" color:white;">{{ isset($total_deposit_amount) ? number_format($total_deposit_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_deposit_charges) ? number_format($total_deposit_charges, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_deposit_e_wallet_charges) ? number_format($total_deposit_e_wallet_charges, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_deposit_commission) ? number_format($total_deposit_commission, 2) : '' }}</td>
                                    <td style=" color:white;">{{ $total_withdrawal_qty ?? '' }}</td>
                                    <td style=" color:white;">{{ isset($total_withdrawal_amount) ? number_format($total_withdrawal_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_withdrawal_charges) ? number_format($total_withdrawal_charges, 2) : '' }}</td>
                                    <td style=" color:white;">{{ number_format($item['withdrawal_e_wallet_charges'], 2) }}</td>
                                    <td style=" color:white;">{{ number_format($item['withdrawal_commission'], 2) }}</td>
                                    <td style=" color:white;">{{ isset($total_commission_amount) ? number_format($total_commission_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_top_up_amount) ? number_format($total_top_up_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_top_up_charges) ? number_format($total_top_up_charges, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_adjustment_amount) ? number_format($total_adjustment_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ isset($total_adjustment_charges) ? number_format($total_adjustment_charges, 2) : '' }}</td>
                                    <td style=" color:white;">{{ number_format($item['transfer_charges'], 2) }}</td>
                                    <td style=" color:white;">{{ isset($total_settlement_amount) ? number_format($total_settlement_amount, 2) : '' }}</td>
                                    <td style=" color:white;">{{ number_format($item['settlement_charges'], 2) }}</td>
                                    <td style=" color:white;">{{ isset($total_revenue) ? number_format($total_revenue, 2) : '' }}</td>
                                    {{-- <td style=" color:white;">{{ number_format($item['total'], 2) }}</td> --}}



                                    {{-- <td>0.00</td> --}}

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="commissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="commissionModalLabel">Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="commissionModalBody">
                    <p>Loading...</p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    {{ __('accounts.close') }}
                </button>
            </div>
        </div>
    </div>
</div>


   @push('js')
<script>
    $(document).on('click', '.show-commission', function (e) {
        e.preventDefault();

        let date = $(this).data('date');
        let type = $(this).data('type');

        const typeText = type === 'deposit' ? 'Deposit' : 'Withdrawal';
        $('#commissionModalLabel').text(typeText + ' Commissions');

        $('#commissionModalBody').html('<p>Loading...</p>');
        $('#commissionModal').modal('show');

        $.ajax({
            url: "{{ route('admin.reports.commission_breakdown') }}",
            method: "GET",
            data: { date, type },
            success: function (response) {
                $('#commissionModalBody').html(response.html);
            },
            error: function () {
                $('#commissionModalBody').html('<p class="text-danger">Failed to load data.</p>');
            }
        });
    });
</script>
@endpush


</x-admin-layout>
