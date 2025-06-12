<x-admin-layout :title="$pageTitle">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.merchant_reports.by_name') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row align-items-left">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('merchant_reports.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('merchant_reports.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <input type="hidden" name="search" value="Yes">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('merchant_reports.merchants') }}</label>
                        <select class="form-select select2" name="merchant" data-allow-clear="true"
                            data-placeholder="{{ __('merchant_reports.select_to_account') }}">
                            <option></option>
                            <option value="">{{ __('merchant_reports.select_merchant') }}</option>
                            @foreach ($apis as $key => $val)
                                <option value="{{ $key }}" @if (@request()->merchant == $key) selected @endif>
                                    {{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i>
                            {{ __('merchant_reports.search') }}</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="{{ route('admin.merchant_reports.export_by_name', ['from_date' => $from_date]) }}"
                            class="btn waves-effect waves-light btn-success" id="exportButton">
                            <i class="icon-base ti tabler-download me-1"></i> {{ __('merchant_reports.export') }}
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
                    @if (isset($totalSummary))
                        <div>
                            <h4 class="text-bold">{{ __('merchant_reports.summary') }}</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td>{{ __('merchant_reports.total_deposit_transactions') }}:</td>
                                    <td>{{ number_format($totalSummary->total_deposit_transactions, 0) }}</td>
                                    <td>{{ __('merchant_reports.total_amount') }}:</td>
                                    <td>{{ number_format($totalSummary->total_deposit, 2) }}</td>
                                    <td>{{ __('merchant_reports.commission') }}:</td>
                                    <td>{{ number_format($totalSummary->total_deposit_commission, 2) }}</td>
                                    <td>{{ __('merchant_reports.total_commission') }}:</td>
                                    <td>{{ number_format($totalSummary->total_commission, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('merchant_reports.total_withdrawal_transactions') }}:</td>
                                    <td>{{ number_format($totalSummary->total_withdrawal_transactions, 0) }}</td>
                                    <td>{{ __('merchant_reports.total_amount') }}:</td>
                                    <td>{{ number_format($totalSummary->total_withdrawal, 0) }}</td>
                                    <td>{{ __('merchant_reports.commission') }}:</td>
                                    <td>{{ number_format($totalSummary->total_withdrawal_commission, 0) }}</td>
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
                                    <th rowspan="2">{{ __('merchant_reports.merchant_name') }}</th>
                                    <th rowspan="1"></th>
                                    <th colspan="3">{{ __('merchant_reports.deposit') }}</th>
                                    <th colspan="3">{{ __('merchant_reports.withdrawal') }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>{{ __('merchant_reports.date') }}</th>
                                    <th>{{ __('merchant_reports.no_of_transactions') }}</th>
                                    <td>{{ __('merchant_reports.total_amount') }}</td>
                                    <td>{{ __('merchant_reports.commission') }}</td>
                                    <th>{{ __('merchant_reports.no_of_transactions') }}</th>
                                    <th>{{ __('merchant_reports.total_withdrawal_amount') }}</th>
                                    <td>{{ __('merchant_reports.commission') }}</td>
                                    <th>{{ __('merchant_reports.total_commission') }}</th>
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
                                                {{-- {{$apis[$result->api_id]}} --}}
                                            </td>
                                            <td>{{ $result->date }}</td>
                                            <td>{{ number_format($result->total_deposit_transactions, 0) }}</td>
                                            <td>{{ number_format($result->total_deposit, 2) }}</td>
                                            <td>{{ number_format($result->total_charges_deposit, 2) }}</td>
                                            <td>{{ number_format($result->total_withdrawal_transactions, 0) }}</td>
                                            <td>{{ number_format($result->total_withdrawal, 2) }}</td>
                                            <td>{{ number_format($result->total_charges_withdrawal, 2) }}</td>
                                            <td>{{ number_format($result->total_commission, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <p class="text-dark">{{ __('merchant_reports.no_data_found') }}</p>
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


    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#category').change(function() {
                    var selectedCategory = $(this).val();

                    if (selectedCategory === 'Bank to E-wallet') {
                        // Show fromtransfer2 and hide fromtransfer1
                        $('#fromtransfer2').show();
                        $('#fromtransfer1').hide();

                        // Show totransfer1 and hide totransfer2
                        $('#totransfer1').show();
                        $('#totransfer2').hide();
                    } else if (selectedCategory === 'E-wallet to Bank') {
                        // Show fromtransfer1 and hide fromtransfer2
                        $('#fromtransfer1').show();
                        $('#fromtransfer2').hide();

                        // Show totransfer2 and hide totransfer1
                        $('#totransfer2').show();
                        $('#totransfer1').hide();
                    } else if (selectedCategory === 'E-wallet to E-wallet') {
                        // Show fromtransfer1 and hide fromtransfer2
                        $('#fromtransfer1').show();
                        $('#fromtransfer2').hide();

                        // Show totransfer1 and hide totransfer2
                        $('#totransfer1').show();
                        $('#totransfer2').hide();
                    }
                });

                $('form').on('submit', function() {
                    const $btn = $(this).find('button[type="submit"]');
                    // Disable the button
                    $btn.prop('disabled', true);
                    // Optional: Change button text to show loading spinner
                    $btn.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('merchant_reports.searching') }}"
                    );
                    return true; // allow form to submit
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


</x-admin-layout>
