@extends('partner.layouts.app')
@section('title')
    @lang($page_title)
@endsection
@section('content')
    <h1 class="text-center">
        <span class="badge badge-primary">Settlementable Amount: <b>{{ $settlementable_amount }} SGD</b></span>
    </h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('partner.settlements.search') }}" method="get">
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

                <!--<div class="col-md-4">-->
                <!--    <div class="form-group">-->
                <!--        <label>User Account No</label>-->
                <!--        <input type="text" class="form-control" value="{{ @request()->account_no }}" name="account_no"/>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Bank</label>
                        <select name="gateway" class="form-control select2">
                            <option value="">All</option>
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->source_name }}"
                                    @if (@request()->gateway == $gateway->source_name) selected @endif>{{ $gateway->source_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control select2">
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
                                class="fas fa-search"></i> @lang('Search')</button>
                    </div>
                </div>

            </div>
        </form>

    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">

                    <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2" data-target="#newModal"
                        data-toggle="modal">
                        <span><i class="fa fa-plus-circle"></i> @lang('Add New')</span>
                    </a>

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col">@lang('Source')</th>
                                    <th scope="col">@lang('Source Name')</th>
                                    <th scope="col">@lang('Acc Holder Name')</th>
                                    <th scope="col">@lang('Account No.')</th>
                                    <th scope="col">@lang('Amount')</th>
                                    <th scope="col">@lang('Charges')</th>
                                    <th scope="col">@lang('Net Amount')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                    <tr>
                                        <td>{{ $item->source }}</td>
                                        <td>{{ $item->source_name }}</td>
                                        <td>{{ $item->account_holder_name }}</td>
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
                                        <td>{{ $item->created_at }}</td>
                                        <!--<td data-label="@lang('Action')">-->
                                        <!--    <div class="dropdown show ">-->
                                        <!--        <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown"-->
                                        <!--           aria-haspopup="true" aria-expanded="false">-->
                                        <!--            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>-->
                                        <!--        </a>-->
                                        <!--        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">-->
                                        <!--            <form action="{{ route('admin.apis.delete', $item['id']) }}" method="POST">-->
                                        <!--                @csrf-->
                                        <!--                @method('DELETE')-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-trash"></i> Delete</button>-->
                                        <!--            </form>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#editModal{{ $item['id'] }}">-->
                                        <!--                <i class="fa fa-edit"></i> Edit-->
                                        <!--            </button><br>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#newModalb" onclick="setBalanceItem({{ $item['id'] }})">-->
                                        <!--                <i class="fa fa-money-bill"></i> Add Balance-->
                                        <!--            </button>-->
                                        <!--            <form action="{{ route('admin.apis.reset', $item['id']) }}" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-key"></i> Reset QR Code</button>-->
                                        <!--            </form>-->
                                        <!--            <form action="{{ route('admin.apis.commission', $item['id']) }}" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-calculator"></i> Commission %</button>-->
                                        <!--            </form>-->
                                        <!--        </div>-->
                                        <!--    </div>-->
                                        <!--</td>-->


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


    <div id="newModal" class="modal fade show" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Add New Settlement')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="settlement-form" action="{{ route('partner.settlements.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Currency</label>
                                    <select class="form-control" name="currency" id="currency-select" required>
                                        <option value="SGD" @if (old('currency') == 'SGD') selected @endif>SGD
                                        </option>
                                        <option value="MYR" @if (old('currency') == 'MYR') selected @endif>MYR
                                        </option>
                                        <option value="USDT" @if (old('currency') == 'USDT') selected @endif>USDT
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                </div>
                            </div>

                            <!-- Bank Details Section -->
                            <div id="bank-details" class="col-md-12 bank-details" style="display: none;">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name"
                                        value="{{ old('bank_name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>Account Holder Name</label>
                                    <input type="text" class="form-control" name="account_holder_name"
                                        value="{{ old('account_holder_name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" class="form-control" name="account_number"
                                        value="{{ old('account_number') }}" required />
                                </div>
                            </div>

                            <!-- USDT Address Section -->
                            <div id="usdt-details" class="col-md-12 usdt-details" style="display: none;">
                                <div class="form-group">
                                    <label>USDT Address</label>
                                    <input type="text" class="form-control" name="usdt_address"
                                        value="{{ old('usdt_address') }}" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit-button" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function(e) {
            $('#image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            $('.select2').select2({
                selectOnClose: true
            });

            $('#settlement-form').on('submit', function(e) {
                // Disable the submit button
                $('#submit-button').prop('disabled', true).text('Submitting...');

                // Submit the form using AJAX
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#newModal').modal('hide');
                            alert(response.message); // Show success message
                            location.reload(); // Optionally reload the page or update UI
                        } else {
                            alert(response.message); // Show error message
                        }
                    },
                    error: function(xhr) {
                        let response = JSON.parse(xhr.responseText);
                        alert(response.error ||
                            'An unexpected error occurred. Please try again.');
                    },
                    complete: function() {
                        $('#submit-button').prop('disabled', false).text('Save');
                    },
                });
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            const currencySelect = document.getElementById('currency-select');
            const bankDetails = document.getElementById('bank-details');
            const usdtDetails = document.getElementById('usdt-details');

            const bankNameInput = document.querySelector('input[name="bank_name"]');
            const accountHolderNameInput = document.querySelector('input[name="account_holder_name"]');
            const accountNumberInput = document.querySelector('input[name="account_number"]');
            const usdtAddressInput = document.querySelector('input[name="usdt_address"]');

            // Function to toggle visibility and set required attributes based on the selected currency
            function toggleDetails() {
                const selectedCurrency = currencySelect.value;

                console.log("Selected currency:", selectedCurrency);

                if (selectedCurrency === 'SGD' || selectedCurrency === 'MYR') {
                    bankDetails.style.display = 'block';
                    usdtDetails.style.display = 'none';

                    // Set required attributes for bank details
                    bankNameInput.required = true;
                    accountHolderNameInput.required = true;
                    accountNumberInput.required = true;

                    // Remove required attribute for USDT address
                    usdtAddressInput.required = false;
                } else if (selectedCurrency === 'USDT') {
                    bankDetails.style.display = 'none';
                    usdtDetails.style.display = 'block';

                    // Set required attribute for USDT address
                    usdtAddressInput.required = true;

                    // Remove required attributes for bank details
                    bankNameInput.required = false;
                    accountHolderNameInput.required = false;
                    accountNumberInput.required = false;
                } else {
                    // Hide both sections and remove all required attributes
                    bankDetails.style.display = 'none';
                    usdtDetails.style.display = 'none';

                    bankNameInput.required = false;
                    accountHolderNameInput.required = false;
                    accountNumberInput.required = false;
                    usdtAddressInput.required = false;
                }
            }

            // Initialize visibility and required attributes on page load
            toggleDetails();

            // Update visibility and required attributes on currency change
            currencySelect.addEventListener('change', toggleDetails);
        });
    </script>
@endpush
