<x-admin-layout :title="$pageTitle">
    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        #currency-wrapper {
            white-space: nowrap;
        }
    </style>

    @endpush
    @php
    $currentRoute = Route::currentRouteName();
    @endphp
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.account_group') }}" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.gateway') }}" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="row">
                <h6>Create Account In Batch
                </h6>
                <form method="post" action="{{ route('admin.accounts.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ trans('Category Name') }}</label>
                            <select class="form-select" name="category_id" id="category-select">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4 col-4">
                            <label>{{ trans('Select Account Name') }}</label>

                            <div class="input-group">
                                <select class="form-select" name="account_id" id="account-select">
                                    <option value="">Select Account Name</option>
                                </select>
                                <span class="input-group-text" id="currency-wrapper" style="display: none;">
                                    <span id="currency-code"></span>
                                </span>
                            </div>

                            @error('account_id')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <hr style="border-top: 1px solid white;">
                    <h6 class="mb-0">{{ trans(' CONFIGURATION') }}</h6>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="daily_limit"
                                value="{{ old('daily_limit') }}">

                            @error('daily_limit')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 col-6">
                            <label>Daily Withdrawal Amount Limit </label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal"
                                value="{{ old('daily_limit_withdrawal') }}">

                            @error('daily_limit_withdrawal')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit"
                                value="{{ old('monthly_limit') }}">

                            @error('monthly_limit')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>Monthly Withdrawal Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                value="{{ old('monthly_limit_withdrawal') }}">

                            @error('monthly_limit_withdrawal')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_limit_transaction"
                                value="{{ old('daily_limit_transaction') }}" required>
                            @error('daily_limit_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Daily Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal_transaction"
                                value="{{ old('daily_limit_withdrawal_transaction') }}" required>
                            @error('daily_limit_withdrawal_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_transaction"
                                value="{{ old('monthly_limit_transaction') }}" required>
                            @error('monthly_limit_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal_transaction"
                                value="{{ old('monthly_limit_withdrawal_transaction') }}" required>
                            @error('monthly_limit_withdrawal_transaction')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Max Transaction Per Minute</label>
                            <input type="number" class="form-control" name="max_transaction_per_minute"
                                value="{{ old('max_transaction_per_minute') }}" required>
                            @error('max_transaction_per_minute')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Max Amount Per Minute</label>
                            <input type="number" class="form-control" name="max_amount_per_minute"
                                value="{{ old('max_amount_per_minute') }}" required>
                            @error('max_amount_per_minute')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <hr style="border-top: 1px solid white;">
                    <div class="row">
                        <div class="form-group col-md-12 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">{{ trans('Time Configuration') }}</h6>
                                <div>
                                    <input type="checkbox" id="check_all_slots" class="form-check-input">
                                    <label for="check_all_slots" class="form-check-label text-white">Check All</label>
                                </div>
                            </div>

                            @php
                            $start = strtotime('00:00');
                            $end = strtotime('24:00');
                            $i = 0;
                            $slots = [];

                            for ($time = $start; $time < $end; $time +=1800) { $from=date('H:i', $time); $to=date('H:i',
                                $time + 1800); $label="$from - $to" ; $slots[]=$label; } $chunks=array_chunk($slots,
                                ceil(count($slots) / 6)); // 6 columns @endphp <div class="row">
                                @foreach ($chunks as $column)
                                <div class="col-md-2 col-sm-4 col-6">
                                    @foreach ($column as $slot)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="time_slots[]"
                                            value="{{ $slot }}" id="slot_{{ $i }}">
                                        <label class="form-check-label text-white" for="slot_{{ $i }}">
                                            {{ $slot }}
                                        </label>
                                    </div>
                                    @php $i++; @endphp
                                    @endforeach
                                </div>
                                @endforeach
                        </div>
                    </div>
            </div>

            <hr style="border-top: 1px solid white;">
            <div class="row">
                <h6>{{ trans('THRESHOLD ALERT') }}</h6>
                <div class="form-group col-md-3 col-3">
                    <label>Daily Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="deposit_daily_limit_percentage"
                        value="{{ old('deposit_daily_limit_percentage', 100) }}" required>

                    @error('deposit_daily_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Daily Withdrawl Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="withdrawal_daily_limit_percentage"
                        value="{{ old('withdrawal_daily_limit_percentage', 100) }}" required>

                    @error('withdrawal_daily_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" name="deposit_monthly_limit_percentage" min="1" max="100"
                        value="{{ old('deposit_monthly_limit_percentage', 100) }}" required>

                    @error('deposit_monthly_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Withdrawal Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100"
                        name="withdrawal_monthly_limit_percentage"
                        value="{{ old('withdrawal_monthly_limit_percentage', 100) }}" required>

                    @error('withdrawal_monthly_limit_percentage')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Low Balance Alert Amount</label>
                    <input type="number" class="form-control" name="low_balance_amount" min="1"
                        value="{{ old('low_balance_amount', 100) }}" Waalaikum salam>

                    @error('low_balance_amount')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <hr>
            <div class="col-12 mb-3">
                <h6>{{ __('Add Account') }}</h6>
            </div>
            <div id="inputGroupContainer">

            </div>



            <!-- More Button -->
            <div class="mt-3">
                <button type="button" id="addMoreBtn" class="btn btn-primary">+ More</button>
            </div>

            <div class="row mt-3 justify-content-between">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <div class="form-check form-switch d-flex align-items-center">
                            <span id="disableText" class="me-12 text-primary">@lang('No')</span>
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="1">
                            <span id="enableText" class="ms-2 text-secondary">@lang('Yes')</span>
                        </div>
                    </div>
                    <button type="submit" class="btn  btn-primary btn-block mt-3">@lang('Save Changes')</button>

                </div>
            </div>


            </form>
        </div>

    </div>
    </div>

    <!-- Hidden template for cloning -->
    <div id="rowTemplate" style="display:none;">
        <div class="row input-group-row">
            <div class="form-group col-md-2 col-12">
                <label>Account Name</label>
                <input type="text" name="e_wallet_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Device Name</label>
                <input type="text" name="device_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Account Number</label>
                <input type="text" name="account_number[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label for="">Account Group</label>
                <select class="form-select select2" name="account_group[__INDEX__][]" multiple data-placeholder="Select Groups" data-allow-clear="true">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>Type</label>
                <select name="account_type[]" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Agent">Agent</option>
                    <option value="Merchant">Merchant</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>In/Out</label>
                <select name="in_out[]" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Deposit">Deposit</option>
                    <option value="Withdrawal">Withdrawal</option>
                    <option value="Both">Both</option>
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Location</label>
                <select name="location[]" class="form-select select2" data-placeholder="Select Location"
                    data-allow-clear="true">
                    <option></option>
                    <option value="">@lang('Select Location')</option>
                    @foreach($users_locations as $location)
                    <option value="{{ $location->id }}">{{ $location->location }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>QR</label>
                <input type="file" name="image[]" class="form-control qr-file" accept="image/png, image/jpeg">
            </div>

            <div class="form-group col-md-1 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-btn">Remove</button>
            </div>
        </div>
    </div>

    @push('js')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script>
        $(document).ready(function() {
        let rowIndex = 0;

        // Add first row on page load
        addNewRow();

        // Add more button functionality
        $('#addMoreBtn').click(function() {
            addNewRow();
        });

        // Remove button functionality
        $(document).on('click', '.remove-btn', function() {
            if ($('#inputGroupContainer .input-group-row').length > 1) {
                $(this).closest('.input-group-row').remove();
            } else {
                alert("You need at least one row");
            }
        });

        function addNewRow() {
            // Get HTML from template
            let rowHtml = $('#rowTemplate').html();

            // Replace __INDEX__ with current index
            rowHtml = rowHtml.replace(/__INDEX__/g, rowIndex);

            // Convert HTML to jQuery object
            let $clone = $(rowHtml);

            // Generate unique IDs if needed
            let timestamp = Date.now();
            $clone.find('[id]').each(function() {
                let newId = $(this).attr('id') + '_' + timestamp;
                $(this).attr('id', newId);
            });

            // Append to container
            $('#inputGroupContainer').append($clone);

            // Initialize Select2
            $clone.find('.select2').select2({
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                allowClear: true
            });

            // Hide remove button if it's the first row
            if ($('#inputGroupContainer .input-group-row').length === 1) {
                $clone.find('.remove-btn').hide();
            } else {
                $clone.find('.remove-btn').show();
            }

            // Increment for next row
            rowIndex++;
        }
    });

        // let $select = $('.select2').select2({
        //     // dropdownParent: $('#groupModal'), // Ensures dropdown appears inside modal
        //     allowClear: true,
        //     selectOnClose: false,
        // });
        // // Prevent dropdown from opening on clear
        // $select.on('select2:unselecting', function (e) {
        //     $(this).data('unselecting', true);
        // });

        // $select.on('select2:opening', function (e) {
        //     if ($(this).data('unselecting')) {
        //         $(this).removeData('unselecting');
        //         e.preventDefault();
        //     }
        // });

        // document.getElementById('addMoreBtn').addEventListener('click', function () {
        //     let container = document.getElementById('inputGroupContainer');
        //     let rows = container.querySelectorAll('.input-group-row');
        //     let lastRow = rows[rows.length - 1];
        //     let clone = lastRow.cloneNode(true);

        //     // Clear values in inputs and selects
        //     clone.querySelectorAll('input, select').forEach(function (el) {
        //         if (el.tagName === 'SELECT') {
        //             el.selectedIndex = -1; // Deselect all
        //         } else {
        //             el.value = '';
        //         }
        //     });

        //     // Remove select2 containers in the cloned row
        //     clone.querySelectorAll('.select2-container').forEach(function (s2) {
        //         s2.remove();
        //     });

        //     // Show remove button in the new row
        //     let removeBtn = clone.querySelector('.remove-btn');
        //     if (removeBtn) {
        //         removeBtn.style.display = 'inline-block';
        //     }

        //     // Append the cleaned clone
        //     container.appendChild(clone);

        //     // Re-initialize select2 only in the cloned row
        //     $(clone).find('.select2').select2();
        // });

        $(document).ready(function () {
            // This is the base URL with a placeholder for category_id
            const accountRoute = "{{ route('admin.get.e_wallet_accounts', ['category_id' => '__CATEGORY_ID__']) }}";

            // When category changes
            $('#category-select').on('change', function () {
                const categoryId = $(this).val();

                // Reset account select and currency display
                $('#account-select').empty().append('<option value="">Select Account Name</option>');
                $('#currency-display').hide();
                $('#currency-code').val('');

                if (categoryId) {
                    // Replace placeholder in URL
                    const url = accountRoute.replace('__CATEGORY_ID__', categoryId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data.length === 0) {
                                $('#account-select').append('<option value="">No accounts found</option>');
                            } else {
                                $.each(data, function (index, account) {
                                    $('#account-select').append(
                                        `<option value="${account.id}" data-currency="${account.currency}">${account.name}</option>`
                                    );
                                });
                            }
                        },
                        error: function (xhr) {
                            alert('Failed to fetch accounts. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // When account changes
        $('#account-select').on('change', function () {
                const selected = $(this).find(':selected');
                const currency = selected.data('currency');

                if (currency) {
                    $('#currency-code').text(currency);
                    $('#currency-wrapper').show();
                } else {
                    $('#currency-wrapper').hide();
                    $('#currency-code').text('');
                }
            });

            function toggleMaxWithdrawalLimit() {
                if ($('#account_type').val() === 'Deposit') {
                    $('#max_withdrawal_limit').hide();
                } else {
                    $('#max_withdrawal_limit').show();
                }
            }

            $('#account_type').on('change', toggleMaxWithdrawalLimit);

            // Initialize the visibility on page load
            toggleMaxWithdrawalLimit();



                function toggleTimeFields() {
                    if ($('#apply_time_limit').val() == 0) {
                        $('#from_time_div').hide();
                        $('#to_time_div').hide();
                    } else {
                        $('#from_time_div').show();
                        $('#to_time_div').show();
                    }
                }

            $('#apply_time_limit').on('change', toggleTimeFields);

            // Initialize the visibility on page load
            toggleTimeFields();




        });

        document.addEventListener("DOMContentLoaded", function() {
            const statusSwitch = document.getElementById("statusSwitch");
            const disableText = document.getElementById("disableText");
            const enableText = document.getElementById("enableText");

            statusSwitch.addEventListener("change", function() {
                if (this.checked) {
                    disableText.classList.remove("text-primary");
                    disableText.classList.add("text-secondary");

                    enableText.classList.remove("text-secondary");
                    enableText.classList.add("text-primary");
                } else {
                    disableText.classList.remove("text-secondary");
                    disableText.classList.add("text-primary");

                    enableText.classList.remove("text-primary");
                    enableText.classList.add("text-secondary");
                }
            });
        });

        // Event delegation for dynamically added remove buttons
        document.getElementById('inputGroupContainer').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-btn')) {
                let rows = document.querySelectorAll('.input-group-row');
                if (rows.length > 1) {
                    e.target.closest('.input-group-row').remove();
                }
            }
        });



    document.getElementById('check_all_slots').addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('input[name="time_slots[]"]');
            checkboxes.forEach(cb => cb.checked = isChecked);
        });
    </script>



    @endpush
</x-admin-layout>
