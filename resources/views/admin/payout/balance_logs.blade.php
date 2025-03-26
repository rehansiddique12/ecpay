<x-admin-layout :title="$pageTitle">
    <style>
        tr th{
          color: white !important
        }
    </style>

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <h3 style="color: #7367f0">{{$pageTitle}}</h3>
    <form action="{{ route('admin.balance.logs.search') }}" method="get">
        <div class="row justify-content-between align-items-center">

            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>E-Wallet</label>
                    <input type="text" class="form-control" value="{{@request()->ewallet}}" name="ewallet" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Account No</label>
                    <input type="text" class="form-control" value="{{@request()->account_no}}" name="account_no" />
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    <label>Transection Type</label>
                    <select name="type" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="plus" @if(@request()->type == 'plus') selected @endif>@lang('Add Credit')</option>
                        <option value="minus" @if(@request()->type == 'minus') selected @endif>@lang('Subtract Credit')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Account Type</label>
                    <select name="a_type" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="Merchant" @if(@request()->a_type == 'Merchant') selected @endif>@lang('Merchant')</option>
                        <option value="Personal" @if(@request()->a_type == 'Personal') selected @endif>@lang('Personal')</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="fas fa-search"></i> @lang('Search')</button>
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

                                <th scope="col">@lang('E-Wallet Name')</th>
                                <th scope="col">@lang('Account No.')</th>
                                <th scope="col">@lang('Type')</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Transection Type</th>
                                <th scope="col">Date-Time</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accountlog as $key => $item)
                            <tr>
                                <td>{{ optional($item->e_wallet_account)->e_wallet_name }}</td>
                                <td>{{ optional($item->e_wallet_account)->account_no }}</td>
                                <td>{{ optional($item->e_wallet_account)->type }}</td>

                                <td>{{ $item->amount }}</td>
                                @if($item->type=="plus")
                                <td><span class="badge bg-success text-white"><b>+ Add Credit</b></span></td>
                                @else
                                <td><span class="badge bg-danger text-white"><b>- Subtract Credit</b></span></td>
                                @endif
                                <td>{{ $item->created_at }}</td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('No Data Found')</p>
                                </td>
                            </tr>

                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $accountlog->appends($_GET)->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

</div>







{{-- @endsection --}}
@push('js')
@endpush
</x-admin-layout>
