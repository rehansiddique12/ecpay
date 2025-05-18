<x-partner-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.merchant_reports.by_date') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row align-items-left">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Date</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <input type="hidden" value="search" name="search_post">
            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    {{-- <button type="button" class="btn waves-effect waves-light btn-success"><i class="fas fa-share"></i> @lang('Export')</button> --}}
                    <a href="{{ route('partner.merchant_reports.export_by_date', ['from_date' => $from_date]) }}"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                        <i class="icon-base ti tabler-download me-1"></i> @lang('Export')
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
                <h3><b>Total Commission</b> {{number_format($totalCommissionAll , 2)}}</h3>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr class="text-center">
                            <th rowspan="2">Merchant Name</th>
                            <th colspan="3">Deposit</th>
                            <th colspan="3">Withdrawal</th>
                           <th></th>
                        </tr>
                        <tr>
                            <th>No. Transaction</th>
                            <th>Total Amount</th>
                            <th>Commission</th>
                            <th>No. Transaction</th>
                            <th>Total Withdrawal</th>
                            <th>Commission</th>
                            <th>Total Commission</th>
                        </tr>
                        </thead>
                        <tbody>
                            @isset($results)
                                @forelse($results as $key => $result)
                                <tr>
                                    <td>{{$apis[$result->api_id]}}</td>
                                    <td>{{number_format($result->total_deposit_transactions , 2)}}</td>
                                    <td>{{number_format($result->total_deposit  ,2)}}</td>
                                    <td>{{number_format($result->total_charges_deposit ,2)}}</td>
                                    <td>{{$result->total_withdrawal_transactions}}</td>
                                    <td>{{number_format($result->total_withdrawal ,2)}}</td>
                                    <td>{{number_format($result->total_charges_withdrawal ,2)}}</td>
                                    <td>{{number_format($result->total_commission , 2)}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">@lang('No Data Found')</p>
                                    </td>
                                </tr>
                                @endforelse
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
    // JavaScript/jQuery to dynamically update the export button href when the date is changed
    document.getElementById('datepicker').addEventListener('change', function() {
        var selectedDate = this.value;  // Get the selected date
        var exportButton = document.getElementById('exportButton');

        // Update the href of the export button with the selected date
        exportButton.href = "{{ route('partner.merchant_reports.export_by_date', ['from_date' => '']) }}/" + selectedDate;
    });
</script>
@endpush
</x-partner-layout>
