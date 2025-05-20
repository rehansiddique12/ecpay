<x-admin-layout :title="$pageTitle">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .year-only .ui-datepicker-month {
            display: none;
            /* Hide month dropdown */
        }

        .year-only .ui-datepicker-calendar {
            display: none;
            /* Hide calendar grid */
        }
    </style>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.merchant_reports.by_month') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row align-items-left">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Year</label>
                        <input type="text" name="searchYear" id="yearpicker" class="form-control"
                            placeholder="Select Year" readonly />
                    </div>
                </div>


                <input type="hidden" name="search" value="Yes">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Merchants</label>
                        <select  class="form-select select2" name="transfer_to1" data-allow-clear="true" data-placeholder="Select To Account">
                                    <option></option>
                            <option value="">Select Merchant</option>
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
                                class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        {{-- <button type="button" class="btn waves-effect waves-light btn-success"><i class="fas fa-share"></i> @lang('Export')</button> --}}
                        <a href="{{ route('admin.merchant_reports.export_by_month', ['from_date' => $from_date]) }}"
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
                                <tr class="text-center">
                                    <th rowspan="2">Month</th>
                                    <th rowspan="2">Merchant Name</th>
                                    <th colspan="3">Deposit</th>
                                    <th colspan="3">Withdrawal</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>No. Transaction</th>
                                    <th>Total Amount</th>
                                    <th>Commission</th>
                                    <th>No. Transaction</th>
                                    <th>Total Withdrawal</th>
                                    <th>Commission</th>
                                    <th>Total Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($results)
                                    @php
                                        $previousMonth = null;
                                    @endphp
                                    @forelse($results as $key => $result)
                                        <tr>
                                            <td>
                                                @if ($previousMonth !== $result->month)
                                                    {{ $months[$result->month] }}
                                                    @php
                                                        $previousMerchant = $result->month;
                                                    @endphp
                                                @endif
                                            </td>
                                            <td>{{ $apis[$result->api_id] }}</td>
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
                                                <p class="text-dark">@lang('No Data Found')</p>
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
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function() {
                // Calculate the year range dynamically
                const currentYear = new Date().getFullYear(); // Get the current year
                const startYear = currentYear - 100; // Start year (100 years back)

                $('#yearpicker').datepicker({
                    dateFormat: 'yy', // Display only the year (use 'yy' for a 4-digit year)
                    changeMonth: false, // Disable month selection
                    changeYear: true, // Enable year dropdown
                    showButtonPanel: true, // Show "Done" button
                    yearRange: `${startYear}:${currentYear}`, // Dynamic year range
                    defaultDate: new Date(currentYear, 0, 1), // Set default date to the current year
                    onClose: function(dateText, inst) {
                        // Get the selected year
                        const year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).val(year); // Set it in the input field
                    },
                    beforeShow: function(input, inst) {
                        // Ensure only years are displayed
                        $("#ui-datepicker-div").addClass("year-only");
                    }
                });

                // Optionally, pre-fill the input with the current year
                $('#yearpicker').val(currentYear);
            });
        </script>
    @endpush

    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    @endpush

    @push('js')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
    $(document).ready(function () {
        $('#category').change(function () {
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

        $('form').on('submit', function () {
            const $btn = $(this).find('button[type="submit"]');
            // Disable the button
            $btn.prop('disabled', true);
            // Optional: Change button text to show loading spinner
            $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Submitting...');
            return true; // allow form to submit
        });
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
