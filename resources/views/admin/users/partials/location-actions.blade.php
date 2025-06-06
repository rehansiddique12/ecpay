<div class="dropdown text-center">
    @if (adminAccessRoute(config('role.manage_location.access.edit')) ||
            adminAccessRoute(config('role.manage_location.access.delete')))
        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-dots-vertical"></i>
        </button>

        <div class="dropdown-menu">
            @if (adminAccessRoute(config('role.manage_location.access.edit')))
                <a href="#" class="dropdown-item edit-roles" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="{{ $location->id }}" data-role="{{ $location->location }}"
                    data-status="{{ $location->status }}">
                    <i class="fa fa-edit text-warning me-2"></i>
                    {{ __('userManagement.edit') }}
                </a>
            @endif
            @if (adminAccessRoute(config('role.manage_location.access.delete')))
                <a href="#" class="dropdown-item delete-role" data-id="{{ $location->id }}">
                    <i class="fa fa-trash text-danger me-2"></i> {{ __('userManagement.delete') }}
            @endif
            </a>
        </div>
    @endif
</div>

<script>
    $(document).on('click', '.delete-role', function(e) {
        e.preventDefault();
        var roleId = $(this).data('id');

        // SweetAlert2 confirmation dialog
        Swal.fire({
            title: "{{ __('userManagement.confirm_title') }}",
            text: "{!! __('userManagement.confirm_text') !!}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('userManagement.confirm_yes_delete') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('admin.users.location.delete') }}', // Your delete route
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: roleId
                    },
                    success: function(response) {
                        // Handle success
                        Swal.fire(
                            "{{ __('userManagement.deleted_title') }}",
                            response.message, // Success message
                            'success'
                        );

                        // Refresh the datatable
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        Swal.fire(
                            "{{ __('userManagement.error_title') }}",
                            "{{ __('userManagement.error_delete_role') }}",
                            'error'
                        );
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit-roles', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let role = $(this).data('role');
        let status = $(this).data('status');

        if (!id || !role) {
            console.error('Role data missing');
            return;
        }

        let updateUrl = "{{ route('admin.location.update', ['id' => ':id']) }}".replace(':id', id);
        $('#editLocationForm').attr('action', updateUrl);
        $('#edit_location').val(role);
        $('#edit_status').val(status);
    });
</script>
