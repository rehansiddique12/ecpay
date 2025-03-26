<x-admin-layout :title="$pageTitle">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            @if(adminAccessRoute(config('role.manage_staff.access.add')))
            <div class="card-header d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User
                </button>
            </div>
            @endif



            <div class="table-responsive text-nowrap">
                <table class="table table-sm">
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
                    <tbody>
                        @foreach($admins as $k => $data)
                        <tr>
                            <td data-label="SL">{{++$k}}</td>
                            <td data-label="Username"><strong>{{$data->username}}</strong></td>
                            <td data-label="Email">{{$data->email}}</td>
                            <td data-label="Phone">{{$data->phone}}</td>

                            <td>
                                <span
                                    class="badge rounded-pill text-bg-{{ $data->status == 0 ? 'danger' : 'success' }}">{{
                                    $data->status == 0 ? 'Deactive' : 'Active' }}
                                </span>
                            </td>
                            @if(adminAccessRoute(config('role.manage_staff.access.edit')))
                            <td data-label="@lang('Action')">
                                <button class="edit_button   btn btn-primary  text-white  btn-sm "
                                    data-target="#myModal{{$data->id}}" data-id="{{$data->id }}" data-toggle="modal">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination links -->
            <div class="card-footer">
                {{ $admins->appends($_GET)->links('partials.pagination') }}
            </div>
        </div>
        {{-- Add User Models --}}
        <div class="modal modal-top fade" id="addUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
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
    </div>
    @push('js')
    <script>
        $(document).ready(function () {
            // Handle form submission via AJAX
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
        });
    </script>
    @endpush
</x-admin-layout>