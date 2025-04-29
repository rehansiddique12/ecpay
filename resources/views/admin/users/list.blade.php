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
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.users' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.users') }}" class="menu-link">
                                    <div data-i18n="Manual Gateway">Manual Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.location' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.location') }}" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.roles_and_permission') }}" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button class="btn {{ $currentRoute == 'admin.rolescategory' ? 'btn-primary' : '' }}">
                                <a href="{{ route('admin.rolescategory') }}" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>

                    </div>
                </div>
                <form action="{{ route('admin.users.search') }}" method="get">

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="mb-2">Location</label>
                                <select class="form-control" name="status" required>
                                    <option> Office 1</option>
                                    <option> Office 2</option>
                                    <option> Singapore</option>
                                    <option>HQ</option>
                                    <option> Office 3</option>
                                    <option> Office 4</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Roles" class="mb-2">Roles</label>
                                <select class="form-control" name="status" required>
                                    <option>SUPERUSER</option>
                                    <option>Full</option>
                                    <option>Finance</option>
                                    <option>QA</option>
                                    <option>Supervisor</option>
                                    <option>Customer Service</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Status" class="mb-2">Status</label>
                                <select name="status" class="form-control">

                                    <option value="1" @if (@request()->status == '1') selected @endif>
                                        @lang('ON')</option>
                                    <option value="0" @if (@request()->status == '0') selected @endif>
                                        @lang('OFF')</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="card-header">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                    Add User
                </button>
            </div>

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            {{-- <th scope="col" class="text-center">
                            <input type="checkbox" class="form-check-input check-all tic-check" name="check-all"
                                   id="check-all">
                            <label for="check-all"></label>
                        </th> --}}
                            <th scope="col">@lang('No.')</th>
                            <th scope="col">@lang('User')</th>
                            <th scope="col">@lang('Location')</th>
                            <th scope="col">@lang('Roles')</th>
                            <th scope="col">@lang('Last Login')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>

                                <td data-label="@lang('No.')">{{ loopIndex($users) + $loop->index }}</td>
                                <td data-label="@lang('User')">

                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="{{ getFile(config('location.user.path') . $user->image) }}"
                                                alt="user" class="rounded-circle" width="45" height="45">
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">@lang($user->username)
                                            </h5>
                                            <span class="text-muted font-14">@lang($user->email)</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="@lang('Location')">{{ $user->location }}</td>
                                <td data-label="@lang('Roles')">{{ $user->roles }}</td>

                                <td data-label="@lang('Last Login')">{{ diffForHumans($user->last_login) }}</td>
                                <td>
                                    <label class="switch" style="pointer-events: none;">
                                        <input type="checkbox"
                                            class="switch-input {{ $user['status'] == 1 ? 'is-valid' : 'is-invalid' }}"
                                            {{ $user['status'] == 1 ? 'checked' : '' }}>

                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>

                                        <span class="switch-label">
                                            {{ $user['status'] == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </td>
                                <td data-label="@lang('Action')">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="{{ route('admin.user-edit', $user->id) }}">
                                                <i class="fa fa-edit text-warning pr-2" aria-hidden="true"></i>
                                                @lang('Edit')
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.send-email', $user->id) }}">
                                                <i class="fa fa-envelope text-success pr-2" aria-hidden="true"></i>
                                                @lang('Send Email')
                                            </a>
                                            <button data-toggle="modal" data-target="#login_as_user"
                                                class="dropdown-item user-login" data-id="{{ $user->id }}">
                                                <i class="fa fa-sign-in-alt text-primary pr-2" aria-hidden="true"></i>
                                                @lang('Login as User')
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-danger" colspan="9">@lang('No User Data')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $users->appends(@$search)->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="all_active" role="dialog">
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
                    <button type="button" class="btn btn-light"
                        data-dismiss="modal"><span>@lang('No')</span></button>
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
                    <button type="button" class="btn btn-light"
                        data-dismiss="modal"><span>@lang('No')</span></button>
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
                    <button type="button" class="btn btn-light"
                        data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="{{ route('admin.userLogin') }}" method="post" class="update-action">
                        @csrf
                        <input type="hidden" class="userId" name="userId" value="" />
                        <button type="submit" class="btn btn-primary"><span>@lang('Yes')</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" action="{{ route('admin.user.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">User Name:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location:</label>
                            <input type="text" class="form-control" id="location" name="location" required placeholder="Enter location">
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Roles:</label>
                            <input type="text" class="form-control" id="Roles" name="Roles" required placeholder="Enter Role">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addUserForm" class="btn btn-primary">Save User</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            "use strict";

            $(document).on('click', '.user-login', function() {
                var id = $(this).data('id');
                $('.userId').val(id);
            });

            $(document).on('click', '#check-all', function() {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $(document).on('change', ".row-tic", function() {
                let length = $(".row-tic").length;
                let checkedLength = $(".row-tic:checked").length;
                if (length == checkedLength) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }
            });

            //dropdown menu is not working
            $(document).on('click', '.dropdown-menu', function(e) {
                e.stopPropagation();
            });

            //multiple active
            $(document).on('click', '.active-yes', function(e) {
                e.preventDefault();
                var allVals = [];
                $(".row-tic:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                var strIds = allVals;

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                    url: "{{ route('admin.user-multiple-active') }}",
                    data: {
                        strIds: strIds
                    },
                    datatType: 'json',
                    type: "post",
                    success: function(data) {
                        location.reload();

                    },
                });
            });

            //multiple deactive
            $(document).on('click', '.inactive-yes', function(e) {
                e.preventDefault();
                var allVals = [];
                $(".row-tic:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                var strIds = allVals;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                    url: "{{ route('admin.user-multiple-inactive') }}",
                    data: {
                        strIds: strIds
                    },
                    datatType: 'json',
                    type: "post",
                    success: function(data) {
                        location.reload();

                    }
                });
            });


            $(document).ready(function() {
                $('select').select2({
                    selectOnClose: true
                });
            });
        </script>
    @endpush
</x-admin-layout>
