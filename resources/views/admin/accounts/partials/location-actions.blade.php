<div class="dropdown text-center">
    @if (adminAccessRoute(config('role.categories.access.edit')) ||
            adminAccessRoute(config('role.categories.access.delete')))
        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-dots-vertical"></i>
        </button>

        <div class="dropdown-menu">
            @if (adminAccessRoute(config('role.categories.access.edit')))
                <a href="#" class="dropdown-item edit-roles" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="{{ $category->id }}" data-role="{{ $category->name }}"
                    data-status="{{ $category->status }}">
                    <i class="fa fa-edit text-warning me-2"></i> {{ __('accounts.edit') }}
                </a>
            @endif
            @if (adminAccessRoute(config('role.categories.access.delete')))
                <a href="#" class="dropdown-item delete-role" data-id="{{ $category->id }}">
                    <i class="fa fa-trash text-danger me-2"></i> {{ __('accounts.delete') }}
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
            title: "{{ __('accounts.are_you_sure') }}",
            text: "{!! __('accounts.warning_text') !!}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('accounts.yes_delete_it') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('admin.category.delete') }}', // Your delete route
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: roleId
                    },
                    success: function(response) {
                        // Handle success
                        Swal.fire(
                            "{{ __('accounts.deleted') }}",
                            response.message, // Success message
                            'success'
                        );

                        // Refresh the datatable
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        Swal.fire(
                            "{{ __('accounts.error') }}",
                            "{{ __('accounts.delete_error') }}",
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
            console.error('Category data missing');
            return;
        }

        let updateUrl = "{{ route('admin.category.update', ['id' => ':id']) }}".replace(':id', id);
        $('#editCategoryForm').attr('action', updateUrl);
        $('#edit_name').val(role);
        $('#edit_status').val(status);
    });
</script>
