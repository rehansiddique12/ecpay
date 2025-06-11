<x-partner-layout :title="$pageTitle">

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.merchant_reports.by_name') }}" method="get">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <div class="row align-items-left">
            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('partner_basic.from_date_label')</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('partner_basic.to_date_label')</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>
            <input type="hidden" name="search" value="Yes">
            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('partner_basic.merchant_label')</label>
                    <select name="merchant" class="form-select">
                        <option value="">@lang('partner_basic.select_merchant')</option>
                        @foreach($apis as  $key => $val)
                        <option value="{{ $key }}" @if(@request()->merchant == $key) selected @endif>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="{{ route('partner.merchant_reports.export_by_name', ['from_date' => $from_date , 'to_date' => $to_date , 'merchant' => @request()->merchant]) }}"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                        <i class="icon-base ti tabler-download me-1"></i> @lang('partner_basic.export')
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
                @if(isset($totalSummary))
                <div>
                    <h4 class="text-bold">@lang('partner_basic.summary')</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td>@lang('partner_basic.total_deposit_transaction_label'):</td>
                            <td>{{ number_format($totalSummary->total_deposit_transactions , 0) }}</td>
                            <td>@lang('partner_basic.total_amount_label'):</td>
                            <td>{{ number_format($totalSummary->total_deposit , 2) }}</td>
                            <td>@lang('partner_basic.commission_label'):</td>
                            <td>{{ number_format($totalSummary->total_deposit_commission , 2) }}</td>
                            <td>@lang('partner_basic.total_commission_label'):</td>
                            <td>{{ number_format($totalSummary->total_commission , 2) }}</td>
                        </tr>
                        <tr>
                            <td>@lang('partner_basic.total_withdrawal_transaction_label'):</td>
                            <td>{{ number_format($totalSummary->total_withdrawal_transactions , 0) }}</td>
                            <td>@lang('partner_basic.total_amount_label'):</td>
                            <td>{{ number_format($totalSummary->total_withdrawal , 0) }}</td>
                            <td>@lang('partner_basic.commission_label'):</td>
                            <td>{{ number_format($totalSummary->total_withdrawal_commission , 0) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr class="text-center">
                            <th rowspan="2">@lang('partner_basic.merchant_name_label')</th>
                            <th rowspan="1"></th>
                            <th colspan="3">@lang('partner_basic.deposit_label')</th>
                            <th colspan="3">@lang('partner_basic.withdrawal_label')</th>
                           <th></th>
                        </tr>
                        <tr>
                            <th>@lang('partner_basic.Date_label')</th>
                            <th>@lang('partner_basic.no_transaction_label')</th>
                            <th>@lang('partner_basic.total_amount_label')</th>
                            <th>@lang('partner_basic.commission_label')</th>
                            <th>@lang('partner_basic.no_transaction_label')</th>
                            <th>@lang('partner_basic.total_widthdrawal_label')</th>
                            <th>@lang('partner_basic.commission_label')</th>
                            <th>@lang('partner_basic.total_commission_label')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @isset($results)
                            @php
                                $previousMerchant = null;
                            @endphp
                                @forelse($results as $key => $result)
                                <tr>
                                    <td>
                                        @if ($previousMerchant !== $apis[$result->api_id])
                                        {{ $apis[$result->api_id] }}
                                        @php
                                            $previousMerchant = $apis[$result->api_id];
                                        @endphp
                                        @endif
                                    </td>
                                    <td>{{$result->date }}</td>
                                    <td>{{number_format($result->total_deposit_transactions , 0)}}</td>
                                    <td>{{number_format($result->total_deposit  ,2)}}</td>
                                    <td>{{number_format($result->total_charges_deposit ,2)}}</td>
                                    <td>{{number_format($result->total_withdrawal_transactions, 0)}}</td>
                                    <td>{{number_format($result->total_withdrawal ,2)}}</td>
                                    <td>{{number_format($result->total_charges_withdrawal ,2)}}</td>
                                    <td>{{number_format($result->total_commission , 2)}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark">@lang('partner_basic.no_data_found')</p>
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
@endpush
</x-partner-layout>
