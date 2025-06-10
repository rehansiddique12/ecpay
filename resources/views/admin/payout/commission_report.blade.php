<x-admin-layout :title="$pageTitle">
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('admin.api.post.commissions') }}" method="get">
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
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-select">
                        <option value="">@lang('All')</option>
                        <option value="1" @if(@request()->type == '1') selected @endif>@lang('Deposit')</option>
                        <option value="2" @if(@request()->type == '2') selected @endif>@lang('Withdrawal')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                        <option></option>
                        <option value="">All</option>
                        @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected @endif>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                   <label>Parent</label>
                   <select name="parent" class="form-select select2" data-allow-clear="true" data-placeholder="Select Parent">
                        <option disabled selected></option>
                        <option value="" @if(request()->parent === '') selected @endif>All</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" @selected(request()->parent == $partner->id)>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-md-4">
                  <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="{{ route('admin.commissions.export', request()->all()) }}"
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

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('Partner/Agent')</th>
                                <th scope="col">@lang('Type')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Charges')</th>
                                <th scope="col">@lang('Net Amount')</th>
                                <th scope="col">@lang('Profit')</th>
                                <th scope="col">@lang('Parent')</th>
                                <th scope="col">@lang('Created At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item->api->name }}</td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if ($item->type == 2)
                                    <span class="badge bg-danger">
                                        <i class="fa fa-circle  text-white danger font-12"></i> @lang('Withdrawal')
                                    </span>
                                    @elseif($item->type == 1)
                                    <span class="badge bg-success">
                                        <i class="fa fa-circle text-white success font-12"></i> @lang('Deposit')
                                    </span>
                                    @endif
                                </td>
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->charges }} ({{ $item->charges_p }}%)</td>
                                <td>{{ $item->total_amount }}</td>
                                <td>{{ $item->profit }} ({{ $item->profit_p }}%)</td>
                                <td>{{ $item->fromapi->name }}</td>
                                <td>{{ $item->created_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>
                            @endforelse
                             <!-- Add a total row if it's the last page -->
                            @if($isLastPage && $totalAmount)
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                <td><strong>{{ $totalAmount }}</strong></td>
                                <td><strong>{{ $totalChargesSum }}</strong></td>
                                <td><strong>{{ $totalAAmountSum }}</strong></td>
                                <td><strong>{{ $totalProfitSum }}</strong></td>
                                <td colspan="4"></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination links -->
               <div class="card-footer">
                    {{ $records->appends($_GET)->links('partials.pagination') }}
                </div>

            </div>
        </div>
    </div>

</div>

 @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush
    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

    <script>
        "use strict";
        $(document).ready(function(e) {
           let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                selectOnClose: true,
            });

            // Prevent dropdown from opening on clear
            $select.on('select2:unselecting', function (e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function (e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
                }
            });
        });
    </script>

    @endpush
</x-admin-layout>
