@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

{{-- <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow"> --}}
    <form action="{{ route('admin.ewallet.accounts.details') }}" method="get" id="statusFilterForm">
        <div class="row align-items-center">
            <div class="col-md-2">
                <div class="form-group">
                    <label>{{ __('reports.status') }}</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="" {{ request()->has('status') && request()->status === '' ? 'selected' : '' }}>{{ __('reports.all') }}</option>
                        <option value="1" {{ request()->status === '1' || !request()->has('status') ? 'selected' : '' }}>{{ __('reports.active') }}</option>
                        <option value="0" {{ request()->status === '0' ? 'selected' : '' }}>{{ __('reports.inactive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>@lang('reports.gateway')</label>
                    <select class="form-select form-select-sm select2" name="gateway_input"
                            data-placeholder="{{ __('reports.select_gateway') }}">
                        <option value="">{{ __('reports.all') }}</option>
                        @foreach ($gateways as $key => $value)
                            <option value="{{ $value }}" {{ request()->gateway_input === $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>@lang('reports.accounts_number')</label>
                    <input type="text" class="form-control"
                           value="{{ request('account_number') }}"
                           placeholder="Enter Account Number"
                           name="account_number">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary">
                        <i class="icon-base ti tabler-search me-1"></i> {{ __('reports.search') }}
                    </button>
                </div>
            </div>

            <!-- Buttons section -->
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    @foreach($EWalletAccount as $account)
                        <a href="{{ route('admin.ewallet.accounts.details', [
                            'status' => 1, // always active
                            'gateway_input' => $account->e_wallet_name
                        ]) }}"
                           class="btn btn-outline-primary btn-sm {{ request('gateway_input') === $account->e_wallet_name && request('status') == 1 ? 'active' : '' }}">
                            {{ $account->e_wallet_name }}
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </form>

{{-- </div> --}}

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h5 style="color: #7367f0;" class="mb-0">{{ __('accounts.accounts_list') }}</h5>

    <!-- Bulk Actions Dropdown -->
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('accounts.bulk_actions') }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
            <li>
                <a class="dropdown-item bulk-action" href="#" data-action="activate">
                    <i class="icon-base ti tabler-check me-2"></i> {{ __('accounts.activate_selected') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item bulk-action" href="#" data-action="deactivate">
                    <i class="icon-base ti tabler-x me-2"></i> {{ __('accounts.deactivate_selected') }}
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <table class=" table table-hover table-striped table-bordered table-sm">
        <thead class="thead-dark">
            <tr>
                <th scope="col">
                    <input type="checkbox" id="select-all">
                </th>
                <th scope="col">{{ __('accounts.acc_number') }}</th>
                <th scope="col">{{ __('accounts.category') }}</th>
                <th scope="col">{{ __('accounts.code') }}</th>
                <th scope="col">{{ __('accounts.groups') }}</th>
                <th scope="col">{{ __('accounts.account_name') }}</th>
                <th scope="col">{{ __('accounts.location') }}</th>
                <th scope="col">{{ __('accounts.device_name') }}</th>
                <th scope="col">{{ __('accounts.live_balance') }}</th>
                <th>{{ __('accounts.type') }}</th>
                <th scope="col">{{ __('accounts.d') }}</th>
                <th scope="col">{{ __('accounts.w') }}</th>
                <th scope="col">{{ __('accounts.d') }}</th>
                <th scope="col">{{ __('accounts.w') }}</th>
                <th scope="col">{{ __('accounts.status') }}</th>
                <th>{{ __('accounts.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $key => $item)
            <tr
                style="background-color: {{ $item['daily_received'] > ($item['daily_limit'] * $item['deposit_daily_limit_percentage']) / 100 || $item['monthly_received'] > ($item['monthly_limit'] * $item['deposit_monthly_limit_percentage']) / 100 || $item['daily_sent'] > ($item['daily_limit_withdrawal'] * $item['withdrawal_daily_limit_percentage']) / 100 || $item['monthly_sent'] > ($item['monthly_limit_withdrawal'] * $item['withdrawal_monthly_limit_percentage']) / 100 ? 'yellow' : '' }}">
                <td>
                    <input type="checkbox" class="row-checkbox" value="{{ $item->id }}">
                </td>
                <td>
                    {{ $item['account_no'] }}
                </td>

                <td>
                    {{ $item->category->name ?? 'N/A' }}
                </td>
                <td>
                </td>
                <td>
                    @if ($item->accountGroups->isNotEmpty())
                    {!! $item->accountGroups->pluck('group.name')->filter()->map(function ($name) {
                    return '<span class="badge bg-primary me-1">' . e($name) . '</span>';
                    })->implode(' ') !!}
                    @else
                    <span class="text-muted">N/A</span>
                    @endif
                </td>
                <td>{{ $item['e_wallet_name'] }}</td>
                <td>
                    {{ $item->location->location ?? '' }}
                </td>

                <td>{{ $item['device_name'] }}</td>
                <td>{{ $item['live_balance'] }}</td>
                <td>{{ $item['type'] }} ( {{ $item['account_type'] }} )</td>
                <td>
                    {{ $item['today_total_deposit'] ? number_format($item['today_total_deposit'], 2) : 0 }} /
                    {{ $item['daily_limit'] }}
                </td>
                <td>
                    {{ $item['today_total_payout'] ? number_format($item['today_total_payout'], 2) : 0 }} /
                    {{ $item['daily_limit_withdrawal'] }}
                </td>

                <td>
                    {{ $item['today_transaction_count'] }} / {{ $item['daily_limit_transaction'] }}
                </td>
                <td>
                    {{ $item['today_payout_count'] }} / {{ $item['daily_limit_withdrawal_transaction'] }}
                </td>
                <td class="text-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $item->id }}" {{
                            $item->status == 1 ? 'checked' : '' }}>
                    </div>
                </td>

                <td>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-2 shadow-sm">

                            @if (adminAccessRoute(config('role.e_wallet_accounts.access.delete')))
                            <form action="{{ route('admin.merchant.delete', $item['id']) }}" method="POST"
                                class="mb-1 delete-account-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger confirm-delete-btn">
                                    <i class="icon-base ti tabler-trash me-2"></i> {{ __('accounts.delete') }}
                                </button>
                            </form>
                            @endif

                            @if (adminAccessRoute(config('role.e_wallet_accounts.access.edit')))
                            <a href="{{ route('admin.accounts.edit', $item->id) }}" class="dropdown-item">
                                <i class="icon-base ti tabler-pencil me-2"></i>
                                {{ __('accounts.edit') }}
                            </a>

                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#newModalb" onclick="setBalanceItem({{ $item['id'] }})">
                                <i class="icon-base ti tabler-currency me-2"></i>
                                {{ __('accounts.add_balance') }}
                            </button>

                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#newModalc"
                                onclick="editBalanceItem({{ $item['id'] }}, {{ $item['balance'] }}, {{ $item['live_balance'] }})">
                                <i class="icon-base ti tabler-user me-2"></i> {{ __('accounts.edit_balance') }}
                            </button>

                            <form action="{{ route('admin.accounts.charges', $item->id) }}" method="GET" class="mb-0">
                                <button type="submit" class="dropdown-item">
                                    <i class="icon-base ti tabler-calculator me-2"></i>
                                    {{ __('accounts.charges%') }}
                                </button>
                            </form>
                            @endif

                        </div>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="100%">
                    <p class="text-dark">{{ __('accounts.no_data_found') }}</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer">
    {{ $records->appends($_GET)->links('partials.pagination') }}
</div>

<div class="modal modal-top fade" id="newModalb" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">{{ __('accounts.add_balance') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInput" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">{{ __('accounts.balance') }}</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                                <span class="amount_error text-danger error-text"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <input id="plus" value="plus" type="radio" checked name="type" />
                                <label for="plus" class="pr-3">{{ __('accounts.plus_add_credit') }}</label>
                                <br>
                                <input id="minus" value="minus" type="radio" name="type" />
                                <label for="minus" class="pr-3">{{ __('accounts.minus_subtract_credit') }}</label>
                                <span class="type_error text-danger error-text" error-text"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitBalanceBtn" class="btn btn-primary">{{ __('accounts.add')
                        }}</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">{{
                        __('accounts.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-top fade" id="newModalc" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">{{ __('accounts.edit_balance') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.edit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInpute" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">{{ __('accounts.balance') }}</label>
                                <input type="number" id="currentbalance" step="0.01" class="form-control" name="amount"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">{{ __('accounts.live_balance') }}</label>
                                <input type="number" step="0.01" id="livebalance" class="form-control"
                                    name="live_balance" required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="updateBalanceBtn" class="btn btn-primary">{{ __('accounts.update')
                        }}</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">{{
                        __('accounts.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

<script>

        let $select = $('.select2').select2({
            // placeholder: "Select Partner",
            allowClear: true,
            selectOnClose: false,
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

    function setBalanceItem(itemId) {
            // Find the input field in the modal
            var balanceInput = document.getElementById("balanceInput");

            // Set the value of the input field to the item id
            balanceInput.value = itemId;
        }

        function editBalanceItem(itemId, balance, live_balance) {
            // Find the input field in the modal
            var balanceInput = document.getElementById("balanceInpute");
            var currentbalance = document.getElementById("currentbalance");
            var livebalance = document.getElementById("livebalance");

            // Set the value of the input field to the item id
            balanceInput.value = itemId;
            currentbalance.value = balance;
            livebalance.value = live_balance;
        }

        $(document).on('click', '.confirm-delete-btn', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: "{{ __('accounts.confirm_delete_title') }}",
                text: "{{ __('accounts.confirm_delete_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: "{{ __('accounts.confirm_delete_button') }}",
                cancelButtonText: "{{ __('accounts.cancel_button') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            setInterval(function() {
                const dots = document.querySelectorAll(".dot");
                dots.forEach(function(dot) {
                    if (dot.style.opacity === "0") {
                        dot.style.opacity = "1";
                    } else {
                        dot.style.opacity = "0";
                    }
                });
            }, 700);
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Function to send AJAX request to update live status
            function updateLiveStatus(itemId) {
                if (!itemId) return; // Prevent errors if itemId is missing

                const url = "{{ route('admin.update.status', ['id' => '__id__']) }}".replace('__id__', itemId);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!csrfToken) {
                    console.error('CSRF token missing!');
                    return;
                }

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Ensure data.id exists before updating UI
                        if (data.id !== undefined) {
                            const statusIndicator = document.getElementById('status-indicator-' + data.id);
                            if (statusIndicator) {
                                statusIndicator.className = data.live ? 'dot' : 'reddot';
                            }
                        }
                    })
                    .catch(error => console.error('AJAX Error:', error));
            }

            // Run the updateLiveStatus function every 10 seconds
            setInterval(function() {
                document.querySelectorAll('[id^="status-indicator-"]').forEach(item => {
                    const itemId = item.id.split('-')[2]; // Extract ID correctly
                    updateLiveStatus(itemId);
                });
            }, 10000); // 10 seconds
        });

        $(document).on('change', '.toggle-status', function () {
        let accountId = $(this).data('id');
        let isChecked = $(this).is(':checked');

        $.ajax({
            url: 'accounts/' + accountId + '/status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    let status = response.status === 1
                        ? '{{ __("accounts.active") }}'
                        : '{{ __("accounts.inactive") }}';

                    // Optional: replace this with `toastr.success(...)` if using toastr
                    alert('Status updated to: ' + status);
                } else {
                    alert('Something went wrong: ' + response.message);
                }
            },
            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Unknown error occurred.';
                alert('Error: ' + errorMsg);

                // Optional: if using toastr
                // toastr.error('Error: ' + errorMsg);
            }
        });
    });

    $(document).ready(function() {
        $('form[action="{{ route('admin.account.balance.add') }}"]').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = $('#submitBalanceBtn');

            // Disable button and show loading text
            submitBtn.prop('disabled', true).text('{{ __('accounts.processing') }}');

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData,
                success: function(response) {
                    $('#newModalb').modal('hide');
                    $('#balanceResponse').html('');
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('accounts.success_title') }}',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            form[0].reset();
                            window.location.reload();
                        });
                    }
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    var firstErrorField = null;

                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                        var $field = $('.' + key);
                        if (!firstErrorField && $field.length) {
                            firstErrorField = $field;
                        }
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('{{ __('accounts.add') }}');
                }
            });
        });

        $('form[action="{{ route('admin.account.balance.edit') }}"]').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = $('#updateBalanceBtn');

            submitBtn.prop('disabled', true).text('{{ __('accounts.processing') }}');
            form.find('.text-danger').remove();

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData,
                success: function(response) {
                    $('#newModalc').modal('hide');
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('accounts.success_title') }}',
                            text: response.message ||
                                '{{ __('accounts.balance_updated') }}',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            form[0].reset();
                            window.location.reload();
                        });
                    }
                },
                error: function(response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;

                        $.each(errors, function(key, messages) {
                            let input = form.find('[name="' + key + '"]');
                            if (input.length) {
                                input.after('<small class="text-danger">' +
                                    messages[0] + '</small>');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('accounts.error_title') }}',
                            text: '{{ __('accounts.something_went_wrong') }}',
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('{{ __('accounts.update') }}');
                }
            });
        });

        // $(document).on('change', '.toggle-status', function() {
        //     let accountId = $(this).data('id');
        //     let status = $(this).is(':checked') ? 1 : 0;

        //     $.ajax({
        //         url: '{{ route('admin.ewallet-account.toggleStatus') }}',
        //         method: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             id: accountId,
        //             status: status
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 alert('{{ __('accounts.status_updated_successfully') }}');
        //             } else {
        //                 alert('{{ __('accounts.failed_to_update_status') }}');
        //             }
        //         },
        //         error: function() {
        //             alert('{{ __('accounts.something_went_wrong') }}');
        //         }
        //     });
        // });
    });
    document.addEventListener("DOMContentLoaded", function () {
    // Select All Checkbox
    document.getElementById("select-all").addEventListener("change", function () {
        document.querySelectorAll(".row-checkbox").forEach(cb => cb.checked = this.checked);
    });

    // Bulk Action Click
    document.querySelectorAll(".bulk-action").forEach(actionBtn => {
        actionBtn.addEventListener("click", function (e) {
            e.preventDefault();
            let action = this.dataset.action;
            let selected = Array.from(document.querySelectorAll(".row-checkbox:checked")).map(cb => cb.value);

            if (selected.length === 0) {
                alert("Please select at least one account.");
                return;
            }

            if (!confirm(`Are you sure you want to ${action} selected accounts?`)) return;

            fetch("{{ route('admin.accounts.bulk-status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    action: action,
                    ids: selected
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Something went wrong.");
                }
            });
        });
    });
});

</script>
@endpush
