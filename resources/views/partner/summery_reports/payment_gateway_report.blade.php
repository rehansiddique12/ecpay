<x-partner-layout :title="$pageTitle">
    <style>
        td:hover {
            background-color: lightgray;
            cursor: pointer;
        }
    </style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.payment.payment_gateway_report') }}" method="get">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.from_date_label')</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        </label><label>@lang('partner_basic.to_date_label')</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> @lang('partner_basic.search')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                        <h3 style="color: #7367f0">{{ __('partner_basic.Payment_Gateway_Performance_Report_en')}}</h3>
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('partner_basic.Date_en')</th>
                            <th scope="col">@lang('partner_basic.Total_Deposit_Request_en')</th>
                            <th scope="col">@lang('partner_basic.Total_Auto_Process_en')</th>
                            <th scope="col">@lang('partner_basic.Total_Manual_Process_en')</th>
                            <th scope="col">@lang('partner_basic.Total_Abandoned_en')</th>
                            <th scope="col">@lang('partner_basic.Success_Rate_en')</th>
                            <th scope="col">@lang('partner_basic.Within_10s_en')</th>
                            <th scope="col">@lang('partner_basic.gt_10_seconds_en')</th>
                            <th scope="col">@lang('partner_basic.gt_20_seconds_en')</th>
                            <th scope="col">@lang('partner_basic.gt_30_seconds_en')</th>
                            <th scope="col">@lang('partner_basic.gt_40_seconds_en')</th>
                            <th scope="col">@lang('partner_basic.gt_50_seconds_en')</th>
                            <th scope="col">@lang('partner_basic.gt_1_min_en')</th>
                            <th scope="col">@lang('partner_basic.gt_5_min_en')</th>
                            <th scope="col">@lang('partner_basic.gt_10_min_en')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($combined as $date => $apis)
                            <tr>
                                <td rowspan="{{ count($apis) + 1 }}">{{ $date }}</td> <!-- Group by Date -->
                                @foreach ($apis as $api_id => $counts)
                                    @php
                                        $partnerName = $partners[$api_id] ?? $api_id; // Fetch partner name from $partners array
                                        $fundCount = $counts['fund_count'] ?? 0;
                                        $autoProcessCount = $counts['auto_process_count'] ?? 0;
                                        $manualProcessCount = $counts['manual_process_count'] ?? 0;
                                        $abandoned = $fundCount - ($autoProcessCount + $manualProcessCount);

                                        $timeLessThan10 = $counts['time_less_than_10'] ?? 0;
                                        $timeBetween10And20 = $counts['time_between_10_and_20'] ?? 0;
                                        $timeBetween20And30 = $counts['time_between_20_and_30'] ?? 0;
                                        $timeBetween30And40 = $counts['time_between_30_and_40'] ?? 0;
                                        $timeBetween40And50 = $counts['time_between_40_and_50'] ?? 0;
                                        $timeBetween50And60 = $counts['time_between_50_and_60'] ?? 0;
                                        $timeBetween60And5Minutes = $counts['time_between_60_and_5_minutes'] ?? 0;
                                        $timeBetween5And10Minutes = $counts['time_between_5_and_10_minutes'] ?? 0;
                                        $time_greater_than_10_minutes = $counts['time_greater_than_10_minutes'] ?? 0;
                                        $successRate =
                                            $fundCount > 0 && $fundCount - $abandoned > 0
                                                ? ($autoProcessCount / ($fundCount - $abandoned)) * 100
                                                : 0;
                                    @endphp
                            <tr>
                                {{-- <td>{{ $partnerName }}</td> --}}
                                <td>{{ $fundCount }}</td>
                                <td>{{ $autoProcessCount }}</td>
                                <td>{{ $manualProcessCount }}</td>
                                <td>{{ max(0, $abandoned) }}</td> <!-- Ensure no negative values -->
                                <td>{{ number_format($successRate, 2) }}%</td> <!-- Format success rate -->
                                <td>{{ $timeLessThan10 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween10And20 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween20And30 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween30And40 }}</td>
                                <td>{{ $timeBetween40And50 }}</td>
                                <td>{{ $timeBetween50And60 }}</td>
                                <td>{{ $timeBetween60And5Minutes }}</td>
                                <td>{{ $timeBetween5And10Minutes }}</td>
                                <td>{{ $time_greater_than_10_minutes }}</td>
                            </tr>
                        @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center">@lang('partner_basic.No_Data_Available_en')</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@push('js')
@endpush
</x-partner-layout>
