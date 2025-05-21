<x-admin-layout :title="$pageTitle">

    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);

            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        /* Hide the loading spinner by default */
        .d-none {
            display: none;
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
                        @if(adminAccessRoute(config('role.manage_staff.access.view')))
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.users' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.users') }}" class="menu-link">
                                    <div data-i18n="Users">Users</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.manage_location.access.view')))
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.location' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.location') }}" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.roles_and_permission.access.add')))
                        <div>
                            <button
                                class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.roles_and_permission') }}" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        @endif
                        @if(adminAccessRoute(config('role.roles_category.access.view')))
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.rolescategory' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.rolescategory') }}" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="card card-primary my-4 shadow">
                    <div class="card-body">
                        @if(adminAccessRoute(config('role.roles_category.access.add')))
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                Add New Role
                            </button>
                            <button type="button" class="btn btn-warning" id="openCloneModal" data-bs-toggle="modal"
                                data-bs-target="#cloneModal">
                                Clone Role Permission
                            </button>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="rolesTables"
                                class="text-center categories-show-table table table-hover table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">@lang('No.')</th>
                                        <th scope="col">@lang('Roles Name')</th>
                                        <th class="text-center" scope="col">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div id="tableLoader" class="loading-overlay d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Role Modal --}}
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addRolesForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="newModalLabel">Add Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roles_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roles_name" name="role"
                                placeholder="Enter role name" required>
                            <div class="error-text text-danger mt-1 role_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveRoleBtn">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CloneEdit--}}
    <div class="modal fade" id="cloneModal" tabindex="-1" aria-labelledby="cloneModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addRolesFormCopy" action="{{ route('admin.roles.copy') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="cloneModalLabel">Clone Role Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <label for="add_new_role" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="add_new_role" name="add_new_role"
                            placeholder="Enter role name" required>
                    </div>


                    <div class="modal-body">
                        <label for="copy_role_name" class="form-labels">Clone from</label>
                        <select class="form-select" id="copy_role_name" name="copy_role_name" required>
                            <option value="">-- Select Role --</option>
                            <!-- Options will be appended here -->
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Role Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editRolesForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_role_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_roles_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="edit_roles_name" name="roles_name"
                                placeholder="Enter role name" required>
                            <div class="text-danger mt-1 roles_name_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editSaveBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('.categories-show-table').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "{{ route('admin.rolescategory') }}", // Make sure this route returns JSON for DataTable
                        type: 'GET',
                        beforeSend: function () {
                            $('#tableLoader').removeClass('d-none');
                            $('.categories-show-table').css('pointer-events', 'none');
                        },
                        complete: function () {
                            $('#tableLoader').addClass('d-none');
                            $('.categories-show-table').css('pointer-events', 'auto');
                        },
                        dataSrc: function (json) {
                            if (json.error) {
                                Swal.fire('Error', json.error, 'error');
                                return [];
                            }
                            return json.data;
                        },
                        error: function (xhr, error, code) {
                            Swal.fire('Failed!', 'Could not load data: ' + error, 'error');
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 rows', '25 rows', '50 rows', 'All']
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        // processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Processing...</span></div>"
                    },
                    info: false
            });



        });


        $('#addRolesForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = $('#saveRoleBtn');

            // Clear previous errors
            $('#role-error').text('');

            // Disable button and show spinner
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('admin.roles.add') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        // Close modal, reset form, show success message
                        $('#newModal').modal('hide');
                        form[0].reset();
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false
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
                    btn.prop('disabled', false).text('Save Role');
                }
            });
        });


        $('#editRolesForm').on('submit', function(e) {
            e.preventDefault();
            url= $(this).attr('action');
            $('#edit-role-error').text('');
            let btn = $('#editSaveBtn');
            // Disable button
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editModal').modal('hide');
                        $('#editRolesForm')[0].reset();
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false
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
                    btn.prop('disabled', false).text('Save Changes');
                }
            });
        });

        $('#cloneModal').on('show.bs.modal', function () {
            $.ajax({
                url: "{{ route('admin.roles.list') }}", // Create this route
                type: "GET",
                success: function(response) {
                    const selectBox = $('#copy_role_name');
                    selectBox.empty().append('<option value="">-- Select Role --</option>');

                    $.each(response.roles, function(key, value) {
                        selectBox.append(`<option value="${value.name}">${value.name}</option>`);
                    });
                },
                error: function(xhr) {
                    console.error("Failed to fetch roles:", xhr.responseText);
                }
            });
        });


    </script>
    @endpush

</x-admin-layout>
