<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        tr th {
            color: white !important
        }
    </style>
    @endpush

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
        <form action="{{ route('admin.settlements.search') }}" method="get">
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="{{@request()->from_date}}" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="{{@request()->to_date}}" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Partner</label>
                        <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select a partner">
                            <option></option>
                            <option value="">All</option>
                            @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id) selected
                                @endif>{{ $partner->website }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="col-md-4  mt-4">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">All</option>
                            @foreach($gateways as $gateway)
                            <option value="{{ $gateway->source_name }}" @if(@request()->gateway ==
                                $gateway->source_name) selected @endif>{{ $gateway->source_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4  mt-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="">@lang('All')</option>
                            <option value="1" @if(@request()->status == '1') selected @endif>@lang('Approved')</option>
                            <option value="0" @if(@request()->status == '0') selected @endif>@lang('Pending')</option>
                            <option value="2" @if(@request()->status == '2') selected @endif>@lang('Rejected')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-3">
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

                    <button type="button" class="btn btn-primary mb-4 hover:drop-shadow-xl" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        Add New Settlement
                    </button>

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered table-sm">
                            <thead class="thead-dark text-warning"
                                style="background: var(--bs-menu-active-bg); color:#ffffff;">
                                <tr>

                                    <th scope="col">@lang('Source')</th>
                                    <th scope="col">@lang('Source Name')</th>
                                    <th scope="col">@lang('Account No.')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charges')</th>
                                    <th scope="col">@lang('Net Amount')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Partner')</th>
                                    <th scope="col">Created At</th>
                                    <th>Action</th>
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
                                        <span class="badge  bg-danger">
                                            {{-- <i class="fa fa-circle text-white font-12"></i> --}}
                                            @lang('Rejected')
                                        </span>
                                        @elseif($item->status == 1)
                                        <span class="badge bg-success">
                                            {{-- <i class="fa fa-circle text-white font-12"></i> --}}
                                            @lang('Approved')</span>
                                        @else
                                        <span class="badge bg-warning">
                                            {{-- <i class="fa fa-circle text-white font-12"></i> --}}
                                            @lang('Pending')</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->api->website ?? '' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td data-label="@lang('Action')">
                                        <div class="dropdown show ">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="icon-base ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                @if(adminAccessRoute(config('role.settlements.access.edit')))
                                                <form action="{{ route('admin.settlements.approve', $item['id']) }}"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                            class="fa fa-check"></i> Approve</button>
                                                </form>
                                                <form action="{{ route('admin.settlements.reject', $item['id']) }}"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                            class="fa fa-times"></i> Reject</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

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
                    <div class="card-footer">
                        {{ $records->appends($_GET)->links('partials.pagination') }}
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Add Settlement Mode     --}}
    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="settlementForm" action="{{ route('admin.settlements.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Partner</label>
                                    <select name="partner" class="form-select" required>
                                        @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}" @if(@request()->partner == $partner->id)
                                            selected @endif>{{ $partner->website }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger error-partner"></div>
                                </div>
                            </div>

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
                                    <div class="text-danger error-amount"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

    <script>
        "use strict";
        $(document).ready(function(e) {
            $('#settlementForm').on('submit', function (e) {
                e.preventDefault();
                let form = $(this);
                let submitBtn = $('#submitBtn');

                // Clear all previous errors
                $('.text-danger').text('');

                // Disable button and show processing text
                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (response) {
                        $('#newModal').modal('hide');
                        location.reload(); // or show a success message
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                $('.error-' + key).text(messages[0]);
                            });
                        } else {
                            alert('Something went wrong.');
                        }
                    },
                    complete: function () {
                        // Re-enable button and reset text to Save
                        submitBtn.prop('disabled', false).text('Save');
                    }
                });
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
