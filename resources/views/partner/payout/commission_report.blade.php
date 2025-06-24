@extends('partner.layouts.app')
@section('title')
@lang($page_title)
@endsection
@section('content')


<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.api.commissions.search') }}" method="get">
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
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="1" @if(@request()->type == '1') selected @endif>@lang('Deposit')</option>
                        <option value="2" @if(@request()->type == '2') selected @endif>@lang('Withdrawal')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-control">
                        <option value="">All</option>
                        @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected @endif>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
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

                                <th scope="col">@lang('Partner/Agent')</th>
                                <th scope="col">@lang('Type')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Charges')</th>
                                <th scope="col">@lang('Net Amount')</th>
                                <th scope="col">@lang('Profit')</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $key => $item)
                            <tr>
                                <td>{{ $item->api->name }}</td>
                                <td data-label="@lang('Status')" class="text-lg-center text-right">
                                    @if ($item->type == 2)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-danger danger font-12"></i> @lang('Withdrawal') </span>
                                    @elseif($item->type == 1)
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-success success font-12"></i> @lang('Deposit')</span>
                                    @endif
                                </td>
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->charges }} ({{ $item->charges_p }}%)</td>
                                <td>{{ $item->total_amount }}</td>
                                <td>{{ $item->profit }} ({{ $item->profit_p }}%)</td>
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
            </div>
        </div>
    </div>

</div>




@endsection
@push('js')
<script>
    "use strict";
    $(document).ready(function(e) {


        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


    });

    $(document).ready(function() {
        $('select').select2({
            selectOnClose: true
        });
    });
</script>

@endpush