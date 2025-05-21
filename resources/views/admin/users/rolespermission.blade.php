<x-admin-layout :title="$pageTitle">
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }

        .custom-checkbox input[type="checkbox"] {
            filter: invert(100%) brightness(1.7);
            width: 20px;
            height: 20px;

        }

    .custom-checkbox {
        transform: scale(1.5); /* Make checkbox bigger */
        margin: auto;
        display: block;
    }
   .custom-checkbox-lg {
        transform: scale(1.5); /* Makes checkbox bigger */
        margin-right: 10px;
        vertical-align: middle;
    }

    .custom-label-lg {
        font-size: 1.4rem; /* Increases label text size */
        font-weight: 500;
    }
    </style>
    @php
    $currentRoute = Route::currentRouteName();
    @endphp
    <div class="page-header m-0 m-md-4 my-4 m-md-0 p-5">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0">{{ $pageTitle }}</h3>
                <div class="row">
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
                <div class="assign-permissions-content">
                    @php
                    // You can pass a selected role from controller or set a default here

                    $selectedRoleId = old('role_select') ?? ($selectedRoleId ?? null);
                    // $storedPermissions = $selectedRole->admin_access ?? [];
                    // dd($selectedRoleId);
                    @endphp
                    <div class="row align-items-center mb-3 mt-4">
                        <h4 for="role_select" class="col-md-2">Role</h4>

                        <div class="col-md-4">

                            <select name="role_select" id="role_select" class="form-select">

                                <option value="">-- Select Role --</option>

                                @foreach ($roles_list as $role)

                                <option value="{{ $role->id }}" {{ $selectedRoleId==$role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedRoleId > 0)
                    </div>

                    <div class="form-group col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between text-center">
                                <h5 class="card-title text-center">{{trans('Accessibility')}}</h5>
                            </div>
                            <form role="form" method="POST" class="actionRoute"
                                action="{{route('admin.update_role_permissions' , $selectedRoleId)}}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body select-all-access">
                                    <div class="form-group">
                                        {{-- <label> --}}
                                            <input type="checkbox" class="selectAll custom-checkbox-lg mb-3" name="accessAll">
                                            <span class="custom-label-lg">{{trans('Select
                                            All')}}</span>
                                            {{-- </label> --}}
                                    </div>

                                    <table class=" table table-hover table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>@lang('Permissions')</th>
                                                <th class="text-center">@lang('View')</th>
                                                <th class="text-center">@lang('Add')</th>
                                                <th class="text-center">@lang('Edit')</th>
                                                <th class="text-center">@lang('Delete')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="permissionsTableBody">
                                            @foreach(config('role') as $key => $value)
                                            <tr>
                                                <td data-label="Permissions" class="text-left">{{$value['label']}}</td>
                                                <td data-label="View">
                                                    @if(!empty($value['access']['view']))
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="{{join(',',$value['access']['view'])}}" name="access[]"
                                                        @if(in_array_any( $value['access']['view'],
                                                        $storedPermissions??[] )) checked @endif />
                                                    @endif
                                                </td>
                                                <td data-label="Add">
                                                    @if(!empty($value['access']['add']))
                                                    <input type="checkbox" class="custom-checkbox" value="{{join(',',$value['access']['add'])}}"
                                                        name="access[]" @if(in_array_any($value['access']['add'],
                                                        $storedPermissions??[] )) checked @endif />
                                                    @endif
                                                </td>
                                                <td data-label="Edit">
                                                    @if(!empty($value['access']['edit']))
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="{{join(',',$value['access']['edit'])}}" name="access[]"
                                                        @if(in_array_any($value['access']['edit'],
                                                        $storedPermissions??[])) checked @endif />
                                                    @endif
                                                </td>

                                                <td data-label="Delete">
                                                    @if(!empty($value['access']['delete']))
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="{{join(',',$value['access']['delete'])}}" name="access[]"
                                                        @if(in_array_any( $value['access']['delete'],
                                                        $storedPermissions??[])) checked @endif />
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        </tbody>
                                    </table>

                                </div>
                                <!-- Action Buttons -->
                                <div class="row mb-4">
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary me-2" id="updatePermissions">
                                            <i class="fas fa-save"></i> @lang('Update Permissions')
                                        </button>
                                        {{-- <button type="button" class="btn btn-success" id="createNewRole">
                                            <i class="fas fa-plus"></i> @lang('Create New Role')
                                        </button> --}}
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>
                    @endif

                </div>
            </div>

            @push('js')
            <script>
                $(document).ready(function() {
                    // Load permissions when role changes
                    $('#role_select').change(function() {
                        const roleId = $(this).val();
                        if (roleId) {
                            window.location.href = `?role_select=${roleId}`; // Reload page with selected role
                        }
                    });

                    // Select All Checkbox
                    $('.selectAll').click(function() {
                        $('input[name="access[]"]').prop('checked', $(this).prop('checked'));
                    });
                });
            </script>
            @endpush
</x-admin-layout>
