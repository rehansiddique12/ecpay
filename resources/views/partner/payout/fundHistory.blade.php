@extends('partner.layouts.app')
@section('title',trans('Fund History'))
@section('content')

<div class="row justify-content-center m-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('partner.fund-history.search') }}" method="get">
                    <div class="row justify-content-between">
                        <div class="col-md-4">
                            <div class="form-group input-box mb-2">
                                <input type="text" name="name" value="{{@request()->name}}" class="form-control" placeholder="@lang('Type Here')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group input-box mb-2">
                                <select name="status" class="form-select">
                                    <option value="">@lang('All Payment')</option>
                                    <option value="1" @if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
                                    <option value="2" @if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
                                    <option value="3" @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group input-box mb-2">
                                <input type="date" class="form-control" name="date_time" id="datepicker" />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2 h-fill">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search"></i> @lang('Search')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4 m-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body ">
                <div class="table-parent table-responsive m-0">
                    <table class="table table-striped" id="service-table">
                        <thead class="thead thead-dark">
                            <tr>
                                <th scope="col">@lang('Transaction ID')</th>
                                <th scope="col">@lang('Gateway')</th>
                                <th scope="col">Sender Account</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Charge')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Time')</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($funds as $key => $data)
                            <tr>

                                <td data-label="#@lang('Transaction ID')">
                                    <strong>{{$data->transaction}}</strong>
                                </td>
                                <td data-label="@lang('Gateway')">@lang(optional($data->gateway)->name)</td>
                                <td>{{$data->account_no}}</td>
                                <td data-label="@lang('Amount')">
                                    <strong>{{getAmount($data->amount)}} @lang($basic->currency)</strong>
                                </td>

                                <td data-label="@lang('Charge')">
                                    <strong>{{getAmount($data->charge)}} @lang($basic->currency)</strong>
                                </td>

                                <td data-label="@lang('Status')">
                                    @if($data->status == 1)
                                    <span class="badge bg-success">@lang('Complete')</span>
                                    @elseif($data->status == 2)
                                    <span class="badge bg-warning">@lang('Pending')</span>
                                    @elseif($data->status == 3)
                                    <span class="badge bg-danger">@lang('Cancel')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Time')">
                                    {{ dateTime($data->created_at, 'd M Y h:i A') }}
                                </td>
                                <td data-label="@lang('Action')">
                                    @if(empty($data->receipt_image))
                                    <a href="{{ route('partner.fund-history.upload', ['id' => $data->id]) }}" class="btn btn-primary btn-sm text-white">Upload Receipt</a>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr class="text-center">
                                <td colspan="100%">{{__('No Data Found!')}}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $funds->appends($_GET)->links($theme.'partials.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection