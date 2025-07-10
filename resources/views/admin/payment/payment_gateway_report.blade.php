<x-admin-layout :title="$pageTitle">
    <style>
        td:hover {
            background-color: lightgray;
            cursor: pointer;
        }
    </style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payment.payment_gateway_report') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            @php
                $today = \Carbon\Carbon::today()->format('Y-m-d');
                $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');
                $last7Days = \Carbon\Carbon::today()->subDays(6)->format('Y-m-d');
            @endphp
            <div class="col-md-6 mb-3">
                <div class="btn-group" role="group" id="dateFilterButtons">
                    <button type="button" class="btn btn-primary" data-range="today">{{ __('reports.today') }}</button>
                    <button type="button" class="btn btn-outline-primary"
                        data-range="yesterday">{{ __('reports.yesterday') }}</button>
                    <button type="button" class="btn btn-outline-primary"
                        data-range="last7">{{ __('reports.last_7_days') }}</button>
                </div>
            </div>
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ request()->from_date ?? $today }}"
                            name="from_date" id="fromDate" />

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ request()->to_date ?? $today }}"
                            name="to_date" id="toDate" />

                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.partners') }}</label>
                        <select name="partner" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('reports.select_domain') }}">
                            <option></option>
                            <option value="">{{ __('reports.all') }}</option>
                            @foreach ($partners as $key => $value)
                                <option value="{{ $key }}" @if (@request()->partner == $key) selected @endif>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.code') }}</label>
                        <select name="e_wallet_name[]" class="form-select select2" multiple
                            data-placeholder="{{ __('reports.select_codes') }}" data-allow-clear="true">
                            <option value="BKASH" @if (is_array(request()->e_wallet_name) && in_array('BKASH', request()->e_wallet_name)) selected @endif>
                                {{ __('reports.bkash') }}</option>
                            <option value="NAGAD" @if (is_array(request()->e_wallet_name) && in_array('NAGAD', request()->e_wallet_name)) selected @endif>
                                {{ __('reports.nagad') }}</option>
                            <option value="Rocket" @if (is_array(request()->e_wallet_name) && in_array('Rocket', request()->e_wallet_name)) selected @endif>
                                {{ __('reports.rocket') }}</option>
                        </select>
                    </div>
                </div>


                {{-- <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('reports.transaction_type') }}</label>
                        <select name="transaction_type" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('reports.transaction_type') }}">
                            <option></option>
                            <option value="">{{ __('reports.all') }}</option>
                            <option value="Received Money" @if (@request()->transaction_type == 'Received Money') selected @endif>
                                {{ __('reports.received_money') }}</option>
                            <option value="Send Money" @if (@request()->transaction_type == 'Send Money') selected @endif>
                                {{ __('reports.send_money') }}</option>
                            <option value="Cash Out" @if (@request()->transaction_type == 'Cash Out') selected @endif>
                                {{ __('reports.cash_out') }}</option>

                        </select>
                    </div>
                </div> --}}

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="fas fa-search"></i>{{ __('reports.search') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">

                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <td></td>
                            <td colspan="5">{{ __('reports.nagad') }}</td>
                            <td colspan="5">{{ __('reports.bkash') }}</td>
                            <td colspan="5">{{ __('reports.rocket') }}</td>
                        </tr>
                        <tr>
                            <th scope="col">{{ __('reports.date') }}</th>
                            <th scope="col">{{ __('reports.total_deposit_request') }}</th>
                            <th scope="col">{{ __('reports.total_auto_process') }}</th>
                            <th scope="col">{{ __('reports.total_manual_process') }}</th>
                            <th scope="col">{{ __('reports.total_abandoned') }}</th>
                            <th scope="col">{{ __('reports.success_rate') }}</th>

                            <th scope="col">{{ __('reports.total_deposit_request') }}</th>
                            <th scope="col">{{ __('reports.total_auto_process') }}</th>
                            <th scope="col">{{ __('reports.total_manual_process') }}</th>
                            <th scope="col">{{ __('reports.total_abandoned') }}</th>
                            <th scope="col">{{ __('reports.success_rate') }}</th>

                            <th scope="col">{{ __('reports.total_deposit_request') }}</th>
                            <th scope="col">{{ __('reports.total_auto_process') }}</th>
                            <th scope="col">{{ __('reports.total_manual_process') }}</th>
                            <th scope="col">{{ __('reports.total_abandoned') }}</th>
                            <th scope="col">{{ __('reports.success_rate') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse ($e_combined as $date => $apis)
                            <tr>
                                <td>{{ $date }}</td>


                                @if (isset($apis['nagad']))
                                    @php
                                        $counts = $apis['nagad'];
                                        $fundCount = $counts['fund_count'] ?? 0;
                                        $autoProcessCount = $counts['auto_process_count'] ?? 0;
                                        $manualProcessCount = $counts['manual_process_count'] ?? 0;
                                        $abandoned = $fundCount - ($autoProcessCount + $manualProcessCount);
                                        $successRate =
                                            $fundCount > 0 && $fundCount - $abandoned > 0
                                                ? ($autoProcessCount / ($fundCount - $abandoned)) * 100
                                                : 0;
                                    @endphp


                                    <td>{{ $fundCount }}</td>
                                    <td>{{ $autoProcessCount }}</td>
                                    <td>{{ $manualProcessCount }}</td>
                                    <td>{{ max(0, $abandoned) }}</td> <!-- Ensure no negative values -->
                                    <td>{{ number_format($successRate, 2) }}%</td> <!-- Format success rate -->
                                @else
                                    <td colspan="5" class="text-center">-</td>
                                @endif

                                @if (isset($apis['bkash']))
                                    @php
                                        $counts = $apis['bkash'];
                                        $fundCount = $counts['fund_count'] ?? 0;
                                        $autoProcessCount = $counts['auto_process_count'] ?? 0;
                                        $manualProcessCount = $counts['manual_process_count'] ?? 0;
                                        $abandoned = $fundCount - ($autoProcessCount + $manualProcessCount);
                                        
                                        $successRate =
                                            $fundCount > 0
                                                ? round((($autoProcessCount + $manualProcessCount) / $fundCount) * 100)
                                                : 0;        
                                    @endphp


                                    <td>{{ $fundCount }}</td>
                                    <td>{{ $autoProcessCount }}</td>
                                    <td>{{ $manualProcessCount }}</td>
                                    <td>{{ max(0, $abandoned) }}</td> <!-- Ensure no negative values -->
                                    <td>{{ number_format($successRate, 2) }}%</td> <!-- Format success rate -->
                                @else
                                    <td colspan="5" class="text-center">-</td>
                                @endif


                                @if (isset($apis['rocket']))
                                    @php
                                        $counts = $apis['rocket'];
                                        $fundCount = $counts['fund_count'] ?? 0;
                                        $autoProcessCount = $counts['auto_process_count'] ?? 0;
                                        $manualProcessCount = $counts['manual_process_count'] ?? 0;
                                        $abandoned = $fundCount - ($autoProcessCount + $manualProcessCount);
                                        $successRate =
                                            $fundCount > 0 && $fundCount - $abandoned > 0
                                                ? ($autoProcessCount / ($fundCount - $abandoned)) * 100
                                                : 0;
                                    @endphp


                                    <td>{{ $fundCount }}</td>
                                    <td>{{ $autoProcessCount }}</td>
                                    <td>{{ $manualProcessCount }}</td>
                                    <td>{{ max(0, $abandoned) }}</td> <!-- Ensure no negative values -->
                                    <td>{{ number_format($successRate, 2) }}%</td> <!-- Format success rate -->
                                @else
                                    <td colspan="5" class="text-center">-</td>
                                @endif



                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">@lang('No data available')</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>


                <br>
                <br>
                <br>

                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">{{ __('reports.date') }}</th>
                            <th scope="col">{{ __('reports.partner') }}</th>
                            <th scope="col">{{ __('reports.total_deposit_request') }}</th>
                            <th scope="col">{{ __('reports.total_auto_process') }}</th>
                            <th scope="col">{{ __('reports.total_manual_process') }}</th>
                            <th scope="col">{{ __('reports.total_abandoned') }}</th>
                            <th scope="col">{{ __('reports.success_rate') }}</th>
                            <th scope="col">{{ __('reports.within_10_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_10_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_20_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_30_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_40_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_50_seconds') }}</th>
                            <th scope="col">{{ __('reports.after_1_minute') }}</th>
                            <th scope="col">{{ __('reports.after_5_minutes') }}</th>
                            <th scope="col">{{ __('reports.after_10_minutes') }}</th>
                            <th scope="col">{{ __('reports.action') }}</th>
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
                                            $fundCount > 0
                                                ? round((($autoProcessCount + $manualProcessCount) / $fundCount) * 100)
                                                : 0;
                                    @endphp
                            <tr>
                                <td>{{ $partnerName }}</td>
                                <td>{{ $fundCount }}</td>
                                <td>{{ $autoProcessCount }}</td>
                                <td>{{ $manualProcessCount }}</td>
                                <td>{{ max(0, $abandoned) }}</td> <!-- Ensure no negative values -->
                                 <td style="color:
                                @if($successRate > 80)
                                    #0eee0e
                                @elseif($successRate >= 61)
                                    #ffff00
                                @else
                                    #d61212e3
                                @endif
                            ;">
                                {{ number_format($successRate, 2) }}%
                            </td>
                                <td>{{ $timeLessThan10 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween10And20 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween20And30 }}</td> <!-- Add time-based count -->
                                <td>{{ $timeBetween30And40 }}</td>
                                <td>{{ $timeBetween40And50 }}</td>
                                <td>{{ $timeBetween50And60 }}</td>
                                <td>{{ $timeBetween60And5Minutes }}</td>
                                <td>{{ $timeBetween5And10Minutes }}</td>
                                <td>{{ $time_greater_than_10_minutes }}</td>
                                <td>
                                    <a href="{{ route('admin.payment.payment_gateway_report_detail', ['id' => $api_id, 'from_date' => $date, 'to_date' => $date]) }}"
                                        class="btn btn-success">{{ __('reports.detail') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center">{{ __('reports.no_data_available') }}</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('js')
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fromDateInput = document.getElementById('fromDate');
                const toDateInput = document.getElementById('toDate');
                const buttons = document.querySelectorAll('#dateFilterButtons button');

                const today = new Date();
                const formatDate = (date) => date.toISOString().split('T')[0];

                const setDateRange = (range) => {
                    let fromDate, toDate;

                    switch (range) {
                        case 'today':
                            fromDate = toDate = formatDate(today);
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(today.getDate() - 1);
                            fromDate = toDate = formatDate(yesterday);
                            break;
                        case 'last7':
                            const start = new Date(today);
                            start.setDate(today.getDate() - 6);
                            fromDate = formatDate(start);
                            toDate = formatDate(today);
                            break;
                    }

                    fromDateInput.value = fromDate;
                    toDateInput.value = toDate;

                    // Update button styles
                    buttons.forEach(btn => btn.classList.remove('btn-primary'));
                    buttons.forEach(btn => btn.classList.add('btn-outline-primary'));

                    const activeBtn = document.querySelector(`[data-range="${range}"]`);
                    activeBtn.classList.remove('btn-outline-primary');
                    activeBtn.classList.add('btn-primary');
                };

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        const range = button.getAttribute('data-range');
                        setDateRange(range);
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('reports.processing') }}");

                    // Allow form to proceed
                    return true;
                });
                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush
</x-admin-layout>
