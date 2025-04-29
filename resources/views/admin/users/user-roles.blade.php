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
                            <button
                                class="btn {{ $currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : '' }}">
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

                <div class="card card-primary my-4 shadow">
                    <div class="card-body">

                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                Add New Role
                            </button>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#cloneModal">
                                Clone Role Permission
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">@lang('No.')</th>
                                        <th scope="col">@lang('Roles Name')</th>
                                        <th scope="col">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($UserRoles as $index => $role)
                                        <tr>
                                            <td data-label="@lang('No.')">{{ $index + 1 }}</td>
                                            <td data-label="@lang('Roles Name')">{{ $role->roles_name }}</td>

                                            <td data-label="@lang('Action')">
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="#" class="dropdown-item edit-roles"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            data-id="{{ $role->id }}"
                                                            data-role="{{ $role->roles_name }}">
                                                            <i class="fa fa-edit text-warning me-2"></i>
                                                            @lang('Edit')
                                                        </a>

                                                        <form action="{{ route('admin.roles.delete', $role->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item"
                                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                                <i class="fa fa-trash text-danger me-2"></i>
                                                                @lang('Delete')
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-danger" colspan="3">@lang('No Roles Data')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{ $UserRoles->appends(request()->query())->links('partials.pagination') }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Role Modal --}}
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addRolesForm" action="{{ route('admin.roles.add') }}" method="POST">
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cloneModal" tabindex="-1" aria-labelledby="cloneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addRolesForm" action="{{ route('admin.roles.add') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="cloneModalLabel">Clone Role Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <label for="roles_name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roles_name" name="role"
                            placeholder="Enter role name" required>

                    </div>
                    <div class="modal-body">

                        <label for="roles_names" class="form-labels"> Clone from</label>
                        <input type="text" class="form-control" id="roles_names" name="roles"
                            placeholder="Enter role name" required>

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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editRolesForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_roles_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="edit_roles_name" name="roles_name"
                                placeholder="Enter role name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    @push('js')
        <script>
            $(document).ready(function() {

                $('.edit-roles').click(function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    let role = $(this).data('role');

                    if (!id || !role) {
                        console.error('Role data missing');
                        return;
                    }

                    let updateUrl = "{{ route('admin.roles.update', ['id' => ':id']) }}".replace(':id', id);
                    $('#editRolesForm').attr('action', updateUrl);
                    $('#edit_roles_name').val(role);
                });

                $('#editModal').on('hidden.bs.modal', function() {
                    $('#editRolesForm')[0].reset();
                    $('#editRolesForm').attr('action', '');
                });

            });
        </script>
    @endpush

</x-admin-layout>
