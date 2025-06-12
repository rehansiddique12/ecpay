<x-admin-layout :title="$pageTitle">
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.payment.report.all.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('transaction.from_date') }}</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('transaction.to_date') }}</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('transaction.e_wallet') }}</label>
                        <select name="gateway" class="form-select">
                            <option value="">{{ __('transaction.all') }}</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->name }}" @if (@request()->gateway == $gateway->name) selected @endif>
                                    {{ $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('transaction.source') }}</label>
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="{{ __('transaction.select_source') }}">
                            <option></option>
                            <option value="">{{ __('transaction.all_source') }}</option>
                            @foreach ($domains as $partner)
                                <option value="{{ $partner->id }}" @if (@request()->website == $partner->id) selected @endif>
                                    {{ $partner->name }} ===> ( {{ $partner->website }} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="fas fa-search"></i> {{ __('transaction.search') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>



    @php
        $gateway = 'All';
        if (!empty(@request()->gateway)) {
            $gateway = @request()->gateway;
        }
    @endphp
    <!-- Add these lines to your HTML header section -->
    <link rel="stylesheet" href="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css') }}">
    <script src="{{ asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-striped table-bordered">
                    <thead class="bg bg-primary text-white">
                        <tr>
                            <th></th>
                            <th colspan="6" class="bg bg-primary text-white">{{ __('transaction.deposit') }}</th>
                            <th colspan="6" class="bg bg-success text-white">{{ __('transaction.withdrawal') }}</th>
                        </tr>
                    </thead>

                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">{{ __('transaction.date') }}</th>
                            <th scope="col">{{ __('transaction.pending_qty') }}</th>
                            <th scope="col">{{ __('transaction.pending_amount') }}</th>
                            <th scope="col">{{ __('transaction.approved_qty') }}</th>
                            <th scope="col">{{ __('transaction.approved_amount') }}</th>
                            <th scope="col">{{ __('transaction.total_qty') }}</th>
                            <th scope="col">{{ __('transaction.total_amount') }}</th>

                            <th scope="col">{{ __('transaction.pending_qty') }}</th>
                            <th scope="col">{{ __('transaction.pending_amount') }}</th>
                            <th scope="col">{{ __('transaction.approved_qty') }}</th>
                            <th scope="col">{{ __('transaction.approved_amount') }}</th>
                            <th scope="col">{{ __('transaction.total_qty') }}</th>
                            <th scope="col">{{ __('transaction.total_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $value)
                            <tr>
                                <td> {{ isset($value['date']) ? $value['date'] : '' }}</td>

                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Pending']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_pending_count']) ? $value['payment_pending_count'] : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Pending']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_pending_amount']) ? getAmount($value['payment_pending_amount'], 2) : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Complete']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_complete_count']) ? $value['payment_complete_count'] : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Complete']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_complete_amount']) ? getAmount($value['payment_complete_amount'], 2) : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'All']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_count']) ? $value['payment_count'] : '' }}</td>
                                <td onclick="window.location='{{ route('admin.payment.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'All']) }}';"
                                    class="bg bg-primary text-white">
                                    {{ isset($value['payment_total_amount']) ? getAmount($value['payment_total_amount'], 2) : '' }}
                                </td>

                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Pending']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_pending_count']) ? $value['payout_pending_count'] : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Pending']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_pending_amount']) ? getAmount($value['payout_pending_amount'], 2) : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Complete']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_complete_count']) ? $value['payout_complete_count'] : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'Complete']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_complete_amount']) ? getAmount($value['payout_complete_amount'], 2) : '' }}
                                </td>
                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'All']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_count']) ? $value['payout_count'] : '' }}</td>
                                <td onclick="window.location='{{ route('admin.payout.report.detail', ['date' => $value['date'], 'gateway' => $gateway, 'status' => 'All']) }}';"
                                    class="bg bg-success text-white">
                                    {{ isset($value['payout_total_amount']) ? getAmount($value['payout_total_amount'], 2) : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">{{ __('transaction.no_data_found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    @endpush

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html(
                        "<i class='fa fa-spinner fa-spin me-1'></i> {{ __('transaction.processing') }}");

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
</x-admin-layout>
