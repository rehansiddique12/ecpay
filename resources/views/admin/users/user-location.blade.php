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
                {{-- <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                    <div class="card-body"> --}}
                {{-- <div class="card-header"> --}}
                @if (adminAccessRoute(config('role.manage_location.access.add')))
                    <button type="button" class="btn btn-primary  mt-5 mb-3" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        {{ __('userManagement.add_new_location') }}
                    </button>
                @endif
                {{--
                        </div> --}}

                <div class="">
                    <table id="locationTableBody"
                        class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;">{{ __('userManagement.no') }}</th>
                                <th style="width: 200px;">{{ __('userManagement.location') }}</th>
                                <th style="width: 120px;">{{ __('userManagement.status') }}</th>
                                @if (adminAccessRoute(config('role.manage_location.access.edit')) ||
                                        adminAccessRoute(config('role.manage_location.access.delete')))
                                    <th style="width: 100px;" class="text-center">{{ __('userManagement.action') }}
                                    </th>
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
            {{--
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newModalLabel">{{ __('userManagement.add_location') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addLocationForm" action="{{ route('admin.users.location.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="location" class="form-label">{{ __('userManagement.location') }}:</label>
                            <input type="text" class="form-control" id="location" name="location" required
                                placeholder="{{ __('userManagement.enter_location') }}">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('userManagement.status') }}:</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">{{ __('userManagement.active') }}</option>
                                <option value="0">{{ __('userManagement.inactive') }}</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('userManagement.close') }}</button>
                    <button type="submit" form="addLocationForm" id="saveLocationBtn" class="btn btn-primary">
                        {{ __('userManagement.save_location') }}
                    </button>
                    <div id="formErrors" class="text-danger mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">{{ __('userManagement.edit_location') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLocationForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_location"
                                class="form-label">{{ __('userManagement.location') }}:</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required
                                placeholder="{{ __('userManagement.enter_location') }}">
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">{{ __('userManagement.status') }}:</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="1">{{ __('userManagement.active') }}</option>
                                <option value="0">{{ __('userManagement.inactive') }}</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('userManagement.close') }}</button>
                    <button type="submit" form="editLocationForm" id="editLocationBtn"
                        class="btn btn-primary">{{ __('userManagement.update_location') }}</button>
                    <div id="formErrors1" class="text-danger mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
        <script>
            jQuery(document).ready(function() {
                // jQuery(document).on('click', '.edit-location', function(e) {
                //     e.preventDefault();
                //     try {
                //         var id = jQuery(this).data('id');
                //         var location = jQuery(this).data('location');
                //         var status = jQuery(this).data('status');

                //         // Validate data
                //         if (!id || !location) {
                //             console.error('Missing required data');
                //             return;
                //         }

                //         // Populate form fields
                //         jQuery('#edit_id').val(id);
                //         jQuery('#edit_location').val(location).trigger('change');
                //         jQuery('#edit_status').val(status).trigger('change');

                //         // Update form action URL
                //         var updateUrl = "{{ route('admin.location.update', '') }}/" + id;
                //         jQuery('#editLocationForm').attr('action', updateUrl);

                //         // Debugging logs
                //         console.log('Edit form populated:', {
                //             id: id,
                //             location: location,
                //             status: status
                //         });
                //     } catch (error) {
                //         console.error('Error populating edit form:', error);
                //     }
                // });

                $('#locationTableBody').DataTable({
                    processing: false, // We will manually control the loading spinner
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "{{ route('admin.location') }}",
                        type: 'GET',
                        // data: function (d) {
                        //     d.location = $('#filterLocation').val();
                        //     d.role_type = $('#filterRole').val();
                        //     d.status = $('#filterStatus').val();
                        // },
                        beforeSend: function() {
                            // Show the loader spinner when the DataTable starts loading
                            $('#tableLoader').removeClass('d-none'); // Show the spinner
                            // Disable interactions with the table (edit buttons, etc.)
                            $('#locationTableBody').css('pointer-events', 'none');
                        },
                        complete: function() {
                            // Hide the loader spinner once the DataTable has finished loading
                            $('#tableLoader').addClass('d-none'); // Hide the spinner
                            // Re-enable interactions with the table
                            $('#locationTableBody').css('pointer-events', 'auto');
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
                            data: 'location',
                            name: 'location'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
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
                        searchPlaceholder: "Search...",
                        processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Processing...</span></div> <!-- You can customize this text -->", // Custom processing message with spinner
                    },
                    info: false, // Hide "Showing X to Y of Z entries" text
                });

                //addForm
                $('#addLocationForm').on('submit', function(e) {
                    e.preventDefault();

                    let form = $(this);
                    let submitBtn = $('#saveLocationBtn');

                    submitBtn.prop('disabled', true).text('Saving...'); // Disable button and show loading text

                    $('#formErrors').html(''); // Clear previous errors

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Location added',
                                text: 'The new location has been successfully added!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Close the modal
                            $('#newModal').modal('hide');

                            // Reset the form
                            form[0].reset();

                            // Reload DataTable
                            $('#locationTableBody').DataTable().ajax.reload(null,
                                false); // false = retain pagination
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let errorHtml = '<ul>';
                                $.each(errors, function(key, value) {
                                    errorHtml += '<li>' + value[0] + '</li>';
                                });
                                errorHtml += '</ul>';
                                $('#formErrors').html(errorHtml);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Something went wrong. Please try again later.',
                                });
                            }
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text(
                                'Save Location'); // Re-enable button
                        }
                    });
                });

                $('#editLocationForm').on('submit', function(e) {
                    e.preventDefault();

                    let form = $(this);
                    let submitBtn = $('#editLocationBtn');

                    submitBtn.prop('disabled', true).text(
                        'Updating...'); // Disable button and show loading text

                    $('#formErrors1').html(''); // Clear previous errors

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Location Updated',
                                text: 'The location has been Updated successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Close the modal
                            $('#editModal').modal('hide');

                            // Reset the form
                            form[0].reset();

                            // Reload DataTable
                            $('#locationTableBody').DataTable().ajax.reload(null,
                                false); // false = retain pagination
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let errorHtml = '<ul>';
                                $.each(errors, function(key, value) {
                                    errorHtml += '<li>' + value[0] + '</li>';
                                });
                                errorHtml += '</ul>';
                                $('#formErrors1').html(errorHtml);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Something went wrong. Please try again later.',
                                });
                            }
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text(
                                'Update Location'); // Re-enable button
                        }
                    });
                });

                //make  location  status    active/inactive
                $(document).on('click', '.toggle-status', function() {
                    let locationId = $(this).data('id');
                    let $this = $(this);

                    // Preserve original size
                    const originalWidth = $this.outerWidth();
                    const originalHeight = $this.outerHeight();

                    // Set spinner in place of the badge
                    $this.css({
                        width: originalWidth,
                        height: originalHeight,
                        padding: 0,
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }).html(`
                    <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                `);

                    $.ajax({
                        url: "{{ route('admin.location.toggleStatus') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: locationId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#locationTableBody').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!'
                            });
                        }
                    });
                });


            });
        </script>
    @endpush
</x-admin-layout>
