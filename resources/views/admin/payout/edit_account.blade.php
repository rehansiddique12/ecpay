<x-admin-layout :title="$pageTitle">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
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
                                class="btn {{ in_array($currentRoute, ['admin.ewallet.accounts.details', 'admin.accounts.edit']) ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                    <div data-i18n="Accounts List">{{ __('accounts.accounts_list') }}</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_account') }}" class="menu-link">
                                    <div data-i18n="Add Accounts">{{ __('accounts.add_account') }}</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.account_group') }}" class="menu-link">
                                    <div data-i18n="Account Group">{{ __('accounts.account_group') }}</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.gateway') }}" class="menu-link">
                                    <div data-i18n="Gateway">{{ __('accounts.gateway') }}</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.account_management.add_category') }}" class="menu-link">
                                    <div data-i18n="Add Category">{{ __('accounts.categories') }}</div>
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
                <h3 class="text-primary text-bold">{{ __('accounts.edit_batch_title') }}</h3>
                <form method="post" action="{{ route('admin.accounts.update', $e_wallet_account->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.category_name') }}</label>
                            <select class="form-select" name="category_id" id="category-select">
                                <option value="">{{ __('accounts.select_category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ isset($e_wallet_account) && $e_wallet_account->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-4 col-4">
                            <label>{{ __('accounts.select_account') }}</label>

                            <div class="input-group">
                                <select class="form-select" name="account_id" id="account-select">
                                    <option value="">{{ __('accounts.select_account_name') }}</option>
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
                    <h6 class="mb-0">{{ __('accounts.configuration') }}</h6>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.daily_deposit_amount_limit') }}</label>
                            <input type="number" class="form-control" name="daily_limit"
                                value="{{ old('daily_limit', $e_wallet_account->daily_limit ?? '') }}" required>

                            @error('daily_limit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.daily_withdrawal_amount_limit') }}</label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal"
                                value="{{ old('daily_limit_withdrawal', $e_wallet_account->daily_limit_withdrawal ?? '') }}"
                                required>

                            @error('daily_limit_withdrawal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.monthly_deposit_amount_limit') }}</label>
                            <input type="number" class="form-control" name="monthly_limit"
                                value="{{ old('monthly_limit', $e_wallet_account->monthly_limit ?? '') }}" required>

                            @error('monthly_limit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.monthly_withdrawal_amount_limit') }}</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                value="{{ old('monthly_limit_withdrawal', $e_wallet_account->monthly_limit_withdrawal ?? '') }}"
                                required>

                            @error('monthly_limit_withdrawal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.daily_deposit_transaction_limit') }}</label>
                            <input type="number" class="form-control" name="daily_limit_transaction"
                                value="{{ old('daily_limit_transaction', $e_wallet_account->daily_limit_transaction ?? '') }}"
                                required>
                            @error('daily_limit_transaction')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.daily_withdrawal_transaction_limit') }}</label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal_transaction"
                                value="{{ old('daily_limit_withdrawal_transaction', $e_wallet_account->daily_limit_withdrawal_transaction ?? '') }}"
                                required>
                            @error('daily_limit_withdrawal_transaction')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.monthly_deposit_transaction_limit') }}</label>
                            <input type="number" class="form-control" name="monthly_limit_transaction"
                                value="{{ old('monthly_limit_transaction', $e_wallet_account->monthly_limit_transaction ?? '') }}"
                                required>
                            @error('monthly_limit_transaction')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.monthly_withdrawal_transaction_limit') }}</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal_transaction"
                                value="{{ old('monthly_limit_withdrawal_transaction', $e_wallet_account->monthly_limit_withdrawal_transaction ?? '') }}"
                                required>
                            @error('monthly_limit_withdrawal_transaction')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.max_transaction_per_minute') }}</label>
                            <input type="number" class="form-control" name="max_transaction_per_minute"
                                value="{{ old('max_transaction_per_minute', $e_wallet_account->max_transaction_per_minute ?? '') }}"
                                required>
                            @error('max_transaction_per_minute')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>{{ __('accounts.max_amount_per_minute') }}</label>
                            <input type="number" class="form-control" name="max_amount_per_minute"
                                value="{{ old('max_amount_per_minute', $e_wallet_account->max_amount_per_minute ?? '') }}"
                                required>
                            @error('max_amount_per_minute')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <hr style="border-top: 1px solid white;">
                    <div class="row">
                        <div class="form-group col-md-12 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">{{ __('accounts.time_configuration') }}</h6>
                                <div>
                                    <input type="checkbox" id="check_all_slots" class="form-check-input">
                                    <label for="check_all_slots"
                                        class="form-check-label text-white">{{ __('accounts.check_all') }}</label>
                                </div>
                            </div>

                            @php
                                $start = strtotime('00:00');
                                $end = strtotime('24:00');
                                $i = 0;
                                $slots = [];

                                for ($time = $start; $time < $end; $time += 1800) {
                                    $from = date('H:i', $time);
                                    $to = date('H:i', $time + 1800);
                                    $slots[] = "$from - $to";
                                }
                            $chunks = array_chunk($slots, ceil(count($slots) / 6)); // 6 columns @endphp <div class="row">
                                @foreach ($chunks as $column)
                                    <div class="col-md-2 col-sm-4 col-6">
                                        @foreach ($column as $slot)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="time_slots[]"
                                                    value="{{ $slot }}" id="slot_{{ $i }}"
                                                    {{ in_array($slot, $savedSlots ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label text-white"
                                                    for="slot_{{ $i }}">
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
                        <h6>{{ __('accounts.threshold_alert') }}</h6>
                        <div class="form-group col-md-3 col-3">
                            <label>{{ __('accounts.daily_deposit_limit') }}</label>
                            <input type="number" class="form-control" min="1" max="100"
                                name="deposit_daily_limit_percentage"
                                value="{{ old('deposit_daily_limit_percentage', $e_wallet_account->deposit_daily_limit_percentage ?? '') }}"
                                required>

                            @error('deposit_daily_limit_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-3 col-3">
                            <label>{{ __('accounts.daily_withdrawal_limit') }}</label>
                            <input type="number" class="form-control" min="1" max="100"
                                name="withdrawal_daily_limit_percentage"
                                value="{{ old('withdrawal_daily_limit_percentage', $e_wallet_account->withdrawal_daily_limit_percentage ?? '') }}"
                                required>

                            @error('withdrawal_daily_limit_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-3 col-3">
                            <label>{{ __('accounts.monthly_deposit_limit') }}</label>
                            <input type="number" class="form-control" name="deposit_monthly_limit_percentage"
                                min="1" max="100"
                                value="{{ old('deposit_monthly_limit_percentage', $e_wallet_account->deposit_monthly_limit_percentage ?? '') }}"
                                required>

                            @error('deposit_monthly_limit_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-3 col-3">
                            <label>{{ __('accounts.monthly_withdrawal_limit') }}</label>
                            <input type="number" class="form-control" min="1" max="100"
                                name="withdrawal_monthly_limit_percentage"
                                value="{{ old('withdrawal_monthly_limit_percentage', $e_wallet_account->withdrawal_monthly_limit_percentage ?? '') }}"
                                required>

                            @error('withdrawal_monthly_limit_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-3 col-3">
                            <label>{{ __('accounts.low_balance_alert') }}</label>
                            <input type="number" class="form-control" name="low_balance_amount" min="1"
                                value="{{ old('low_balance_amount', $e_wallet_account->low_balance_amount ?? '') }}"
                                required>

                            @error('low_balance_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <hr>
                    <div class="col-12 mb-3">
                        <h6>{{ __('accounts.add_account') }}</h6>
                    </div>
                    <div id="inputGroupContainer">
                        <div class="row input-group-row">
                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.account_name') }}</label>
                                <input type="text" name="e_wallet_name[]"
                                    value="{{ old('e_wallet_name', $e_wallet_account->e_wallet_name ?? '') }}"
                                    required class="form-control" required>
                            </div>
                            <input type="hidden" name="first_account_id" value="{{ $e_wallet_account->id ?? '' }}">

                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.device_name') }}</label>
                                <input type="text" name="device_name[]"
                                    value="{{ old('device_name', $e_wallet_account->device_name ?? '') }}" required
                                    class="form-control" required>
                            </div>

                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.account_number') }}</label>
                                <input type="text" name="account_number[]" class="form-control"
                                    value="{{ old('account_number', $e_wallet_account->account_no ?? '') }}" required>
                            </div>

                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.account_group') }}</label>
                                <select class="form-select select3" name="account_group[0][]" multiple
                                    data-placeholder={{ __('accounts.select_groups') }} data-allow-clear="true">
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"
                                            {{ in_array($group->id, $selectedGroupIds) ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-1 col-12">
                                <label>{{ __('accounts.type') }}</label>
                                <select name="account_type[]" class="form-select" required>
                                    <option value="">{{ __('accounts.select') }}</option>
                                    <option value="Agent" {{ $e_wallet_account->type == 'Agent' ? 'selected' : '' }}>
                                        {{ __('accounts.agent') }}</option>
                                    <option value="Merchant"
                                        {{ $e_wallet_account->type == 'Merchant' ? 'selected' : '' }}>
                                        {{ __('accounts.merchant') }}
                                    </option>
                                    <option value="Personal"
                                        {{ $e_wallet_account->type == 'Personal' ? 'selected' : '' }}>
                                        {{ __('accounts.personal') }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-1 col-12">
                                <label>{{ __('accounts.in_out') }}</label>
                                <select name="in_out[]" class="form-select" required>
                                    <option value="">{{ __('accounts.select') }}</option>
                                    <option value="Deposit"
                                        {{ $e_wallet_account->account_type == 'Deposit' ? 'selected' : '' }}>
                                        {{ __('accounts.deposit') }}
                                    </option>
                                    <option value="Withdrawal"
                                        {{ $e_wallet_account->account_type == 'Withdrawal' ? 'selected' : '' }}>
                                        {{ __('accounts.withdrawal') }}</option>
                                    <option value="Both"
                                        {{ $e_wallet_account->account_type == 'Both' ? 'selected' : '' }}>
                                        {{ __('accounts.both') }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.location') }}</label>
                                <select name="location[]" class="form-select select2"
                                    data-placeholder="Select Location" data-allow-clear="true">
                                    <option></option>
                                    <option value="">{{ __('accounts.select_location') }}</option>
                                    @foreach ($users_locations as $location)
                                        <option
                                            {{ $location->id == $e_wallet_account->location_id ? 'selected' : '' }}
                                            value="{{ $location->id }}">{{ $location->location }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2 col-12">
                                <label>{{ __('accounts.qr') }}</label>
                                {{-- Show old image preview if exists --}}
                                @if (!empty($e_wallet_account->image))
                                    <div class="mb-2">
                                        <img src="{{ asset('assets/uploads/withdraw/' . $e_wallet_account->image) }}"
                                            alt={{ __('accounts.qr_code') }} class="img-thumbnail"
                                            style="max-width: 100px;">
                                    </div>
                                @endif
                                <input type="file" name="image[]" class="form-control qr-file"
                                    accept="image/png, image/jpeg">
                            </div>

                        </div>
                    </div>

                    <!-- More Button -->
                    <div class="mt-3">
                        <button type="button" id="addMoreBtn"
                            class="btn btn-primary">{{ __('accounts.more') }}</button>
                    </div>

                    <div class="row mt-3 justify-content-between">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label>{{ __('accounts.status') }}</label>
                                <div class="form-check form-switch d-flex align-items-center">
                                    <span id="disableText" class="me-12 text-primary">{{ __('accounts.no') }}</span>
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                                        value="1"
                                        {{ isset($e_wallet_account) && $e_wallet_account->status == 1 ? 'checked' : '' }}>
                                    <span id="enableText"
                                        class="ms-2 text-secondary">{{ __('accounts.yes') }}</span>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn  btn-primary btn-block mt-3">{{ __('accounts.save_changes') }}</button>

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
                <label>{{ __('accounts.account_name') }}</label>
                <input type="text" name="e_wallet_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>{{ __('accounts.device_name') }}</label>
                <input type="text" name="device_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>{{ __('accounts.account_number') }}</label>
                <input type="text" name="account_number[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>{{ __('accounts.account_group') }}</label>
                <select class="form-select select2" name="account_group[__INDEX__][]" multiple
                    data-placeholder={{ __('accounts.select_groups') }} data-allow-clear="true">
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>{{ __('accounts.type') }}</label>
                <select name="account_type[]" class="form-select" required>
                    <option value="">{{ __('accounts.select') }}</option>
                    <option value="Agent">{{ __('accounts.agent') }}</option>
                    <option value="Merchant">{{ __('accounts.merchant') }}</option>
                    <option value="Personal">{{ __('accounts.personal') }}</option>
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>{{ __('accounts.in_out') }}</label>
                <select name="in_out[]" class="form-select" required>
                    <option value="">{{ __('accounts.select') }}</option>
                    <option value="Deposit">{{ __('accounts.deposit') }}</option>
                    <option value="Withdrawal">{{ __('accounts.withdrawal') }}</option>
                    <option value="Both">{{ __('accounts.both') }}</option>
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>{{ __('accounts.location') }}</label>
                <select name="location[]" class="form-select select2"
                    data-placeholder={{ __('accounts.select_location') }} data-allow-clear="true">
                    <option></option>
                    <option value="">{{ __('accounts.select_location') }}</option>
                    @foreach ($users_locations as $location)
                        <option value="{{ $location->id }}">{{ $location->location }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>{{ __('accounts.qr') }}</label>
                <input type="file" name="image[]" class="form-control qr-file" accept="image/png, image/jpeg">
            </div>

            <div class="form-group col-md-1 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-btn">{{ __('accounts.remove') }}</button>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            $(document).ready(function() {


                let $select = $('.select3').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    // selectOnClose: true,
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

                let rowIndex = 1;

                // Add first row on page load
                // addNewRow();

                // Add more button functionality
                $('#addMoreBtn').click(function() {
                    addNewRow();
                });

                // Remove button functionality
                $(document).on('click', '.remove-btn', function() {
                    if ($('#inputGroupContainer .input-group-row').length > 1) {
                        $(this).closest('.input-group-row').remove();
                    } else {
                        alert("{{ __('accounts.need_at_least_one_row') }}");
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

            $(document).ready(function() {
                const accountRoute =
                    "{{ route('admin.get.e_wallet_accounts', ['category_id' => '__CATEGORY_ID__']) }}";
                const selectedAccountId = "{{ $e_wallet_account->gateway_id ?? '' }}";

                function loadAccounts(categoryId, selectedId = '') {
                    const url = accountRoute.replace('__CATEGORY_ID__', categoryId);

                    $('#account-select').empty().append(
                        '<option value="">{{ __('accounts.select_account_name') }}</option>');
                    $('#currency-wrapper').hide();
                    $('#currency-code').text('');

                    if (categoryId) {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                if (data.length === 0) {
                                    $('#account-select').append(
                                        '<option value="">{{ __('accounts.no_accounts_found') }}</option>'
                                    );
                                } else {
                                    $.each(data, function(index, account) {
                                        const selected = account.id == selectedId ? 'selected' : '';
                                        $('#account-select').append(
                                            `<option value="${account.id}" data-currency="${account.currency}" ${selected}>${account.name}</option>`
                                        );
                                    });

                                    // Show currency if selected
                                    const selectedOption = $('#account-select').find('option:selected');
                                    const currency = selectedOption.data('currency');
                                    if (currency) {
                                        $('#currency-code').text(currency);
                                        $('#currency-wrapper').show();
                                    }
                                }
                            },
                            error: function(xhr) {
                                alert('{{ __('accounts.failed_fetch_accounts') }}');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                }

                // Load accounts on page load if category is selected (edit mode)
                const currentCategory = $('#category-select').val();
                if (currentCategory) {
                    loadAccounts(currentCategory, selectedAccountId);
                }

                // On category change
                $('#category-select').on('change', function() {
                    const categoryId = $(this).val();
                    loadAccounts(categoryId);
                });

                // When account changes
                $('#account-select').on('change', function() {
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

                });
            });

            // Event delegation for dynamically added remove buttons
            document.getElementById('inputGroupContainer').addEventListener('click', function(e) {
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
