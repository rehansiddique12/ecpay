<x-admin-layout :title="$pageTitle">
<style>
    /* Styling for the loading overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4); /* Dim the table */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;  /* Ensure overlay is above the table */
}

/* Hide the loading spinner by default */
.d-none {
    display: none;
}

</style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            @if(adminAccessRoute(config('role.manage_staff.access.add')))
            <div class="card-header d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User
                </button>
            </div>
            @endif

            <div class="table-responsive text-nowrap p-2">
                <table id="staffTable" class="table table-sm">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-white">SL</th>
                            <th class="text-white">Username</th>
                            <th class="text-white">Email</th>
                            <th class="text-white">Phone</th>
                            <th class="text-white">Status</th>
                            @if(adminAccessRoute(config('role.manage_staff.access.edit')))
                            <th class="text-white">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
               <!-- Loading Overlay (hidden initially) -->
                <div id="tableLoader" class="loading-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Processing...</span>
                    </div>
                </div>
            </div>
            <!-- Pagination links -->
            {{-- <div class="card-footer">
                {{ $admins->appends($_GET)->links('partials.pagination') }}
            </div> --}}
        </div>
        {{-- Add User Models --}}
        <div class="modal modal-top fade" id="addUserModal" tabindex="-1"  data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <form id="storeStaffForm" role="form" method="POST" class="modal-content"
                    action="{{ route('admin.storeStaff') }}" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTopTitle">@lang('Manage Admin Role')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('Name') }}:</label>
                                <input class="form-control" name="name" placeholder="{{ trans('Name') }}"
                                    value="{{ old('name') }}" required>
                                <span class="error-text name_error text-danger"></span>
                                <!-- Error container for Name -->
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('Username') }}:</label>
                                <input class="form-control" name="username" placeholder="{{ trans('Username') }}"
                                    value="{{ old('username') }}" required>
                                <span class="error-text username_error text-danger"></span>
                                <!-- Error container for Username -->
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('E-Mail') }}:</label>
                                <input class="form-control" name="email" placeholder="Email Address"
                                    value="{{ old('email') }}" required>
                                <span class="error-text email_error text-danger"></span>
                                <!-- Error container for Email -->
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('Phone') }}:</label>
                                <input class="form-control" name="phone" placeholder="{{ trans('Mobile Number') }}"
                                    value="{{ old('phone') }}" required>
                                <span class="error-text phone_error text-danger"></span>
                                <!-- Error container for Phone -->
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('Password') }}:</label>
                                <input type="password" name="password" placeholder="Password" class="form-control"
                                    value="{{ old('password') }}" autocomplete="off">
                                <span class="error-text password_error text-danger"></span>
                                <!-- Error container for Password -->
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark">{{ trans('Select Status') }}:</label>
                                <select name="status" id="event-status" class="form-select" required>
                                    <option value="1" @if(old('status')=='1' ) selected @endif>{{ trans('Active') }}
                                    </option>
                                    <option value="0" @if(old('status')=='0' ) selected @endif>{{ trans('DeActive') }}
                                    </option>
                                </select>
                                <span class="error-text status_error text-danger"></span>
                                <!-- Error container for Status -->
                            </div>

                            <!-- Accessibility Table (No changes here) -->
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <h5 class="card-title">{{ trans('Accessibility') }}</h5>
                                    </div>

                                    <div class="card-body select-all-access">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input selectAll" name="accessAll"
                                                id="selectAll">
                                            <label class="form-check-label" for="selectAll">{{ trans('Select All')
                                                }}</label>
                                        </div>

                                        <table class="table table-hover table-striped table-bordered text-center">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-start">@lang('Permissions')</th>
                                                    <th>@lang('View')</th>
                                                    <th>@lang('Add')</th>
                                                    <th>@lang('Edit')</th>
                                                    <th>@lang('Delete')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(config('role') as $key => $value)
                                                <tr>
                                                    <td data-label="Permissions" class="text-start">{{ $value['label']
                                                        }}</td>
                                                    <td data-label="View">
                                                        @if(!empty($value['access']['view']))
                                                        <input type="checkbox" value="{{ join(" ,",
                                                            $value['access']['view']) }}" name="access[]" />
                                                        @endif
                                                    </td>
                                                    <td data-label="Add">
                                                        @if(!empty($value['access']['add']))
                                                        <input type="checkbox" value="{{ join(" ,",
                                                            $value['access']['add']) }}" name="access[]" />
                                                        @endif
                                                    </td>
                                                    <td data-label="Edit">
                                                        @if(!empty($value['access']['edit']))
                                                        <input type="checkbox" value="{{ join(" ,",
                                                            $value['access']['edit']) }}" name="access[]" />
                                                        @endif
                                                    </td>
                                                    <td data-label="Delete">
                                                        @if(!empty($value['access']['delete']))
                                                        <input type="checkbox" value="{{ join(" ,",
                                                            $value['access']['delete']) }}" name="access[]" />
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
                        <h5 class="modal-title" id="exampleModalLabel3">Edit Manage Admin Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">  
                                <div class="col-md-6 mb-3">
                                    <label for="update-name" class="form-label">Name</label>
                                    <input type="text" name="update-name" id="update-name" class="form-control"
                                        placeholder="Enter Name">
                                        <span class="error-text update-name_error text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update-username" class="form-label">Username</label>
                                    <input type="text" id="update-username" name="update-username" class="form-control"
                                        placeholder="Enter Username">
                                    <span class="error-text update-username_error text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update-email" class="form-label">Email</label>
                                    <input type="email" name="update-email" id="update-email" class="form-control"
                                        placeholder="xxxx@xxx.xx">
                                    <span class="error-text update-email_error text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update-phone" class="form-label">Phone Number</label>
                                    <input type="text" name="update-phone" id="update-phone" class="form-control"
                                        placeholder="Enter Phone Number">
                                        <span class="error-text update-phone_error text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update-password" class="form-label">Password</label>
                                    <input type="password" name="update-password" id="update-password" class="form-control"
                                        placeholder="Enter Password">
                                        <span class="error-text update-password_error text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="update-status" class="form-label">Select Status :</label>
                                    <select name="update-status" id="update-status" class="form-control " required>
                                        <option value="1" @if(old('status')=='1' ) selected @endif>
                                            {{trans('Active')}}
                                        </option>
                                        <option value="0" @if(old('status')=='0' ) selected @endif>
                                            {{trans('DeActive')}}
                                        </option>
                                    </select>
                                    <span class="error-text update-status_error text-danger"></span>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h5 class="card-title">{{ trans('Accessibility') }}</h5>
                                        </div>
                                        <div class="card-body update-select-all-access">
                                        
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input updateSelectAllCheckBox" name="updateAccessAll"
                                                    id="updateSelectAllCheckBox">
                                                <label class="form-check-label" for="updateAccessAll">{{ trans('Select All')
                                                    }}</label>
                                            </div>

                                            <table class="table table-hover table-striped table-bordered text-center">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="text-start">@lang('Permissions')</th>
                                                        <th>@lang('View')</th>
                                                        <th>@lang('Add')</th>
                                                        <th>@lang('Edit')</th>
                                                        <th>@lang('Delete')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(config('role') as $key => $value)
                                                    <tr>
                                                        <td data-label="Permissions" class="text-left">
                                                            {{$value['label']}}</td>
                                                        <td data-label="View">
                                                            @if(!empty($value['access']['view']))
                                                            <input type="checkbox" value="{{join("
                                                                ,",$value['access']['view'])}}" name="update_access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Add">
                                                            @if(!empty($value['access']['add']))
                                                            <input type="checkbox" value="{{join("
                                                                ,",$value['access']['add'])}}" name="update_access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Edit">
                                                            @if(!empty($value['access']['edit']))
                                                            <input type="checkbox" value="{{join("
                                                                ,",$value['access']['edit'])}}" name="update_access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Delete">
                                                            @if(!empty($value['access']['delete']))
                                                            <input type="checkbox" value="{{join("
                                                                ,",$value['access']['delete'])}}" name="update_access[]" />
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-label-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>

                    </form>
            </div>

        </div>

    </div>
    @push('style')
    <link rel="stylesheet" href="{{asset('assets/DataTables/datatables.min.css')}}" />
    @endpush
    @push('js')
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    <script>
       // Handle edit button click
      $(document).on('click', '.editAdminBtn', function () {

        let id = $(this).data('id');
        let name = $(this).data('name');
        let username = $(this).data('username');
        let email = $(this).data('email');
        let phone = $(this).data('phone');
        let status = $(this).data('status');
        let accessAdmin = $(this).data('admin-access');

        $('#editForm #update-name').val(name);
        $('#editForm #update-username').val(username);
        $('#editForm #update-email').val(email);
        $('#editForm #update-phone').val(phone);
        $('#editForm #update-status').val(status);

        let updateUrl = $(this).data('route').replace(':id', id);
        // Set the action attribute for the form
        $('#editForm').attr('action', updateUrl);


        $('#editForm input[type="checkbox"]').prop('checked', false);

        // Parse and check checkboxes based on stored admin access
        if (accessAdmin) {
        try {
            let permissionsArray = Array.isArray(accessAdmin) ? accessAdmin : accessAdmin.split(',');
            // console.log(permissionsArray);
            // Iterate through checkboxes inside the modal and check the ones that match
            $('#editForm input[type="checkbox"]').each(function () {
                let checkboxValues = $(this).val().split(','); // Get checkbox values as an array
                if (checkboxValues.some(value => permissionsArray.includes(value.trim()))) {
                    $(this).prop('checked', true);
                }
            });

        } catch (e) {
            console.error("Error parsing accessAdmin:", e);
        }
        }
        });

        $(document).ready(function () {
            
            //For Add Admin Model
            $('.selectAll').on('click', function () {
            if ($(this).is(':checked')) {
                $(this).parents('.select-all-access').find('input[type="checkbox"]').attr('checked', 'checked');
            } else {
                $(this).parents('.select-all-access').find('input[type="checkbox"]').removeAttr('checked');
            }
            });

            $('.updateSelectAllCheckBox').on('click', function () {
                var isChecked = $(this).is(':checked'); // Get whether the checkbox is checked or not
                $(this).parents('.update-select-all-access').find('input[type="checkbox"]').prop('checked', isChecked); // Set the checked property
            });

            // Handle form submission via AJAX
            $('#staffTable').DataTable({
                processing: false,  // We will manually control the loading spinner
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('admin.staff') }}",
                    type: 'GET',
                    beforeSend: function () {
                        // Show the loader spinner when the DataTable starts loading
                        $('#tableLoader').removeClass('d-none'); // Show the spinner
                        // Disable interactions with the table (edit buttons, etc.)
                        $('#staffTable').css('pointer-events', 'none');
                    },
                    complete: function () {
                        // Hide the loader spinner once the DataTable has finished loading
                        $('#tableLoader').addClass('d-none'); // Hide the spinner
                        // Re-enable interactions with the table
                        $('#staffTable').css('pointer-events', 'auto');
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
                    { data: 'id', name: 'id' },
                    { data: 'username', name: 'username' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'status', name: 'status', orderable: false },
                    @if(adminAccessRoute(config('role.manage_staff.access.edit')))
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    @endif
                ],
                order: [[0, 'asc']], // Default sorting by SL column
                columnDefs: [
                    { targets: '_all', orderable: false }, // Disable sorting for all columns
                ],
                pageLength: 10, // Default page length
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


            //submit add Admin function 
            $('#storeStaffForm').submit(function (e) {
                e.preventDefault();  // Prevent default form submission
    
                // Clear previous errors
                $('.error-text').text('');
    
                // Collect form data
                var formData = new FormData(this);
    
                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),  // Form action URL
                    method: 'POST',
                    data: formData,
                    processData: false,  // Prevent jQuery from processing data
                    contentType: false,  // Prevent jQuery from setting content-type header
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function (response) {

                       if (response.success) {
                           

                            // Optionally, close the modal after success
                            $('#addUserModal').modal('hide'); // Close the modal
                            // Reset form after success
                            $('#storeStaffForm')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message, // Use the message from the response
                                timer: 2000, // Auto-close after 2 seconds
                                showConfirmButton: false
                            });
                        } else {
                            // You can also handle other scenarios (e.g., failure) here if necessary.
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong, please try again.',
                            });
                        }
                    },
                    error: function (response) {
                        // Handle validation errors
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
                                    scrollTop: offsetTop - modal.offset().top + modal.scrollTop() - 100 // Adjust for the modal header
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
           
            $(document).on('submit', '#editForm', function (e) {
                e.preventDefault();  // Prevent default form submission

                // Clear previous errors
                $('.error-text').text('');  // Clear any previous error messages

                // Get the DataTable instance
                var table = $('#staffTable').DataTable();
                
                // Get the current page index and the search query
                var currentPage = table.page();  // Get the current page index
                var searchValue = table.search();  // Get the current search value

                // Collect form data
                var formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),  // Form action URL (set dynamically)
                    method: 'POST',  // Ensure you're using the correct method (PUT for updating)
                    data: formData,
                    processData: false,  // Prevent jQuery from processing data
                    contentType: false,  // Prevent jQuery from setting content-type header
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    beforeSend: function () {
                        // Show the loader spinner and dim the table
                        $('#tableLoader').removeClass('d-none'); // Show the spinner overlay
                        $('#staffTable').css('pointer-events', 'none');  // Disable clicks on the table
                    },
                    complete: function () {
                        // Hide the loader spinner and re-enable table interactions
                        $('#tableLoader').addClass('d-none');  // Hide the spinner overlay
                        $('#staffTable').css('pointer-events', 'auto');  // Re-enable table clicks
                    },
                    success: function (response) {
                        if (response.success) {
                            // Close the modal after success
                            $('#editUserModal').modal('hide'); // Close the modal
                            // Reset form after success

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
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
                                text: response.message || 'Something went wrong, please try again.',
                            });
                        }
                    },
                    error: function (response) {
                        // Handle validation errors
                        var errors = response.responseJSON.errors;
                        var firstErrorField = null; // Track the first error field

                        // Loop through errors and show them
                        $.each(errors, function (key, value) {
                            // Show error next to each field
                            $('.' + key + '_error').text(value.join(', '));  // Join multiple errors if present

                            // Find the first field with an error and focus on it
                            var $field = $('#' + key); // Find the field by ID (make sure the IDs are correct in HTML)

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
                                    scrollTop: offsetTop - modal.offset().top + modal.scrollTop() - 100 // Adjust for the modal header
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

        });
         //submit edit admin function
         

    </script>
    @endpush
</x-admin-layout>