@push('styles')
{{-- <script src="{{ asset('public/assets/css/select2.min.css')}}"></script> --}}
@endpush

<h6 style="color: #7367f0">Accounts List</h6>

<div class="table-responsive">
    <table class=" table table-hover table-striped table-bordered table-sm">
        <thead class="thead-dark">
            <tr>

                <th scope="col">Acc Number</th>
                <th scope="col">Category</th>
                <th scope="col">Code</th>
                <th scope="col">Groups</th>
                <th scope="col">Account Name</th>
                <th scope="col">Location</th>
                <th scope="col">Device Name</th>
                <th scope="col">Live Balance</th>
                <th>Type</th>
                <th scope="col">D</th>
                <th scope="col">W</th>
                <th scope="col">D</th>
                <th scope="col">W</th>
                <th scope="col">Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $key => $item)
            <tr
                style="background-color: {{($item['daily_received'] > ($item['daily_limit']*$item['deposit_daily_limit_percentage']/100)) || ($item['monthly_received'] > ($item['monthly_limit']*$item['deposit_monthly_limit_percentage']/100)) || ($item['daily_sent'] > ($item['daily_limit_withdrawal']*$item['withdrawal_daily_limit_percentage']/100)) || ($item['monthly_sent'] > ($item['monthly_limit_withdrawal']*$item['withdrawal_monthly_limit_percentage']/100))?'yellow':''}}">

                <td>
                    {{ $item['account_no'] }}
                </td>

                <td>
                    {{ $item->category->name ?? 'N/A' }}
                </td>
                <td>
                </td>
                <td>
                    @if($item->accountGroups->isNotEmpty())
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

                <td>{{ $item['device_name']}}</td>
                <td>{{ $item['live_balance'] }}</td>
                <td>{{ $item['type'] }} ( {{ $item['account_type'] }} )</td>
                <td>
                   {{$item['today_total_deposit'] ?? 0 }}  / {{$item['daily_limit']}}
                </td>
                <td>
                    {{$item['daily_limit_withdrawal']}}
                </td>

                <td>
                     {{$item['today_transaction_count']}}  / {{$item['daily_limit_transaction']}}
                </td>
                <td>
                    {{$item['daily_limit_withdrawal_transaction']}}
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

                            @if(adminAccessRoute(config('role.e_wallet_accounts.access.delete')))
                            <form action="{{ route('admin.merchant.delete', $item['id']) }}" method="POST"
                                class="mb-1 delete-account-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger confirm-delete-btn">
                                    <i class="icon-base ti tabler-trash me-2"></i> Delete
                                </button>
                            </form>
                            @endif

                            @if(adminAccessRoute(config('role.e_wallet_accounts.access.edit')))
                            <a href="{{ route('admin.accounts.edit', $item->id) }}" class="dropdown-item">
                                <i class="icon-base ti tabler-pencil me-2"></i> Edit
                            </a>

                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#newModalb" onclick="setBalanceItem({{ $item['id'] }})">
                                <i class="icon-base ti tabler-currency me-2"></i> Add Balance
                            </button>

                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#newModalc"
                                onclick="editBalanceItem({{ $item['id'] }}, {{ $item['balance'] }}, {{ $item['live_balance'] }})">
                                <i class="icon-base ti tabler-user me-2"></i> Edit Balance
                            </button>

                            <form action="{{ route('admin.accounts.charges', $item->id) }}" method="GET" class="mb-0">
                                <button type="submit" class="dropdown-item">
                                    <i class="icon-base ti tabler-calculator me-2"></i> Charges %
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

<div class="modal modal-top fade" id="newModalb" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('Add Balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInput" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Balance</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                                <span class="amount_error text-danger error-text"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <input id="plus" value="plus" type="radio" checked name="type" />
                                <label for="plus" class="pr-3">+ Add Credit</label>
                                <br>
                                <input id="minus" value="minus" type="radio" name="type" />
                                <label for="minus" class="pr-3">- Subtract Credit</label>
                                <span class="type_error text-danger error-text" error-text"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitBalanceBtn" class="btn btn-primary">@lang('Add')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                        aria-label="Close">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-top fade" id="newModalc" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">@lang('Edit Balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.account.balance.edit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">


                        <input type="text" hidden id="balanceInpute" class="form-control" name="account_id">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Balance</label>
                                <input type="number" id="currentbalance" step="0.01" class="form-control" name="amount"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Live Balance</label>
                                <input type="number" step="0.01" id="livebalance" class="form-control"
                                    name="live_balance" required />
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="updateBalanceBtn" class="btn btn-primary">@lang('Update')</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                        aria-label="Close">@lang('Close')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('public/assets/js/select2.min.js')}}"></script>

<script>
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

    $(document).on('click', '.confirm-delete-btn', function (e) {
        e.preventDefault();

        const form = $(this).closest('form');

        Swal.fire({
            title: 'Are you sure?',
            text: "This account will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
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
            url: '/admin/accounts/' + accountId + '/status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    let status = response.status === 1 ? 'Active' : 'Inactive';
                    toastr.success('Status updated to ' + status);
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            error: function () {
                toastr.error('Status update failed.');
            }
        });
    });

    $(document).ready(function () {
        $('form[action="{{ route('admin.account.balance.add') }}"]').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = $('#submitBalanceBtn');

            // Disable button and show loading text
            submitBtn.prop('disabled', true).text('Processing...');

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData,
                success: function (response) {
                    $('#newModalb').modal('hide');
                    $('#balanceResponse').html('');
                    // $('#balanceResponse').html('<div class="alert alert-success">Balance updated successfully!</div>');
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // This runs after the alert is closed (automatically or manually)

                            // Close modal



                            // Reset form
                            form[0].reset();

                            // Reload page
                            window.location.reload();
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
                complete: function () {
                    // Re-enable button
                    submitBtn.prop('disabled', false).text('Add');
                }
            });
        });

        $('form[action="{{ route('admin.account.balance.edit') }}"]').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = $('#updateBalanceBtn');

            // Disable the button and change text
            submitBtn.prop('disabled', true).text('Processing...');

            // Clear previous errors
            form.find('.text-danger').remove();

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData,
                success: function (response) {
                    $('#newModalc').modal('hide');
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Balance updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {

                            form[0].reset();
                            window.location.reload();
                        });
                    }
                },
                error: function (response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;

                        $.each(errors, function (key, messages) {
                            let input = form.find('[name="' + key + '"]');
                            if (input.length) {
                                input.after('<small class="text-danger">' + messages[0] + '</small>');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).text('Update');
                }
            });
        });

        $(document).on('change', '.toggle-status', function () {
            let accountId = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("admin.ewallet-account.toggleStatus") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: accountId,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        alert('Status updated successfully');
                    } else {
                        alert('Failed to update status');
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        });


    });
</script>
@endpush
