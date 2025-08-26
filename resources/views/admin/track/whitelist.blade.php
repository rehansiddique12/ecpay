<x-admin-layout :title="$pageTitle">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
        <style>
            .select2-container--open {
                z-index: 9999 !important;
            }
        </style>
    @endpush

    <div class="card card-primary shadow">
        <div class="card-body">
            <h3 style="color: #7367f0">{{ $pageTitle }}</h3>

            {{-- Success message --}}
            {{-- @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif --}}

            @if (adminAccessRoute(config('role.ip_whitelist.access.add')))
                {{-- Add new button --}}
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#newModal">
                    Add New
                </button>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>IP Address</th>
                            <th>Admin</th>
                            <th>Created At</th>
                            @if (adminAccessRoute(config('role.ip_whitelist.access.edit')))
                            @if (adminAccessRoute(config('role.ip_whitelist.access.delete')))
                                <th>Actions</th>
                            @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($whitelists as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->ip_address }}</td>
                                <td>{{ $row->admin ? $row->admin->name : 'N/A' }}</td>
                                <td>{{ $row->created_at }}</td>
                              @if (adminAccessRoute(config('role.ip_whitelist.access.edit')))
                            @if (adminAccessRoute(config('role.ip_whitelist.access.delete')))
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $row->id }}">Edit</button>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.whitelist.delete', $row->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                @endif
                                @endif
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.whitelist.update', $row->id) }}">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit IP</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>IP Address</label>
                                                    <input type="text" name="ip_address" class="form-control"
                                                        value="{{ $row->ip_address }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Admin</label>
                                                    <select name="user_id" class="form-select form-select-sm select2"
                                                        style="position: relative; z-index: 10000;" required>
                                                        @foreach ($admins as $admin)
                                                            <option value="{{ $admin->id }}"
                                                                {{ $admin->id == $row->user_id ? 'selected' : '' }}>
                                                                {{ $admin->name }} ({{ $admin->username }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="newModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.whitelist.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New IP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>IP Address</label>
                            <input type="text" name="ip_address" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Admin</label>
                            <select name="user_id" class="form-select form-select-sm select2" required>
                                <option value="">-- Select Admin --</option>
                                @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script>
            let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                selectOnClose: false,
            });

            // Prevent dropdown from opening on clear
            $select.on('select2:unselecting', function(e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function(e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
                }
            });
        </script>
    @endpush
</x-admin-layout>
