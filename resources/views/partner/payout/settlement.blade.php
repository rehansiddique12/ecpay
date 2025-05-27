<x-partner-layout :title="$pageTitle">
    <h1 class="text-center">
        <span class="badge badge-primary">Settlementable Amount: <b>{{ $settlementable_amount }} TK</b></span>
    </h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.settlements.search') }}" method="get">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-5">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{ @request()->from_date }}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{ @request()->to_date }}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-2"></div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">@lang('All')</option>
                            @foreach ($gateways as $gateway)
                            <option value="{{ $gateway->source_name }}" @if (@request()->gateway ==
                                $gateway->source_name) selected @endif>{{ $gateway->source_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="all">@lang('All')</option>
                            <option value="1" @if (@request()->status == '1') selected @endif>@lang('Approved')
                            </option>
                            <option value="0" @if (@request()->status == '0') selected @endif>@lang('Pending')
                            </option>
                            <option value="2" @if (@request()->status == '2') selected @endif>@lang('Rejected')
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> @lang('Search')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-primary mr-2 mb-3" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        <span>@lang('Add New')</span>
                    </button>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">@lang('Source')</th>
                                    <th scope="col">@lang('Source Name')</th>
                                    <th scope="col">@lang('Account No.')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charges')</th>
                                    <th scope="col">@lang('Net Amount')</th>
                                    <th scope="col" class="text-center">@lang('Status')</th>
                                    <th scope="col">Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                <tr>
                                    <td>{{ $item->source }}</td>
                                    <td>{{ $item->source_name }}</td>
                                    <td>{{ $item->account_no }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->charges }}</td>
                                    <td>{{ $item->net_amount }}</td>
                                    <td data-label="@lang('Status')" class="text-lg-center text-right">
                                        @if ($item->status == 2)
                                        <span class="badge badge-light">
                                            <i class="fa fa-circle text-danger danger font-12"></i>
                                            @lang('Rejected') </span>
                                        @elseif($item->status == 1)
                                        <span class="badge badge-light">
                                            <i class="fa fa-circle text-success success font-12"></i>
                                            @lang('Approved')</span>
                                        @else
                                        <span class="badge badge-light">
                                            <i class="fa fa-circle text-warning success font-12"></i>
                                            @lang('Pending')</span>
                                        @endif
                                    </td>
                                    <td>{{ convertToUserTimezone($item->created_at) }}</td>

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





    {{-- New MODAL --}}
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="newModalLabel">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSettlementForm" action="{{ route('partner.settlements.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-select" name="source" required>
                                        <option value="Bank">Bank</option>
                                        <option value="EWallet">EWallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source Name</label>
                                    <input type="text" class="form-control" name="source_name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account No.</label>
                                    <input type="text" class="form-control" name="account_no" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveCategoryBtn" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        "use strict";

            $(document).ready(function() {
                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    // selectOnClose: true,
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

                $('#addSettlementForm').on('submit', function (e) {
                    e.preventDefault();

                    let form = $(this);
                    let url = form.attr('action');
                    let formData = form.serialize();

                    let btn = $('#saveCategoryBtn');
                    // Clear previous errors
                    $('.error-text').text('');
                    // Disable button and show spinner
                    btn.prop('disabled', true).text('Saving...');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function (response) {

                            if (response.success) {
                                // Close modal, reset form, show success message
                                $('#newModal').modal('hide');
                                form[0].reset();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message, // Use the message from the response
                                    timer: 2000, // Auto-close after 2 seconds
                                    showConfirmButton: false,
                                    didClose: () => {
                                        location.reload(); // Reload page after alert closes
                                    }
                                });
                            }

                        },
                        error: function(response) {
                            var errors = response.responseJSON.errors;
                            var firstErrorField = null; // Track the first error field

                            // Loop through errors and show them
                            $.each(errors, function (key, value) {
                                // Show error next to each field
                                $('.' + key + '_error').text(value[0]);

                                // Find the first field with an error and focus on it
                                var $field = $('.' + key); // Find the field by class

                                // Only set firstErrorField if it hasn't been set already
                                if (!firstErrorField && $field.length) {
                                    firstErrorField = $field; // Set the first error field
                                }
                            });
                        },
                        complete: function() {
                            btn.prop('disabled', false).text('Save');
                        }
                    });
                });

            });
    </script>
    @endpush
</x-partner-layout>
