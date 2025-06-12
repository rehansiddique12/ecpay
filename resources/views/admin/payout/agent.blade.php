<x-admin-layout :title="$pageTitle">

    @push('styles')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 90px;
            height: 30px;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            user-select: none;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: white;
            line-height: 30px;
            border-radius: 20px;
            transition: 0.4s;
        }

        .slider.active {
            background: linear-gradient(to right, #28a745, #20c997);
        }

        .slider.deactive {
            background: linear-gradient(to right, #dc3545, #d1404f);
        }
        div:where(.swal2-container).swal2-top-end>.swal2-popup, div:where(.swal2-container).swal2-top-right>.swal2-popup {
            margin-top:3rem;
        }
    </style>
    @endpush

    <div class="row ">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                    {{-- @if (adminAccessRoute(config('role.partners.access.add'))) --}}
                    <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        Add New
                    </button>
                    <div class="d-flex justify-content-end mb-3">
                        <label class="form-check-label me-2" for="showAllToggle">@lang('Show All')</label>
                        <input type="checkbox" id="showAllToggle" {{ $showAll == '1' ? 'checked' : '' }}>
                    </div>



                    {{-- @endif --}}

                    <div class="table-responsive ">
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-responsive table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">@lang('ID')</th>
                                    <th scope="col">@lang('Name')</th>
                                    <th scope="col">@lang('Username')</th>
                                    <th scope="col">@lang('Balance')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $key => $item)
                                <tr>
                                    <td style="max-width: 70px;">{{ $item['id'] }}</td>
                                    <td style="max-width: 110px;"><a
                                            href="{{ route('admin.agent.profile', $item->id) }}">{{ $item['name']
                                            }}</a>
                                    </td>
                                    <td style="max-width: 100px;">{{ $item['username'] }}</td>
                                    <td>{{ $item['balance'] }}</td>

                                    <td data-label="@lang('Status')" class="text-lg-center text-right">
                                        {{-- Flex container for Status --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>@lang('Status')&nbsp;</span>
                                            <label class="switch mb-0">
                                                <input type="checkbox" class="toggle-switch" data-id="{{ $item->id }}" data-type="status"
                                                    {{ $item->status == 1 ? 'checked' : '' }}>
                                                <span class="slider {{ $item->status == 1 ? 'active' : 'deactive' }}">
                                                    {{ $item->status == 1 ? __('Active') : __('Deactive') }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>



                                    <td>
                                        @if (adminAccessRoute(config('role.partner_login.access.view')))
                                        <a class="btn btn-sm edit_button"
                                            href="{{ route('admin.apis.login', $item['id']) }}" target="_blank"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Partner">
                                            <i class="icon-base ti tabler-login me-1"></i>
                                        </a>

                                        <br>
                                        @endif
                                        @if (adminAccessRoute(config('role.partners.access.delete')))

                                        <button type="button"
                                            class="btn btn-sm delete_api_button edit_button delete-api"
                                            data-id="{{ $item['id'] }}"
                                            data-url="{{ route('admin.apis.delete', $item['id']) }}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="Delete">
                                            <i class="icon-base ti tabler-trash me-1"></i>
                                        </button>
                                                                                @endif
                                        <br>
                                        <button class="btn btn-sm edit_button"
                                            onclick="generateAndCopyPassword({{ $item['id'] }})"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Reload">
                                            <i class="icon-base ti tabler-restore me-1"></i>
                                        </button>

                                        <br>
                                        <a class="btn btn-sm edit_button"
                                            data-copy="Username: {{ $item['username'] }}&#10;Password: {{ $item['password_string'] }}&#10;Api Key: {{ $item['api_key'] }}"
                                            onclick="copyToClipboard(this)" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="Copy">
                                            <i class="icon-base ti tabler-copy-check me-1"></i>
                                        </a>


                                        <br>
                                        <a class="btn btn-sm edit_button"
                                            href="{{ route('admin.api.profile.export', $item['id']) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Download EX">
                                            <i class="icon-base ti tabler-database-export me-1"></i>
                                        </a>

                                        <br>

                                        <a class="btn btn-sm"
                                            href="{{ route('admin.apis.reset', $item['id']) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="QR Code">
                                            <i class="icon-base ti tabler-qrcode me-1"></i>
                                        </a>
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
                        @if ($records instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $records->appends($_GET)->links('partials.pagination') }}
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- New MODAL --}}
    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add Agent')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.agent.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Name</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Username</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Password</label>
                                    <input type="text" class="form-control" name="password" required />
                                    <span class="text-danger error-text password_error"></span>

                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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

    {{-- New MODAL End here --}}

    {{-- New Partner MODAL --}}
    <div class="modal modal-top fade" id="newModalByParent" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add New')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.addByParent') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">


                            <input type="text" hidden id="parentid" class="form-control" name="parent_id">
                            <input type="text" hidden id="acc_id" class="form-control" name="acc_type">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Name</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Username</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">E-Mail</label>
                                    <input type="text" class="form-control" name="email" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Phone</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Password</label>
                                    <input type="text" class="form-control" name="password" required />
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Website</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="website" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_deposit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">API End-Point</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia/api"
                                        name="api_endpoint_withdrawal" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Redirect URL</label>
                                    <input type="text" class="form-control" placeholder="http://ecwin.asia"
                                        name="redirect_url" />
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- New Partner End here --}}


    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">@lang('Add Balance')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.apis.balance.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">


                            <input type="text" hidden id="balanceInput" class="form-control" name="partner_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Balance</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                </div>
                            </div>



                            <!--<div class="col-md-12">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="pr-3">Adjustment</label>-->

                            <!--    </div>-->
                            <!--</div>-->




                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Type</label>
                                    <select class="form-control" name="adjustment" id="adjustment" required>
                                        <option value="4">Topup</option>
                                        <option value="1">Balance Adjustment</option>
                                        <option value="2">Deposit Adjustment</option>
                                        <option value="3">Withdrawal Adjustment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <input value="1" type="radio" name="amount_type" id="amount_type1" checked>
                                    <label class="pr-3">(+) Add</label>
                                    <input value="2" type="radio" name="amount_type" id="amount_type2">
                                    <label class="pr-3">(-) Deduct</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-control" name="source" required>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Transactions Id</label>
                                    <input type="text" class="form-control" name="txn" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Remarks</label>
                                    <textarea name="reason" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('Add')</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});
        $(document).on('click', '.delete_api_button', function(e) {
            e.preventDefault();
            var roleId = $(this).data('id');
            var url = $(this).data('url');
            // SweetAlert2 confirmation dialog
            Swal.fire({
                 title: `Are you sure you want to delete ID: ${roleId}?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url, // Your delete route
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: roleId
                        },
                        success: function(response) {
                            // Handle success
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || `ID ${roleId} was deleted successfully.`,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                willClose: () => {
                                    window.location.reload();
                                }
                            });

                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the role.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on('change', '.toggle-switch', function () {
    const checkbox = $(this);
    const apiId = checkbox.data('id');
    const type = checkbox.data('type'); // 'status', 'sign', or 'txn_verification'
    const value = checkbox.is(':checked') ? 1 : 0;

    $.ajax({
        url: "{{ route('admin.apis.toggleStatus') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: apiId,
            type: type,
            value: value
        },
        success: function (response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message || 'Field updated successfully.',
                    showConfirmButton: false,
                    timer: 1500
                });

                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Swal.fire('Error!', response.message || 'Update failed.', 'error');
            }
        },
        error: function () {
            Swal.fire('Error!', 'Something went wrong.', 'error');
        }
    });
});


        document.addEventListener('DOMContentLoaded', function() {
                let currentlyEditing = null;

                document.querySelectorAll('.editable').forEach(function(span) {
                    span.addEventListener('click', function() {
                        if (currentlyEditing) return; // Only one field at a time

                        currentlyEditing = this;
                        const currentText = this.textContent.trim();
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = currentText;
                        input.classList.add('form-control', 'form-control-sm');

                        this.textContent = '';
                        this.appendChild(input);
                        input.focus();

                        input.addEventListener('blur', function() {
                            const newValue = this.value.trim();
                            const id = span.dataset.id;
                            const field = span.dataset.field;

                            // Send AJAX update
                            fetch(`{{ route('admin.apis.inlineUpdate') }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        id: id,
                                        field: field,
                                        value: newValue
                                    })
                                }).then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        span.textContent = newValue;
                                    } else {
                                        alert('Update failed');
                                        span.textContent = currentText;
                                    }
                                    currentlyEditing = null;
                                }).catch(err => {
                                    console.error(err);
                                    alert('Something went wrong');
                                    span.textContent = currentText;
                                    currentlyEditing = null;
                                });
                        });
                    });
                });
            });

        function generateAndCopyPassword(id) {
                const url = `{{ route('admin.apis.generatePassword', ':id') }}`.replace(':id', id);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.password) {
                            navigator.clipboard.writeText(data.password)
                                .then(() => alert("New password generated and copied to clipboard: " + data.password))
                                .catch(() => alert("Failed to copy to clipboard."));
                        } else {
                            alert("Failed to generate password.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Something went wrong.");
                    });
            }

        function copyToClipboard(element) {
                const text = element.getAttribute('data-copy');
                navigator.clipboard.writeText(text).then(function() {
                    alert('Copied to clipboard!');
                }, function(err) {
                    alert('Failed to copy text: ', err);
                });
            }

            function setBalanceItem(itemId) {
                // Find the input field in the modal
                var balanceInput = document.getElementById("balanceInput");

                // Set the value of the input field to the item id
                balanceInput.value = itemId;
            }

            function setParentID(parentidd, acc_idd) {
                // Find the input field in the modal
                var parentidInput = document.getElementById("parentid");
                var acc_idInput = document.getElementById("acc_id");

                // Set the value of the input field to the item id
                parentidInput.value = parentidd;
                acc_idInput.value = acc_idd;
            }

         document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
    </script>

    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>

    <script>
        "use strict";
            $(document).ready(function() {

                $('form').on('submit', function (e) {
                    e.preventDefault();

                    let $form = $(this);
                    let submitBtn = $('#submitBtn');

                    // Disable the button
                    submitBtn.prop('disabled', true).text('Saving...');

                    // Clear previous errors
                    $form.find('span.error-text').text('');

                    $.ajax({
                        url: $form.attr('action'),
                        method: $form.attr('method'),
                        data: $form.serialize(),
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#newModal').modal('hide');
                                $form[0].reset();
                                Toast.fire({
                                icon: "success",
                                title: "Agent Added Successfully"
                                });
                               setTimeout(function() {
        location.reload();
    }, 3000);
                            }
                        },
                        error: function (xhr) {
                            console.log(xhr);
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    $form.find('span.' + key + '_error').text(value[0]);
                                });
                            } else {
                                alert('Something went wrong.');
                            }
                        },
                        complete: function () {
                            // Enable the button again
                            submitBtn.prop('disabled', false).text('@lang("Save")');
                        }
                    });
                });

                $('#adjustment').change(function() {
                    // Get the selected value
                    var selectedValue = $(this).val();

                    // Check if selected value is 1 or 2
                    if (selectedValue == 1 || selectedValue == 2) {
                        // If selected value is 1 or 2, check amount_type1 and uncheck amount_type2
                        $('#amount_type1').prop('checked', true);
                        $('#amount_type2').prop('checked', false);
                    } else if (selectedValue == 3) {
                        // If selected value is 3, check amount_type2 and uncheck amount_type1
                        $('#amount_type2').prop('checked', true);
                        $('#amount_type1').prop('checked', false);
                    }
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
   <script>
    document.getElementById('showAllToggle').addEventListener('change', function () {
        const showAll = this.checked ? 1 : 0;
        const url = new URL(window.location.href);
        url.searchParams.set('show_all', showAll);
        window.location.href = url.toString();
    });
</script>



    @endpush
</x-admin-layout>
