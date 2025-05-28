<a href="#" class="edit-method-btn" data-bs-toggle="modal" data-bs-target="#editMethodModal"
    data-id="<?php echo e($gateway->id); ?>" data-name="<?php echo e($gateway->name); ?>" data-currency="<?php echo e($gateway->currency); ?>"
    data-convention_rate="<?php echo e(getAmount($gateway->convention_rate)); ?>"
    data-min_amount="<?php echo e(getAmount($gateway->min_amount)); ?>" data-max_amount="<?php echo e(getAmount($gateway->max_amount)); ?>"
    data-min_withdrawal="<?php echo e(getAmount($gateway->min_withdrawal_amount)); ?>"
    data-max_withdrawal="<?php echo e(getAmount($gateway->max_withdrawal_amount)); ?>"
    data-percentage_charge="<?php echo e(getAmount($gateway->percentage_charge)); ?>"
    data-fixed_charge="<?php echo e(getAmount($gateway->fixed_charge)); ?>" data-note="<?php echo e($gateway->note); ?>"
    data-status="<?php echo e($gateway->status); ?>" data-image="<?php echo e(getFile(config('location.gateway.path') . $gateway->image)); ?>"
    data-parameters="<?php echo e(htmlentities(json_encode($gateway->parameters))); ?>">
    <i class="fa fa-edit text-primary me-2 fs-5"></i>
</a>


<script>
    // $(document).on('click', '.delete-role', function(e) {
    //     e.preventDefault();
    //     var roleId = $(this).data('id');

    //     // SweetAlert2 confirmation dialog
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "You won't be able to revert this!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, delete it!'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                 url: '<?php echo e(route("admin.accounts.payment.methods.deactivate")); ?>', // Your delete route
    //                 method: 'DELETE',
    //                 data: {
    //                     _token: '<?php echo e(csrf_token()); ?>',
    //                     code: roleId
    //                 },
    //                 success: function(response) {
    //                     // Handle success
    //                     Swal.fire(
    //                         'Deleted!',
    //                         response.message, // Success message
    //                         'success'
    //                     );

    //                     // Refresh the datatable
    //                     $('.categories-show-table').DataTable().ajax.reload(null, false);
    //                 },
    //                 error: function(xhr, status, error) {
    //                     // Handle error
    //                     Swal.fire(
    //                         'Error!',
    //                         'There was an error deleting the role.',
    //                         'error'
    //                     );
    //                 }
    //             });
    //         }
    //     });
    // });

    $(document).on('click', '.edit-method-btn', function(e) {
            e.preventDefault();

            let id = $(this).data('id');
            if (!id) {
                console.error('Method ID missing');
                return;
            }
            // console.log(($(this).data('name')));

            // Build the update URL dynamically
            let updateUrlTemplate = "<?php echo e(route('admin.deposit.accounts.update', ':id')); ?>";
            let updateUrl = updateUrlTemplate.replace(':id', id);
            $('#editMethodModal form').attr('action', updateUrl);

            // Fill inputs - use correct name attributes with 'edit_' prefix
            $("#editMethodModal input[name='edit_name']").val($(this).data("name"));
            $("#editMethodModal input[name='edit_currency']").val($(this).data("currency"));
            $("#editMethodModal input[name='edit_convention_rate']").val($(this).data('convention_rate'));
            $("#editMethodModal input[name='edit_minimum_deposit_amount']").val($(this).data('min_amount'));
            $("#editMethodModal input[name='edit_maximum_deposit_amount']").val($(this).data('max_amount'));
            $("#editMethodModal input[name='edit_minimum_withdrawal_amount']").val($(this).data('min_withdrawal'));
            $("#editMethodModal input[name='edit_maximum_withdrawal_amount']").val($(this).data('max_withdrawal'));
            $("#editMethodModal input[name='edit_percentage_charge']").val($(this).data('percentage_charge'));
            $("#editMethodModal input[name='edit_fixed_charge']").val($(this).data('fixed_charge'));
            $("#editMethodModal textarea[name='edit_note']").val($(this).data('note'));

            // Image preview - make sure you have an img tag with id="image_preview_container" inside modal
            let imageUrl = $(this).data('image');
            if (imageUrl) {
                $('#editMethodModal #image_preview_container').attr('src', imageUrl).show();
            } else {
                $('#editMethodModal #image_preview_container').attr('src', '').hide();
            }

            // Checkbox status - checked if status == 1 (active)
            let status = $(this).data('status');
            $("#editMethodModal input[name='edit_status']").prop("checked", status == 1);
    });

</script>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/partials/gateway-actions.blade.php ENDPATH**/ ?>