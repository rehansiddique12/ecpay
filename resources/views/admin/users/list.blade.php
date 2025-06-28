<x-admin-layout :title="$pageTitle">
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>
    @php
        $currentRoute = Route::currentRouteName();
    @endphp
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        @if (adminAccessRoute(config('role.manage_staff.access.view')))
                            <div>
                                <button class="btn {{ $currentRoute == 'admin.users' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.users') }}" class="menu-link">
                                        <div data-i18n="Users">{{ __('userManagement.user') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.manage_location.access.view')))
                            <div>
                                <button class="btn {{ $currentRoute == 'admin.location' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.location') }}" class="menu-link">
                                        <div data-i18n="Location">{{ __('userManagement.location') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.roles_and_permission.access.add')))
                            <div>
                                <button
                                    class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.roles_and_permission') }}" class="menu-link">
                                        <div data-i18n="Roles and Permission">
                                            {{ __('userManagement.roles_and_permission') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif
                        @if (adminAccessRoute(config('role.roles_category.access.view')))
                            <div>
                                <button class="btn {{ $currentRoute == 'admin.rolescategory' ? 'btn-primary' : '' }}">
                                    <a href="{{ route('admin.rolescategory') }}" class="menu-link">
                                        <div data-i18n="Roles Category">{{ __('userManagement.roles_category') }}</div>
                                    </a>
                                </button>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="mb-2">{{ __('userManagement.location') }}</label>
                            <select class="form-select" name="location" id="filterLocation">
                                <option value="">{{ __('userManagement.select_location') }}</option>
                                @foreach ($locations as $key => $value)
                                    <option value="{{ $key }}"
                                        @if (@request()->location == $key) selected @endif>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Roles" class="mb-2">{{ __('userManagement.roles') }}</label>
                            <select class="form-select" name="role_type" id="filterRole">
                                <option value="">{{ __('userManagement.select_roles') }}</option>
                                @foreach ($userRoles as $key => $role)
                                    <option value="{{ $role }}"
                                        @if (@request()->roles == $role) selected @endif>
                                        {{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Status" class="mb-2">{{ __('userManagement.status') }}</label>
                            <select name="status" class="form-select" id="filterStatus">
                                <option value="">{{ __('userManagement.all') }}</option>
                                <option value="1" @if (@request()->status == '1') selected @endif>
                                    {{ __('userManagement.on') }}</option>

                                <option value="0" @if (@request()->status == '0') selected @endif>
                                    {{ __('userManagement.off') }}</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            @if (adminAccessRoute(config('role.manage_staff.access.add')))
                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                    data-bs-target="#addUserModal">
                    {{ __('userManagement.add_user') }}
                </button>
            @endif

            <div class="">
                <table id="staffTable" class="table table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 50px;">{{ __('userManagement.no') }}</th>
                            <th style="width: 200px;">{{ __('userManagement.user') }}</th>
                            <th style="width: 150px;">{{ __('userManagement.location') }}</th>
                            <th style="width: 150px;">{{ __('userManagement.roles') }}</th>
                            <th style="width: 100px;">{{ __('userManagement.status') }}</th>
                            <th style="width: 100px;">{{ __('userManagement.last_login') }}</th>
                            @if (adminAccessRoute(config('role.manage_staff.access.edit')))
                                <th style="width: 100px;">{{ __('userManagement.action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="tableLoader" class="loading-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('userManagement.processing') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="all_active" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Active User Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to active the User's")</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="" method="post">
                        @csrf
                        <a href="" class="btn btn-primary active-yes"><span>@lang('Yes')</span></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="all_inactive" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('DeActive User Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to Inactive the User's")</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="" method="post">
                        @csrf
                        <a href="" class="btn btn-primary inactive-yes"><span>@lang('Yes')</span></a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="login_as_user" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Login as User')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you really want to login as user')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="{{ route('admin.userLogin') }}" method="post" class="update-action">
                        @csrf
                        <input type="hidden" class="userId" name="userId" value="" />
                        <button type="submit" class="btn btn-primary"><span>@lang('Yes')</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Add User Modal -->
    <div class="modal modal-top fade" id="addUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form id="storeStaffForm" role="form" method="POST" class="modal-content"
                action="{{ route('admin.storeStaff') }}" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle">{{ __('userManagement.add_new_admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.name') }}:</label>
                            <input class="form-control" name="name" placeholder="{{ __('userManagement.name') }}"
                                value="{{ old('name') }}" required>
                            <span class="error-text name_error text-danger"></span>
                            <!-- Error container for Name -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.username') }}:</label>
                            <input class="form-control" name="username"
                                placeholder="{{ __('userManagement.username') }}" value="{{ old('username') }}"
                                required>
                            <span class="error-text username_error text-danger"></span>
                            <!-- Error container for Username -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.email') }}:</label>
                            <input class="form-control" name="email"
                                placeholder="{{ __('userManagement.email_address') }}" value="{{ old('email') }}">
                            <span class="error-text email_error text-danger"></span>
                            <!-- Error container for Email -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.phone') }}:</label>
                            <input class="form-control" name="phone"
                                placeholder="{{ __('userManagement.mobile_number') }}" value="{{ old('phone') }}">
                            <span class="error-text phone_error text-danger"></span>
                            <!-- Error container for Phone -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.password') }}:</label>
                            <input type="password" name="password" placeholder="{{ __('userManagement.password') }}"
                                class="form-control" value="{{ old('password') }}" autocomplete="off">
                            <span class="error-text password_error text-danger"></span>
                            <!-- Error container for Password -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark">{{ __('userManagement.select_status') }}:</label>
                            <select name="status" id="event-status" class="form-select" required>
                                <option value="1" @if (old('status') == '1') selected @endif>
                                    {{ __('userManagement.active') }}
                                </option>
                                <option value="0" @if (old('status') == '0') selected @endif>
                                    {{ __('userManagement.deactive') }}
                                </option>
                            </select>

                            <span class="error-text status_error text-danger"></span>
                            <!-- Error container for Status -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">{{ __('userManagement.select_location') }}
                                :</label>
                            <select name="location" id="location" class="form-select" required>
                                <option value="">{{ __('userManagement.select_location') }}</option>
                                @foreach ($locations as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span class="error-text location_error text-danger"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role_type" class="form-label">{{ __('userManagement.select_role') }}
                                :</label>
                            <select name="role_type" id="role_type" class="form-select" required>
                                <option value="">{{ __('userManagement.select_role') }}</option>
                                @foreach ($userRoles as $key => $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span class="error-text role_type_error text-danger"></span>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">{{ __('userManagement.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('userManagement.save') }}</button>

                </div>
            </form>
        </div>
    </div>

    {{-- Edit User Models --}}
    <div class="modal modal-top fade" id="editUserModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm" role="form" class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">{{ __('userManagement.edit_manage_admin_role') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="update-name" class="form-label">{{ __('userManagement.name') }}</label>
                            <input type="text" name="update-name" id="update-name" class="form-control"
                                placeholder="{{ __('userManagement.enter_name') }}">
                            <span class="error-text update-name_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-username"
                                class="form-label">{{ __('userManagement.username') }}</label>
                            <input type="text" id="update-username" name="update-username" class="form-control"
                                placeholder="{{ __('userManagement.enter_username') }}">
                            <span class="error-text update-username_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-email" class="form-label">{{ __('userManagement.email') }}</label>
                            <input type="email" name="update-email" id="update-email" class="form-control"
                                placeholder="{{ __('userManagement.enter_email') }}">
                            <span class="error-text update-email_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-phone"
                                class="form-label">{{ __('userManagement.phone_number') }}</label>
                            <input type="text" name="update-phone" id="update-phone" class="form-control"
                                placeholder="{{ __('userManagement.enter_phone') }}">
                            <span class="error-text update-phone_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-password"
                                class="form-label">{{ __('userManagement.password') }}</label>
                            <input type="password" name="update-password" id="update-password" class="form-control"
                                placeholder="{{ __('userManagement.enter_password') }}">
                            <span class="error-text update-password_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-status" class="form-label">{{ __('userManagement.select_status') }}
                                :</label>
                            <select name="update-status" id="update-status" class="form-select" required>
                                <option value="1" @if (old('status') == '1') selected @endif>
                                    {{ __('userManagement.active') }}</option>
                                <option value="0" @if (old('status') == '0') selected @endif>
                                    {{ __('userManagement.deactive') }}</option>
                            </select>
                            <span class="error-text update-status_error text-danger"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_location" class="form-label">{{ __('userManagement.select_location') }}
                                :</label>
                            <select name="edit_location" id="edit_location" class="form-select" required>
                                <option value="">{{ __('userManagement.select_location') }}</option>
                                @foreach ($locations as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span class="error-text location_error text-danger"></span>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="update-status" class="form-label">{{ __('userManagement.select_role') }}
                                :</label>
                            <select name="role_type_edit" id="role_type_edit" class="form-select" required>
                                <option>{{ __('userManagement.select_role') }}</option>
                                @foreach ($userRoles as $key => $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span class="error-text update-status_error text-danger"></span>
                        </div>

                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">{{ __('userManagement.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('userManagement.save') }}</button>
                </div>

            </form>
        </div>

    </div>



    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/DataTables/datatables.min.css') }}" />
    @endpush


    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Handle form submission via AJAX
                $('#staffTable').DataTable({
                    processing: false, // We will manually control the loading spinner
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "{{ route('admin.users') }}",
                        type: 'GET',
                        data: function(d) {
                            d.location = $('#filterLocation').val();
                            d.role_type = $('#filterRole').val();
                            d.status = $('#filterStatus').val();
                        },
                        beforeSend: function() {
                            // Show the loader spinner when the DataTable starts loading
                            $('#tableLoader').removeClass('d-none'); // Show the spinner
                            // Disable interactions with the table (edit buttons, etc.)
                            $('#staffTable').css('pointer-events', 'none');
                        },
                        complete: function() {
                            // Hide the loader spinner once the DataTable has finished loading
                            $('#tableLoader').addClass('d-none'); // Hide the spinner
                            // Re-enable interactions with the table
                            $('#staffTable').css('pointer-events', 'auto');
                        },
                        dataSrc: function(json) {
                            // console.log("DataTables Response:", json);
                            if (json.error) {
                                Swal.fire('Error', json.error, 'error');
                                return [];
                            }
                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            Swal.fire('Failed!', 'Could not load data: ' + error, 'error');
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'location_name',
                            name: 'location_name'
                        },
                        {
                            data: 'role_type',
                            name: 'role_type'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false
                        },
                        {
                            data: 'last_login_human',
                            name: 'last_login_human',
                            orderable: false,
                        },
                        @if (adminAccessRoute(config('role.manage_staff.access.edit')))
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        @endif
                    ],
                    order: [
                        [0, 'asc']
                    ], // Default sorting by SL column
                    columnDefs: [{
                            targets: '_all',
                            orderable: false
                        }, // Disable sorting for all columns
                    ],
                    pageLength: 50, // Default page length
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 rows', '25 rows', '50 rows', 'All']
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: {!! json_encode(__('userManagement.search_placeholder')) !!},
                        processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>{{ __('userManagement.processing') }}</span></div> <!-- You can customize this text -->", // Custom processing message with spinner
                    },
                    info: false, // Hide "Showing X to Y of Z entries" text
                });
                const searchPlaceholder = @json(__('userManagement.search_placeholder'));
                //console.log('searchPlaceholder:', searchPlaceholder);

                //submit add Admin function
                $('#storeStaffForm').submit(function(e) {
                    e.preventDefault(); // Prevent default form submission

                    // Clear previous errors
                    $('.error-text').text('');

                    // Collect form data
                    var formData = new FormData(this);

                    // Send AJAX request
                    $.ajax({
                        url: $(this).attr('action'), // Form action URL
                        method: 'POST',
                        data: formData,
                        processData: false, // Prevent jQuery from processing data
                        contentType: false, // Prevent jQuery from setting content-type header
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {
                            //console.log(response);
                            if (response.success) {

                                // Optionally, close the modal after success
                                $('#addUserModal').modal('hide'); // Close the modal
                                // Reset form after success
                                $('#storeStaffForm')[0].reset();
                                Swal.fire({
                                    icon: 'success',
                                    title: "{{ __('userManagement.success_title') }}",
                                    text: response
                                        .message, // Use the message from the response
                                    timer: 2000, // Auto-close after 2 seconds
                                    showConfirmButton: false
                                });
                            } else {
                                // You can also handle other scenarios (e.g., failure) here if necessary.
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: "{{ __('userManagement.something_wrong') }}",
                                });
                            }
                        },
                        error: function(response) {
                            // Handle validation errors
                            var errors = response.responseJSON.errors;
                            var firstErrorField = null; // Track the first error field

                            // Loop through errors and show them
                            $.each(errors, function(key, value) {
                                // Show error next to each field
                                $('.' + key + '_error').text(value[0]);

                                // Find the first field with an error and focus on it
                                var $field = $('.' + key); // Find the field by class

                                // Only set firstErrorField if it hasn't been set already
                                if (!firstErrorField && $field.length) {
                                    firstErrorField = $field; // Set the first error field
                                }
                            });

                            // If there's a field with an error, focus and scroll to it
                            if (firstErrorField && firstErrorField.length) {
                                // Focus on the first field with an error
                                firstErrorField.focus();

                                // Scroll to the first error field inside the modal
                                var modal = $('#addUserModal'); // Target the modal
                                var offsetTop = firstErrorField.offset().top;

                                // Check if the modal exists and adjust scrolling
                                if (modal.length) {
                                    modal.animate({
                                        scrollTop: offsetTop - modal.offset().top + modal
                                            .scrollTop() -
                                            100 // Adjust for the modal header
                                    }, 500); // Smooth scroll to the error field
                                } else {
                                    // Scroll to the first error field on the page if modal is not available
                                    $('html, body').animate({
                                        scrollTop: offsetTop -
                                            100 // Adjust 100px for some margin
                                    }, 500); // Smooth scroll to the error field
                                }
                            }
                        }
                    });
                });

            });

            // const debouncedReload = _.debounce(function () {
            //     staffTable.ajax.reload();
            // }, 500);

            // Apply the debounced function to the filter dropdowns
            // $('#filterLocation, #filterRole, #filterStatus').on('change', debouncedReload);

            $('#filterLocation, #filterRole, #filterStatus').on('change', function() {
                $('#staffTable').DataTable().ajax.reload();
            });

            //Edit Button Click Load Data Inside Model
            $(document).on('click', '.editAdminBtn', function() {

                let id = $(this).data('id');
                let name = $(this).data('name');
                let username = $(this).data('username');
                let email = $(this).data('email');
                let phone = $(this).data('phone');
                let status = $(this).data('status');
                let roleType = $(this).data('role-type');
                let location_id = $(this).data('location');

                $('#editForm #update-name').val(name);
                $('#editForm #update-username').val(username);
                $('#editForm #update-email').val(email);
                $('#editForm #update-phone').val(phone);
                $('#editForm #update-status').val(status);
                $('#editForm #role_type_edit').val(roleType);
                $('#editForm #edit_location').val(location_id);

                let updateUrl = $(this).data('route').replace(':id', id);
                // Set the action attribute for the form
                $('#editForm').attr('action', updateUrl);

            });

            //EditFormPostMethod
            $(document).on('submit', '#editForm', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Clear previous errors
                $('.error-text').text(''); // Clear any previous error messages

                // Get the DataTable instance
                var table = $('#staffTable').DataTable();

                // Get the current page index and the search query
                var currentPage = table.page(); // Get the current page index
                var searchValue = table.search(); // Get the current search value

                // Collect form data
                var formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Form action URL (set dynamically)
                    method: 'POST', // Ensure you're using the correct method (PUT for updating)
                    data: formData,
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting content-type header
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    beforeSend: function() {
                        // Show the loader spinner and dim the table
                        $('#tableLoader').removeClass('d-none'); // Show the spinner overlay
                        $('#staffTable').css('pointer-events', 'none'); // Disable clicks on the table
                    },
                    complete: function() {
                        // Hide the loader spinner and re-enable table interactions
                        $('#tableLoader').addClass('d-none'); // Hide the spinner overlay
                        $('#staffTable').css('pointer-events', 'auto'); // Re-enable table clicks
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close the modal after success
                            $('#editUserModal').modal('hide'); // Close the modal
                            // Reset form after success

                            Swal.fire({
                                icon: 'success',
                                title: "{{ __('userManagement.success_title') }}",
                                text: response.message, // Use the message from the response
                                timer: 2000, // Auto-close after 2 seconds
                                showConfirmButton: false
                            }).then(function() {
                                // Reload the DataTable and maintain the current page and search state
                                table.page(currentPage).search(searchValue).draw(false);
                                $('#editForm')[0].reset();
                            });

                        } else {
                            // If response.success is false, handle failure scenario
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: response.message ||
                                    "{{ __('userManagement.something_wrong') }}",
                            });
                        }
                    },
                    error: function(response) {
                        // Handle validation errors
                        var errors = response.responseJSON.errors;
                        var firstErrorField = null; // Track the first error field

                        // Loop through errors and show them
                        $.each(errors, function(key, value) {
                            // Show error next to each field
                            $('.' + key + '_error').text(value.join(
                                ', ')); // Join multiple errors if present

                            // Find the first field with an error and focus on it
                            var $field = $('#' +
                                key
                            ); // Find the field by ID (make sure the IDs are correct in HTML)

                            // Only set firstErrorField if it hasn't been set already
                            if (!firstErrorField && $field.length) {
                                firstErrorField = $field; // Set the first error field
                            }
                        });

                        // If there's a field with an error, focus and scroll to it
                        if (firstErrorField && firstErrorField.length) {
                            // Focus on the first field with an error
                            firstErrorField.focus();

                            // Scroll to the first error field inside the modal
                            var modal = $('#editUserModal'); // Target the modal
                            var offsetTop = firstErrorField.offset().top;

                            // Check if the modal exists and adjust scrolling
                            if (modal.length) {
                                modal.animate({
                                    scrollTop: offsetTop - modal.offset().top + modal.scrollTop() -
                                        100 // Adjust for the modal header
                                }, 500); // Smooth scroll to the error field
                            } else {
                                // Scroll to the first error field on the page if modal is not available
                                $('html, body').animate({
                                    scrollTop: offsetTop - 100 // Adjust 100px for some margin
                                }, 500); // Smooth scroll to the error field
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.toggle-status', function() {
                const $badge = $(this);
                const url = $badge.data('url');

                // Preserve original button width and height
                const originalWidth = $badge.outerWidth();
                const originalHeight = $badge.outerHeight();

                // Replace badge content with centered spinner, preserving size and style
                $badge.css({
                    width: originalWidth,
                    height: originalHeight,
                    padding: 0,
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    // Optional: keep same font-size/color if needed
                }).html(`
                    <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                `);

                // Send POST request
                $.post(url, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#staffTable').DataTable().ajax.reload(null, false);
                        }
                    })
                    .fail(function() {
                        alert('Failed to update status.');
                    });
            });
        </script>
    @endpush
</x-admin-layout>
