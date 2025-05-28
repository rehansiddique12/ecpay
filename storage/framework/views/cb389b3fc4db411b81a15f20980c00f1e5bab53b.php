<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <?php $__env->stopPush(); ?>
    <?php
    $currentRoute = Route::currentRouteName();
    ?>

     <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <?php if(adminAccessRoute(config('role.account_management.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.ewallet.accounts.details')); ?>" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_management.access.add'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.e_wallet_accounts.access.edit'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.on_off_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.on_off_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">On/Off Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_group.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.account_group')); ?>" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.gateways.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.gateway')); ?>" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.categories.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_category')); ?>" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>


                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <?php if(adminAccessRoute(config('role.categories.access.add'))): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal"
                        id="newCategoryButton">
                        Add Category
                    </button>
                    <?php endif; ?>
                </div>

                <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                    <thead class="thead-dark bg-primary">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Category Name'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div id="tableLoader" class="loading-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Processing...</span>
                    </div>
                </div>
            </div>


        </div>
    </div>

    
    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #7367f0" id="modalTopTitle"><?php echo app('translator')->get('Add New'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCategoryForm" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="pr-3">Category Name</label>
                            <input type="text" class="form-control" name="name" required />
                            <span class="name_error text-danger error-text"></span>
                        </div>
                        <div class="form-group mt-3">
                            <label class="pr-3">Status</label>
                            <select class="form-select" z-index="99999" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveCategoryBtn" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div id="editModal" class="modal modal-top fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary modal-colored-header">
                    <h5 class="modal-title" style="color: white" id="modalTopTitle"><?php echo app('translator')->get('Edit Record'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('POST'); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="pr-3">Category Name</label>
                            <input type="text" class="form-control" id="edit_name" name="edit_name" value="" required />
                            <span class="edit_name_error text-danger error-text"></span>
                        </div>
                        <div class="form-group mt-3">
                            <label class="pr-3">Status</label>
                            <select class="form-select" id="edit_status" name="edit_status" required>
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="submit" id="editSaveBtn" class="btn btn-primary"><?php echo app('translator')->get('Update'); ?></button>
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                aria-label="Close"><?php echo app('translator')->get('Close'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('.categories-show-table').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "<?php echo e(route('admin.account_management.add_category')); ?>", // Make sure this route returns JSON for DataTable
                        type: 'GET',
                        beforeSend: function () {
                            $('#tableLoader').removeClass('d-none');
                            $('.categories-show-table').css('pointer-events', 'none');
                        },
                        complete: function () {
                            $('#tableLoader').addClass('d-none');
                            $('.categories-show-table').css('pointer-events', 'auto');
                        },
                        dataSrc: function (json) {
                            if (json.error) {
                                Swal.fire('Error', json.error, 'error');
                                return [];
                            }
                            return json.data;
                        },
                        error: function (xhr, error, code) {
                            Swal.fire('Failed!', 'Could not load data: ' + error, 'error');
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'status', name: 'status' , orderable: false, searchable: false},
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 rows', '25 rows', '50 rows', 'All']
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        // processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Processing...</span></div>"
                    },
                    info: false
            });



        });

        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let btn = $('#saveCategoryBtn');

            // Clear previous errors
            $('.error-text').text('');

            // Disable button and show spinner
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "<?php echo e(route('admin.category.store')); ?>",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                   if (response.success) {
                        // Close modal, reset form, show success message
                        $('#newModal').modal('hide');
                        form[0].reset();
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false
                        });
                    }
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    var firstErrorField = null; // Track the first error field

                    // Loop through errors and show them
                    $.each(errors, function (key, value) {
                        // Show error next to each field
                        $('.' + key + '_error').text(value[0]);

                        // Find the first field with an error and focus on it
                        var $field = $('.' + key); // Find the field by class

                        // Only set firstErrorField if it hasn't been set already
                        if (!firstErrorField && $field.length) {
                            firstErrorField = $field; // Set the first error field
                        }
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save');
                }
            });
        });

        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            url= $(this).attr('action');
            $('.error-text').text('');
            let btn = $('#editSaveBtn');
            // Disable button
            btn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editModal').modal('hide');
                        $('#editCategoryForm')[0].reset();
                        $('.categories-show-table').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message, // Use the message from the response
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false
                        });
                    }
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    var firstErrorField = null; // Track the first error field

                    // Loop through errors and show them
                    $.each(errors, function (key, value) {
                        // Show error next to each field
                        $('.' + key + '_error').text(value[0]);

                        // Find the first field with an error and focus on it
                        var $field = $('.' + key); // Find the field by class

                        // Only set firstErrorField if it hasn't been set already
                        if (!firstErrorField && $field.length) {
                            firstErrorField = $field; // Set the first error field
                        }
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update');
                }
            });
        });

    </script>

    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/add_category.blade.php ENDPATH**/ ?>