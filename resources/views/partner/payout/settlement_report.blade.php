<x-partner-layout :title="$pageTitle">
    <style>
        td:hover {
            background-color: lightgray;
            cursor: pointer;
        }

        .modal-auto-width {
            max-width: 80%;
        }
    </style>


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-auto-width" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Record Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Content will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>



    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.settlement.report.daily.search') }}" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.from_date_label')</label>
                        <input type="date" class="form-control" value="{{ $from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.to_date_label')</label>
                        <input type="date" class="form-control" value="{{ $to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>@lang('partner_basic.e_wallet_label')</label>
                        <select name="gateway" class="form-control">
                            <option value="">@lang('partner_basic.all_en')</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->source_name }}"
                                    @if (@request()->gateway == $gateway->source_name) selected @endif>{{ $gateway->source_name }}
                                </option>
                            @endforeach
                        </select>
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
            <h3 style="color: #7367f0">{{ __('partner_basic.Daily_Settlement_Report_en')}}</h3>

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">@lang('partner_basic.Date_en')</th>
                            <th scope="col">@lang('partner_basic.Pending_(QTY)_en')</th>
                            <th scope="col">@lang('partner_basic.Pending_Amount_en')</th>
                            <th scope="col">@lang('partner_basic.Approved_(QTY)_en')</th>
                            <th scope="col">@lang('partner_basic.Approved_Amount_en')</th>
                            <th scope="col">@lang('partner_basic.Total_(QTY)_en')</th>
                            <th scope="col">@lang('partner_basic.Total_Amount_en')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settlementsByDate as $key => $settlement)
                            <tr>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'All')">
                                    {{ $settlement->settlement_date }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'Pending')">
                                    {{ $settlement->pending_count }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'Pending')">
                                    {{ $settlement->pending_amount }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'Approved')">
                                    {{ $settlement->complete_count }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'Approved')">
                                    {{ $settlement->complete_amount }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'All')">
                                    {{ $settlement->settlement_count }}</td>
                                <td
                                    onclick="openmodel('{{ $settlement->settlement_date }}', '{{ $gateway }}', 'All')">
                                    {{ $settlement->total_amount }}</td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark">@lang('partner_basic.no_data_found')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @push('js')
        <script>
            function openmodel(date, gateway, status) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Ajax request to fetch data
                $.ajax({
                    url: "{{ route('partner.settlement.report.detail') }}",
                    method: 'POST',
                    data: {
                        date: date,
                        gateway: gateway,
                        status: status,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log(response);
                        $('#modalContent').empty();

                        // Iterate over the response data and append it to the modal body in a table format
                        var table = $('<table class="table"></table>');
                        var thead = $(
                            '<thead class="thead-dark"><tr><th>Source</th><th>Source Name</th><th>Account No</th><th>Amount</th><th>Charges</th><th>Net Amount</th><th>Status</th><th>Created At</th><th>Updated At</th></tr></thead>'
                            );
                        var tbody = $('<tbody></tbody>');

                        // Assuming response is an array
                        for (var i = 0; i < response.length; i++) {
                            var row = $('<tr></tr>');

                            row.append('<td>' + response[i].source + '</td>');
                            row.append('<td>' + response[i].source_name + '</td>');
                            row.append('<td>' + response[i].account_no + '</td>');
                            row.append('<td>' + response[i].amount + '</td>');
                            row.append('<td>' + response[i].charges + '</td>');
                            row.append('<td>' + response[i].net_amount + '</td>');
                            var statusBadge;
                            if (response[i].status == 2) {
                                statusBadge =
                                    '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> Rejected</span>';
                            } else if (response[i].status == 1) {
                                statusBadge =
                                    '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> Approved</span>';
                            } else {
                                statusBadge =
                                    '<span class="badge badge-light"><i class="fa fa-circle text-warning success font-12"></i> Pending</span>';
                            }

                            row.append('<td>' + statusBadge + '</td>');
                            var createdAt = new Date(response[i].created_at).toLocaleString('en-GB', {
                                day: 'numeric',
                                month: 'numeric',
                                year: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true
                            });
                            var updatedAt = new Date(response[i].updated_at).toLocaleString('en-GB', {
                                day: 'numeric',
                                month: 'numeric',
                                year: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true
                            });

                            row.append('<td>' + createdAt + '</td>');
                            row.append('<td>' + updatedAt + '</td>');

                            tbody.append(row);
                        }

                        table.append(thead);
                        table.append(tbody);
                        $('#modalContent').append(table);

                        // Show the modal
                        $('#myModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        </script>
    @endpush
</x-partner-layout>
