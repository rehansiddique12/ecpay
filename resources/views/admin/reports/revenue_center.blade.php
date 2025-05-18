<x-admin-layout :title="$pageTitle">
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.reports.revenue_center') }}" method="get">
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
                            <tr>


                                <th scope="col">Date</th>
                                <th scope="col">Total Deposit Charges</th>
                                <th scope="col">Total Withdrawal Charges</th>
                                <th scope="col">Total Paid Commission</th>
                                <th scope="col">Daily Profit</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data))
                            @forelse($data as $key => $item)
                            <tr>
                                <td>{{ $item['date'] }}</td>
                                <td>{{ number_format($item['deposit_charges'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_charges'], 2) }}</td>
                                <td>{{ number_format($item['commission_profit'], 2) }}</td>
                                <td>{{ number_format($item['daily_profit'], 2) }}</td>

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
