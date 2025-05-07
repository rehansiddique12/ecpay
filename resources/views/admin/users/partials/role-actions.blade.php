<div class="dropdown text-center">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <a href="#" class="dropdown-item edit-roles" data-bs-toggle="modal"
           data-bs-target="#editModal"
           data-id="{{ $role->id }}"
           data-role="{{ $role->name }}">
            <i class="fa fa-edit text-warning me-2"></i> @lang('Edit')
        </a>

        <a href="#" class="dropdown-item delete-role" data-id="{{ $role->id }}">
            <i class="fa fa-trash text-danger me-2"></i> @lang('Delete')
        </a>
    </div>
</div>

<script>
    $(document).on('click', '.delete-role', function(e) {
        e.preventDefault();
        var roleId = $(this).data('id');

        // SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.roles.delete") }}', // Your delete route
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: roleId
                    },
                    success: function(response) {
                        // Handle success
                        Swal.fire(
                            'Deleted!',
                            response.message, // Success message
                            'success'
                        );

                        // Refresh the datatable
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        Swal.fire(
                            'Error!',
                            'There was an error deleting the role.',
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

        if (!id || !role) {
            console.error('Role data missing');
            return;
        }

        let updateUrl = "{{ route('admin.roles.update', ['id' => ':id']) }}".replace(':id', id);
        $('#editRolesForm').attr('action', updateUrl);
        $('#edit_roles_name').val(role);
    });
</script>
