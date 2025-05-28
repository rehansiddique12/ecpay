<div class="dropdown text-center">
    <?php if(adminAccessRoute(config('role.manage_location.access.edit')) ||
    adminAccessRoute(config('role.manage_location.access.delete'))): ?>
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-dots-vertical"></i>
    </button>

    <div class="dropdown-menu">
        <?php if(adminAccessRoute(config('role.manage_location.access.edit'))): ?>
        <a href="#" class="dropdown-item edit-roles" data-bs-toggle="modal" data-bs-target="#editModal"
            data-id="<?php echo e($location->id); ?>" data-role="<?php echo e($location->location); ?>" data-status="<?php echo e($location->status); ?>">
            <i class="fa fa-edit text-warning me-2"></i> <?php echo app('translator')->get('Edit'); ?>
        </a>
        <?php endif; ?>
        <?php if(adminAccessRoute(config('role.manage_location.access.delete'))): ?>
        <a href="#" class="dropdown-item delete-role" data-id="<?php echo e($location->id); ?>">
            <i class="fa fa-trash text-danger me-2"></i> <?php echo app('translator')->get('Delete'); ?>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>
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
                    url: '<?php echo e(route("admin.users.location.delete")); ?>', // Your delete route
                    method: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
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
        let status = $(this).data('status');

        if (!id || !role) {
            console.error('Role data missing');
            return;
        }

        let updateUrl = "<?php echo e(route('admin.location.update', ['id' => ':id'])); ?>".replace(':id', id);
        $('#editLocationForm').attr('action', updateUrl);
        $('#edit_location').val(role);
        $('#edit_status').val(status);
    });
</script>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/users/partials/location-actions.blade.php ENDPATH**/ ?>