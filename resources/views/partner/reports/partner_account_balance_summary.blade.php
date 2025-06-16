<x-partner-layout :title="$pageTitle">

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="{{ route('partner.reports.partner_account_balance_summary') }}" method="get">

        <div class="row justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="form-group">
                    <label>@lang('partner_basic.from_date_label')</label>
                    <input type="date" class="form-control" value="{{$from_date}}" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>@lang('partner_basic.to_date_label')</label>
                    <input type="date" class="form-control" value="{{$to_date}}" name="to_date" id="datepicker" />
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                        class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3 style="color: #7367f0">{{ __('partner_basic.Partner_Account_Balance_Summary_en')}}</h3>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>


                                <th scope="col">@lang('partner_basic.Date_label')</th>
                                <th scope="col">@lang('partner_basic.Opening_Balance_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Deposit_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Deposit_Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Withdrawal_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Withdrawal_Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Settlement_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Settlement_Charges_en')</th>
                                <th scope="col">@lang('partner_basic.Total_Adjustment_en')</th>
                                <th scope="col">@lang('partner_basic.Commission_Earned_en')</th>
                                <th scope="col">@lang('partner_basic.Closing_Balance_en')</th>


                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data))
                            @forelse($data as $key => $item)
                            <tr>

                                <td>{{ $item['date'] }}</td>
                                <td>{{ number_format($item['opening_balance'], 2) }}</td>
                                <td>{{ number_format($item['deposit_amount'], 2) }}</td>
                                <td>{{ number_format($item['deposit_charges'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_amount'], 2) }}</td>
                                <td>{{ number_format($item['withdrawal_charges'], 2) }}</td>
                                <td>{{ number_format($item['settlement_amount'], 2) }}</td>
                                <td>{{ number_format($item['settlement_charges'], 2) }}</td>
                                <td>{{ number_format($item['adjustment'], 2) }}</td>
                                <td>{{ number_format($item['commission'], 2) }}</td>
                                <td>{{ number_format($item['closing_balance'], 2) }}</td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('partner_basic.no_data_found')</p>
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
</x-partner-layout>
