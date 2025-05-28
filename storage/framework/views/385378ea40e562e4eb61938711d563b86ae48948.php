<div class="dropdown text-center">
    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-base ti tabler-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu">
        <?php if(partnerAccessRoute(config('rolep.manage_staff.access.edit'))): ?>
        <li>
            <a href="#" class="dropdown-item edit-roles" data-bs-toggle="modal" data-bs-target="#editModal"
                data-id="<?php echo e($admin->id); ?>" data-name="<?php echo e($admin->name); ?>" data-username="<?php echo e($admin->username); ?>"
                data-email="<?php echo e($admin->email); ?>" data-phone="<?php echo e($admin->phone); ?>" data-status="<?php echo e($admin->status); ?>"
                data-access="<?php echo e(json_encode($admin->admin_access)); ?>">
                <i class="fa fa-edit text-warning me-2"></i> <?php echo app('translator')->get('Edit'); ?>
            </a>
        </li>

        <li>
            <form action="<?php echo e(route('partner.apis.reset', $admin->id)); ?>" method="GET" class="d-inline">
                <button type="submit" class="dropdown-item btn btn-link p-0">
                    <i class="fa fa-key text-primary me-2"></i> <?php echo app('translator')->get('Reset Password'); ?>
                </button>
            </form>
        </li>
        <?php endif; ?>

        <?php if(partnerAccessRoute(config('rolep.manage_staff.access.delete'))): ?>
        <li>
            <a href="#" class="dropdown-item delete-role" data-id="<?php echo e($admin->id); ?>">
                <i class="fa fa-trash text-danger me-2"></i> <?php echo app('translator')->get('Delete'); ?>
            </a>
        </li>
        <?php endif; ?>
    </ul>
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
                    url: '<?php echo e(route("partner.apis.delete")); ?>', // Your delete route
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
                        $('#partner_staff_table').DataTable().ajax.reload(null, false);
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

   $(document).on('click', '.edit-roles', function (e) {
        e.preventDefault();

        let id = $(this).data('id');
        let name = $(this).data('name');
        let username = $(this).data('username');
        let email = $(this).data('email');
        let phone = $(this).data('phone');
        let status = $(this).data('status');
         let access = $(this).data('access');

        // Parse JSON string to array if necessary
        if (typeof access === 'string') {
            access = JSON.parse(access);
        }
        console.log(access);
        let updateUrl = "<?php echo e(route('partner.updateStaff', ['id' => ':id'])); ?>".replace(':id', id);
        $('#editForm').attr('action', updateUrl);

        $('#edit_name').val(name);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit-event-status').val(status);

         $("input[name='edit_access[]']").each(function () {
            let checkboxPermissions = $(this).val().split(',').map(p => p.trim());
            let shouldCheck = checkboxPermissions.some(p => access.includes(p));
            $(this).prop('checked', shouldCheck);
        });
    });



</script>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/staff/action_menu.blade.php ENDPATH**/ ?>