<x-partner-layout :title="$pageTitle">
    @push('style')
    <link rel="stylesheet" href="{{asset('assets/DataTables/datatables.min.css')}}" />
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-4 mb-6">{{ $pageTitle }}</h4>
        @if(count($errors) > 0 )
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                @foreach($errors->all() as $key => $error)
                <li> {{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">

                {{-- @if(partnerAccessRoute(config('rolep.manage_staff.access.add'))) --}}
                <div class="d-flex justify-content-end mb-2 text-right">
                    <button data-bs-target="#addModal" data-bs-toggle="modal" class="btn btn-primary btn-sm"><i
                            class="fa fa-user-plus"></i> {{trans('Add New')}} </button>
                </div>
                {{-- @endif --}}

                <div class="">
                    <table id="partner_staff_table"
                        class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('SL')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('Email')</th>
                                <th scope="col">@lang('Phone')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!-- Loading Overlay (hidden initially) -->
                    <div id="tableLoader" class="loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Edit API Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editApiLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form id="editForm" role="form" method="POST" class="actionRoute" action=""
                        enctype="multipart/form-data" onsubmit="return submitForm(this);">
                        @csrf
                        @method('put')

                        <div class="modal-header">
                            <h5 class="modal-title" id="editApiLabel">{{ __('Edit Staff') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="{{ __('Close') }}"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <!-- Name -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('Name') }}:</label>
                                    <input class="form-control" id="edit_name" name="edit_name"
                                        placeholder="{{ __('Name') }}" value="" required autocomplete="off">
                                </div>

                                <!-- Username -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('Username') }}:</label>
                                    <input class="form-control" id="edit_username" name="edit_username"
                                        placeholder="{{ __('Username') }}" value="" required autocomplete="off">
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('E-Mail') }}:</label>
                                    <input class="form-control" id="edit_email" name="edit_email"
                                        placeholder="Email Address" value="" required autocomplete="off">
                                </div>

                                <!-- Phone -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('Phone') }}:</label>
                                    <input class="form-control" id="edit_phone" name="edit_phone"
                                        placeholder="{{ __('Mobile Number') }}" value="" required autocomplete="off">
                                </div>

                                <!-- Password -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('Password') }}:</label>
                                    <input type="password" name="password" placeholder="Password" autocomplete="off"
                                        class="form-control">
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark">{{ __('Confirm Password') }}:</label>
                                    <input id="edit_password_confirmation" type="password"
                                        name="edit_password_confirmation" placeholder="Password" autocomplete="off"
                                        class="form-control">
                                </div>

                                <!-- Status -->
                                <div class="form-group col-md-12 mt-3">
                                    <label class="text-dark">{{ __('Select Status') }}:</label>
                                    <select name="status" id="edit-event-status" class="form-control" required>
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('DeActive') }}</option>
                                    </select>
                                </div>

                                <!-- Access Control -->
                                <div class="form-group col-md-12 mt-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">{{ __('Accessibility') }}</h5>
                                        </div>
                                        <div class="card-body select-all-access">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input selectAll" type="checkbox"
                                                    name="accessAll" id="selectAllAccess">
                                                <label class="form-check-label" for="selectAllAccess">{{ __('Select
                                                    All') }}</label>
                                            </div>

                                            <div class="table-responsive">
                                                <table
                                                    class="table table-hover table-striped table-bordered text-center">
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
                                                        @foreach(config('rolep') as $key => $value)
                                                        <tr>
                                                            <td data-label="Permissions" class="text-left">
                                                                {{$value['label']}}</td>
                                                            <td data-label="View">
                                                                @if(!empty($value['access']['view']))
                                                                <input type="checkbox"
                                                                    value="{{join(',',$value['access']['view'])}}"
                                                                    name="edit_access[]" @if(in_array_any(
                                                                    $value['access']['view'], $data->admin_access??[] ))
                                                                checked
                                                                @endif
                                                                />
                                                                @endif
                                                            </td>
                                                            <td data-label="Add">
                                                                @if(!empty($value['access']['add']))
                                                                <input type="checkbox"
                                                                    value="{{join(',',$value['access']['add'])}}"
                                                                    name="edit_access[]"
                                                                    @if(in_array_any($value['access']['add'],
                                                                    $data->admin_access??[] ))
                                                                checked
                                                                @endif
                                                                />
                                                                @endif
                                                            </td>
                                                            <td data-label="Edit">
                                                                @if(!empty($value['access']['edit']))
                                                                <input type="checkbox"
                                                                    value="{{join(',',$value['access']['edit'])}}"
                                                                    name="edit_access[]"
                                                                    @if(in_array_any($value['access']['edit'],
                                                                    $data->admin_access??[]))
                                                                checked
                                                                @endif/>
                                                                @endif
                                                            </td>

                                                            <td data-label="Delete">
                                                                @if(!empty($value['access']['delete']))
                                                                <input type="checkbox"
                                                                    value="{{join(',',$value['access']['delete'])}}"
                                                                    name="edit_access[]" @if(in_array_any(
                                                                    $value['access']['delete'],
                                                                    $data->admin_access??[]))
                                                                checked
                                                                @endif
                                                                />
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
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-success">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Modal for Add button -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content ">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="myModalLabel">@lang('Manage Staff Role')</h4>
                        {{-- <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button> --}}
                    </div>

                    <form role="form" method="POST" class="actionRoute" action="{{route('partner.storeStaff')}}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('Name')}} :</label>
                                    <input class="form-control" id="name" name="name" placeholder="{{trans('Name')}}"
                                        value="{{old('name')}}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('Username')}} :</label>
                                    <input class="form-control " name="username" placeholder="{{trans('Username')}}"
                                        value="{{old('username')}}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('E-Mail')}} :</label>
                                    <input class="form-control " name="email" placeholder="Email Address"
                                        value="{{old('email')}}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('Phone')}} :</label>
                                    <input class="form-control " name="phone" placeholder="{{trans('Mobile Number')}}"
                                        value="{{old('phone')}}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('Password')}} :</label>
                                    <input type="password" name="password" placeholder="Password" class="form-control "
                                        value="{{old('password')}}" autocomplete="off">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> {{trans('Password')}} :</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        placeholder="Password" class="form-control " value="{{old('password')}}"
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="text-dark"> {{trans('Select Status')}} :</label>
                                    <select name="status" id="event-status" class="form-control " required>
                                        <option value="1" @if(old('status')=='1' ) selected @endif>
                                            {{trans('Active')}}
                                        </option>
                                        <option value="0" @if(old('status')=='0' ) selected @endif>
                                            {{trans('DeActive')}}
                                        </option>
                                    </select>
                                    <br>
                                </div>


                                <div class="form-group col-md-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between text-center">
                                            <h5 class="card-title text-center">{{trans('Accessibility')}}</h5>
                                        </div>

                                        <div class="card-body select-all-access">
                                            <div class="form-group">
                                                <label><input type="checkbox" class="selectAll" name="accessAll">
                                                    {{trans('Select All')}}</label>
                                            </div>

                                            <table class=" table table-hover table-striped table-bordered text-center">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-left">@lang('Permissions')</th>
                                                        <th>@lang('View')</th>
                                                        <th>@lang('Add')</th>
                                                        <th>@lang('Edit')</th>
                                                        <th>@lang('Delete')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(config('rolep') as $key => $value)
                                                    <tr>
                                                        <td data-label="Permissions" class="text-left">
                                                            {{$value['label']}}</td>
                                                        <td data-label="View">
                                                            @if(!empty($value['access']['view']))
                                                            <input type="checkbox"
                                                                value="{{ implode(',', array_map('trim', $value['access']['view'])) }}"
                                                                name="access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Add">
                                                            @if(!empty($value['access']['add']))
                                                            {{-- <input type="checkbox" value="{{join("
                                                                ,",$value['access']['add'])}}" name="access[]" /> --}}
                                                            <input type="checkbox"
                                                                value="{{ implode(',', array_map('trim', $value['access']['add'])) }}"
                                                                name="access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Edit">
                                                            @if(!empty($value['access']['edit']))
                                                            {{-- <input type="checkbox" value="{{join("
                                                                ,",$value['access']['edit'])}}" name="access[]" /> --}}
                                                            <input type="checkbox"
                                                                value="{{ implode(',', array_map('trim', $value['access']['edit'])) }}"
                                                                name="access[]" />
                                                            @endif
                                                        </td>
                                                        <td data-label="Delete">
                                                            @if(!empty($value['access']['delete']))
                                                            {{-- <input type="checkbox" value="{{join("
                                                                ,",$value['access']['delete'])}}" name="access[]" />
                                                            --}}
                                                            <input type="checkbox"
                                                                value="{{ implode(',', array_map('trim', $value['access']['delete'])) }}"
                                                                name="access[]" />
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
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('Save')</button>
                        </div>

                    </form>


                </div>
            </div>
        </div>



    </div>



    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/DataTables/datatables.min.js')}}"></script>
    <script>
        // Handle form submission via AJAX
        $('#partner_staff_table').DataTable({
            processing: false,  // We will manually control the loading spinner
            serverSide: true,
            stateSave: true,
            ajax: {
                url: "{{ route('partner.staff') }}",
                type: 'GET',
                beforeSend: function () {
                    // Show the loader spinner when the DataTable starts loading
                    $('#tableLoader').removeClass('d-none'); // Show the spinner
                    // Disable interactions with the table (edit buttons, etc.)
                    $('#partner_staff_table').css('pointer-events', 'none');
                },
                complete: function () {
                    // Hide the loader spinner once the DataTable has finished loading
                    $('#tableLoader').addClass('d-none'); // Hide the spinner
                    // Re-enable interactions with the table
                    $('#partner_staff_table').css('pointer-events', 'auto');
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

        $(document).ready(function () {
            "use strict";
            $('.selectAll').on('click', function () {
                if ($(this).is(':checked')) {
                    $(this).parents('.select-all-access').find('input[type="checkbox"]').attr('checked', 'checked')
                } else {
                    $(this).parents('.select-all-access').find('input[type="checkbox"]').removeAttr('checked')
                }
            });

            $('#addModal form').on('submit', function (e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                // Clear existing validation error messages
                form.find('.text-danger').remove();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function (response) {
                        $('#addModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        // If there are validation errors, display them in the modal
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, errorMessage) {
                                var inputField = form.find('[name="' + field + '"]');
                                var errorDiv = $('<div class="text text-danger">' + errorMessage[0] + '</div>');
                                inputField.closest('.form-group').append(errorDiv);
                            });
                        }
                    }
                });
            });

        })

        function submitForm(form) {
            event.preventDefault();

            let $form = $(form);
            let actionUrl = $form.attr('action');
            let formData = new FormData(form);

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    // Optional: disable button / show spinner
                },
                success: function (response) {
                    $('#editModal').modal('hide'); // Hide modal
                    $('#editForm')[0].reset(); // Optional: reset form
                    // Reload DataTable
                    if ($.fn.DataTable.isDataTable('#partner_staff_table')) {
                        $('#partner_staff_table').DataTable().ajax.reload(null, false); // false to stay on the current page
                    }
                    // Optional: Show success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Staff details updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                    });
                }
            });

            return false; // Prevent default form submit
        }
    </script>
    @endpush

</x-partner-layout>
