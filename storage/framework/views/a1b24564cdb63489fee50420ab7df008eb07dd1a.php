<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>
    <?php
        $currentRoute = Route::currentRouteName();
    ?>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <?php if(adminAccessRoute(config('role.manage_staff.access.view'))): ?>
                            <div>
                                <button class="btn <?php echo e($currentRoute == 'admin.users' ? 'btn-primary' : ''); ?>">
                                    <a href="<?php echo e(route('admin.users')); ?>" class="menu-link">
                                        <div data-i18n="Users"><?php echo e(__('userManagement.user')); ?></div>
                                    </a>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.manage_location.access.view'))): ?>
                            <div>
                                <button class="btn <?php echo e($currentRoute == 'admin.location' ? 'btn-primary' : ''); ?>">
                                    <a href="<?php echo e(route('admin.location')); ?>" class="menu-link">
                                        <div data-i18n="Location"><?php echo e(__('userManagement.location')); ?></div>
                                    </a>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_and_permission.access.add'))): ?>
                            <div>
                                <button
                                    class="btn <?php echo e($currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : ''); ?>">
                                    <a href="<?php echo e(route('admin.roles_and_permission')); ?>" class="menu-link">
                                        <div data-i18n="Roles and Permission">
                                            <?php echo e(__('userManagement.roles_and_permission')); ?></div>
                                    </a>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_category.access.view'))): ?>
                            <div>
                                <button class="btn <?php echo e($currentRoute == 'admin.rolescategory' ? 'btn-primary' : ''); ?>">
                                    <a href="<?php echo e(route('admin.rolescategory')); ?>" class="menu-link">
                                        <div data-i18n="Roles Category"><?php echo e(__('userManagement.roles_category')); ?></div>
                                    </a>
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="mb-2"><?php echo e(__('userManagement.location')); ?></label>
                            <select class="form-select" name="location" id="filterLocation">
                                <option value=""><?php echo e(__('userManagement.select_location')); ?></option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"
                                        <?php if(@request()->location == $key): ?> selected <?php endif; ?>>
                                        <?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Roles" class="mb-2"><?php echo e(__('userManagement.roles')); ?></label>
                            <select class="form-select" name="role_type" id="filterRole">
                                <option value=""><?php echo e(__('userManagement.select_roles')); ?></option>
                                <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role); ?>"
                                        <?php if(@request()->roles == $role): ?> selected <?php endif; ?>>
                                        <?php echo e($role); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Status" class="mb-2"><?php echo e(__('userManagement.status')); ?></label>
                            <select name="status" class="form-select" id="filterStatus">
                                <option value=""><?php echo e(__('userManagement.all')); ?></option>
                                <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.on')); ?></option>

                                <option value="0" <?php if(@request()->status == '0'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.off')); ?></option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <?php if(adminAccessRoute(config('role.manage_staff.access.add'))): ?>
                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal"
                    data-bs-target="#addUserModal">
                    <?php echo e(__('userManagement.add_user')); ?>

                </button>
            <?php endif; ?>

            <div class="">
                <table id="staffTable" class="table table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 50px;"><?php echo e(__('userManagement.no')); ?></th>
                            <th style="width: 200px;"><?php echo e(__('userManagement.user')); ?></th>
                            <th style="width: 150px;"><?php echo e(__('userManagement.location')); ?></th>
                            <th style="width: 150px;"><?php echo e(__('userManagement.roles')); ?></th>
                            <th style="width: 100px;"><?php echo e(__('userManagement.status')); ?></th>
                            <?php if(adminAccessRoute(config('role.manage_staff.access.edit'))): ?>
                                <th style="width: 100px;"><?php echo e(__('userManagement.action')); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div id="tableLoader" class="loading-overlay d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"><?php echo e(__('userManagement.processing')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Add User Modal -->
    <div class="modal modal-top fade" id="addUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form id="storeStaffForm" role="form" method="POST" class="modal-content"
                action="<?php echo e(route('admin.storeStaff')); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo e(__('userManagement.add_new_admin')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.name')); ?>:</label>
                            <input class="form-control" name="name" placeholder="<?php echo e(__('userManagement.name')); ?>"
                                value="<?php echo e(old('name')); ?>" required>
                            <span class="error-text name_error text-danger"></span>
                            <!-- Error container for Name -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.username')); ?>:</label>
                            <input class="form-control" name="username"
                                placeholder="<?php echo e(__('userManagement.username')); ?>" value="<?php echo e(old('username')); ?>"
                                required>
                            <span class="error-text username_error text-danger"></span>
                            <!-- Error container for Username -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.email')); ?>:</label>
                            <input class="form-control" name="email"
                                placeholder="<?php echo e(__('userManagement.email_address')); ?>" value="<?php echo e(old('email')); ?>">
                            <span class="error-text email_error text-danger"></span>
                            <!-- Error container for Email -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.phone')); ?>:</label>
                            <input class="form-control" name="phone"
                                placeholder="<?php echo e(__('userManagement.mobile_number')); ?>" value="<?php echo e(old('phone')); ?>">
                            <span class="error-text phone_error text-danger"></span>
                            <!-- Error container for Phone -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.password')); ?>:</label>
                            <input type="password" name="password" placeholder="<?php echo e(__('userManagement.password')); ?>"
                                class="form-control" value="<?php echo e(old('password')); ?>" autocomplete="off">
                            <span class="error-text password_error text-danger"></span>
                            <!-- Error container for Password -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark"><?php echo e(__('userManagement.select_status')); ?>:</label>
                            <select name="status" id="event-status" class="form-select" required>
                                <option value="1" <?php if(old('status') == '1'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.active')); ?>

                                </option>
                                <option value="0" <?php if(old('status') == '0'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.deactive')); ?>

                                </option>
                            </select>

                            <span class="error-text status_error text-danger"></span>
                            <!-- Error container for Status -->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label"><?php echo e(__('userManagement.select_location')); ?>

                                :</label>
                            <select name="location" id="location" class="form-select" required>
                                <option value=""><?php echo e(__('userManagement.select_location')); ?></option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="error-text location_error text-danger"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role_type" class="form-label"><?php echo e(__('userManagement.select_role')); ?>

                                :</label>
                            <select name="role_type" id="role_type" class="form-select" required>
                                <option value=""><?php echo e(__('userManagement.select_role')); ?></option>
                                <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="error-text role_type_error text-danger"></span>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal"><?php echo e(__('userManagement.close')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('userManagement.save')); ?></button>

                </div>
            </form>
        </div>
    </div>

    
    <div class="modal modal-top fade" id="editUserModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm" role="form" class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3"><?php echo e(__('userManagement.edit_manage_admin_role')); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="update-name" class="form-label"><?php echo e(__('userManagement.name')); ?></label>
                            <input type="text" name="update-name" id="update-name" class="form-control"
                                placeholder="<?php echo e(__('userManagement.enter_name')); ?>">
                            <span class="error-text update-name_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-username"
                                class="form-label"><?php echo e(__('userManagement.username')); ?></label>
                            <input type="text" id="update-username" name="update-username" class="form-control"
                                placeholder="<?php echo e(__('userManagement.enter_username')); ?>">
                            <span class="error-text update-username_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-email" class="form-label"><?php echo e(__('userManagement.email')); ?></label>
                            <input type="email" name="update-email" id="update-email" class="form-control"
                                placeholder="<?php echo e(__('userManagement.enter_email')); ?>">
                            <span class="error-text update-email_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-phone"
                                class="form-label"><?php echo e(__('userManagement.phone_number')); ?></label>
                            <input type="text" name="update-phone" id="update-phone" class="form-control"
                                placeholder="<?php echo e(__('userManagement.enter_phone')); ?>">
                            <span class="error-text update-phone_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-password"
                                class="form-label"><?php echo e(__('userManagement.password')); ?></label>
                            <input type="password" name="update-password" id="update-password" class="form-control"
                                placeholder="<?php echo e(__('userManagement.enter_password')); ?>">
                            <span class="error-text update-password_error text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="update-status" class="form-label"><?php echo e(__('userManagement.select_status')); ?>

                                :</label>
                            <select name="update-status" id="update-status" class="form-select" required>
                                <option value="1" <?php if(old('status') == '1'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.active')); ?></option>
                                <option value="0" <?php if(old('status') == '0'): ?> selected <?php endif; ?>>
                                    <?php echo e(__('userManagement.deactive')); ?></option>
                            </select>
                            <span class="error-text update-status_error text-danger"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_location" class="form-label"><?php echo e(__('userManagement.select_location')); ?>

                                :</label>
                            <select name="edit_location" id="edit_location" class="form-select" required>
                                <option value=""><?php echo e(__('userManagement.select_location')); ?></option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="error-text location_error text-danger"></span>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="update-status" class="form-label"><?php echo e(__('userManagement.select_role')); ?>

                                :</label>
                            <select name="role_type_edit" id="role_type_edit" class="form-select" required>
                                <option><?php echo e(__('userManagement.select_role')); ?></option>
                                <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="error-text update-status_error text-danger"></span>
                        </div>

                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal"><?php echo e(__('userManagement.close')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('userManagement.save')); ?></button>
                </div>

            </form>
        </div>

    </div>



    <?php $__env->startPush('style'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/DataTables/datatables.min.css')); ?>" />
    <?php $__env->stopPush(); ?>


    <?php $__env->startPush('js'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
        <script>
            $(document).ready(function() {
                // Handle form submission via AJAX
                $('#staffTable').DataTable({
                    processing: false, // We will manually control the loading spinner
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "<?php echo e(route('admin.users')); ?>",
                        type: 'GET',
                        data: function(d) {
                            d.location = $('#filterLocation').val();
                            d.role_type = $('#filterRole').val();
                            d.status = $('#filterStatus').val();
                        },
                        beforeSend: function() {
                            // Show the loader spinner when the DataTable starts loading
                            $('#tableLoader').removeClass('d-none'); // Show the spinner
                            // Disable interactions with the table (edit buttons, etc.)
                            $('#staffTable').css('pointer-events', 'none');
                        },
                        complete: function() {
                            // Hide the loader spinner once the DataTable has finished loading
                            $('#tableLoader').addClass('d-none'); // Hide the spinner
                            // Re-enable interactions with the table
                            $('#staffTable').css('pointer-events', 'auto');
                        },
                        dataSrc: function(json) {
                            // console.log("DataTables Response:", json);
                            if (json.error) {
                                Swal.fire('Error', json.error, 'error');
                                return [];
                            }
                            return json.data;
                        },
                        error: function(xhr, error, code) {
                            Swal.fire('Failed!', 'Could not load data: ' + error, 'error');
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'location_name',
                            name: 'location_name'
                        },
                        {
                            data: 'role_type',
                            name: 'role_type'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false
                        },
                        <?php if(adminAccessRoute(config('role.manage_staff.access.edit'))): ?>
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        <?php endif; ?>
                    ],
                    order: [
                        [0, 'asc']
                    ], // Default sorting by SL column
                    columnDefs: [{
                            targets: '_all',
                            orderable: false
                        }, // Disable sorting for all columns
                    ],
                    pageLength: 50, // Default page length
                    lengthMenu: [
                        [10, 25, 50, -1],
                        ['10 rows', '25 rows', '50 rows', 'All']
                    ],
                    language: {
                        search: "_INPUT_",
                        // searchPlaceholder: "<?php echo __('userManagement.search_placeholder'); ?>",
                        // searchPlaceholder: <?php echo json_encode(__('userManagement.search_placeholder')); ?>,
                        searchPlaceholder: <?php echo json_encode(__('userManagement.search_placeholder'), 15, 512) ?>,
                        processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'><?php echo e(__('userManagement.processing')); ?></span></div> <!-- You can customize this text -->", // Custom processing message with spinner
                    },
                    info: false, // Hide "Showing X to Y of Z entries" text
                });
                // console.log("<?php echo e(__('userManagement.search_placeholder')); ?>");
                const searchPlaceholder = <?php echo json_encode(__('userManagement.search_placeholder'), 15, 512) ?>;
                console.log('searchPlaceholder:', searchPlaceholder);

                //submit add Admin function
                $('#storeStaffForm').submit(function(e) {
                    e.preventDefault(); // Prevent default form submission

                    // Clear previous errors
                    $('.error-text').text('');

                    // Collect form data
                    var formData = new FormData(this);

                    // Send AJAX request
                    $.ajax({
                        url: $(this).attr('action'), // Form action URL
                        method: 'POST',
                        data: formData,
                        processData: false, // Prevent jQuery from processing data
                        contentType: false, // Prevent jQuery from setting content-type header
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {
                            //console.log(response);
                            if (response.success) {

                                // Optionally, close the modal after success
                                $('#addUserModal').modal('hide'); // Close the modal
                                // Reset form after success
                                $('#storeStaffForm')[0].reset();
                                Swal.fire({
                                    icon: 'success',
                                    title: "<?php echo e(__('userManagement.success_title')); ?>",
                                    text: response
                                        .message, // Use the message from the response
                                    timer: 2000, // Auto-close after 2 seconds
                                    showConfirmButton: false
                                });
                            } else {
                                // You can also handle other scenarios (e.g., failure) here if necessary.
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: "<?php echo e(__('userManagement.something_wrong')); ?>",
                                });
                            }
                        },
                        error: function(response) {
                            // Handle validation errors
                            var errors = response.responseJSON.errors;
                            var firstErrorField = null; // Track the first error field

                            // Loop through errors and show them
                            $.each(errors, function(key, value) {
                                // Show error next to each field
                                $('.' + key + '_error').text(value[0]);

                                // Find the first field with an error and focus on it
                                var $field = $('.' + key); // Find the field by class

                                // Only set firstErrorField if it hasn't been set already
                                if (!firstErrorField && $field.length) {
                                    firstErrorField = $field; // Set the first error field
                                }
                            });

                            // If there's a field with an error, focus and scroll to it
                            if (firstErrorField && firstErrorField.length) {
                                // Focus on the first field with an error
                                firstErrorField.focus();

                                // Scroll to the first error field inside the modal
                                var modal = $('#addUserModal'); // Target the modal
                                var offsetTop = firstErrorField.offset().top;

                                // Check if the modal exists and adjust scrolling
                                if (modal.length) {
                                    modal.animate({
                                        scrollTop: offsetTop - modal.offset().top + modal
                                            .scrollTop() -
                                            100 // Adjust for the modal header
                                    }, 500); // Smooth scroll to the error field
                                } else {
                                    // Scroll to the first error field on the page if modal is not available
                                    $('html, body').animate({
                                        scrollTop: offsetTop -
                                            100 // Adjust 100px for some margin
                                    }, 500); // Smooth scroll to the error field
                                }
                            }
                        }
                    });
                });

            });

            // const debouncedReload = _.debounce(function () {
            //     staffTable.ajax.reload();
            // }, 500);

            // Apply the debounced function to the filter dropdowns
            // $('#filterLocation, #filterRole, #filterStatus').on('change', debouncedReload);

            $('#filterLocation, #filterRole, #filterStatus').on('change', function() {
                $('#staffTable').DataTable().ajax.reload();
            });

            //Edit Button Click Load Data Inside Model
            $(document).on('click', '.editAdminBtn', function() {

                let id = $(this).data('id');
                let name = $(this).data('name');
                let username = $(this).data('username');
                let email = $(this).data('email');
                let phone = $(this).data('phone');
                let status = $(this).data('status');
                let roleType = $(this).data('role-type');
                let location_id = $(this).data('location');

                $('#editForm #update-name').val(name);
                $('#editForm #update-username').val(username);
                $('#editForm #update-email').val(email);
                $('#editForm #update-phone').val(phone);
                $('#editForm #update-status').val(status);
                $('#editForm #role_type_edit').val(roleType);
                $('#editForm #edit_location').val(location_id);

                let updateUrl = $(this).data('route').replace(':id', id);
                // Set the action attribute for the form
                $('#editForm').attr('action', updateUrl);

            });

            //EditFormPostMethod
            $(document).on('submit', '#editForm', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Clear previous errors
                $('.error-text').text(''); // Clear any previous error messages

                // Get the DataTable instance
                var table = $('#staffTable').DataTable();

                // Get the current page index and the search query
                var currentPage = table.page(); // Get the current page index
                var searchValue = table.search(); // Get the current search value

                // Collect form data
                var formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'), // Form action URL (set dynamically)
                    method: 'POST', // Ensure you're using the correct method (PUT for updating)
                    data: formData,
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting content-type header
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    beforeSend: function() {
                        // Show the loader spinner and dim the table
                        $('#tableLoader').removeClass('d-none'); // Show the spinner overlay
                        $('#staffTable').css('pointer-events', 'none'); // Disable clicks on the table
                    },
                    complete: function() {
                        // Hide the loader spinner and re-enable table interactions
                        $('#tableLoader').addClass('d-none'); // Hide the spinner overlay
                        $('#staffTable').css('pointer-events', 'auto'); // Re-enable table clicks
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close the modal after success
                            $('#editUserModal').modal('hide'); // Close the modal
                            // Reset form after success

                            Swal.fire({
                                icon: 'success',
                                title: "<?php echo e(__('userManagement.success_title')); ?>",
                                text: response.message, // Use the message from the response
                                timer: 2000, // Auto-close after 2 seconds
                                showConfirmButton: false
                            }).then(function() {
                                // Reload the DataTable and maintain the current page and search state
                                table.page(currentPage).search(searchValue).draw(false);
                                $('#editForm')[0].reset();
                            });

                        } else {
                            // If response.success is false, handle failure scenario
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: response.message ||
                                    "<?php echo e(__('userManagement.something_wrong')); ?>",
                            });
                        }
                    },
                    error: function(response) {
                        // Handle validation errors
                        var errors = response.responseJSON.errors;
                        var firstErrorField = null; // Track the first error field

                        // Loop through errors and show them
                        $.each(errors, function(key, value) {
                            // Show error next to each field
                            $('.' + key + '_error').text(value.join(
                                ', ')); // Join multiple errors if present

                            // Find the first field with an error and focus on it
                            var $field = $('#' +
                                key
                            ); // Find the field by ID (make sure the IDs are correct in HTML)

                            // Only set firstErrorField if it hasn't been set already
                            if (!firstErrorField && $field.length) {
                                firstErrorField = $field; // Set the first error field
                            }
                        });

                        // If there's a field with an error, focus and scroll to it
                        if (firstErrorField && firstErrorField.length) {
                            // Focus on the first field with an error
                            firstErrorField.focus();

                            // Scroll to the first error field inside the modal
                            var modal = $('#editUserModal'); // Target the modal
                            var offsetTop = firstErrorField.offset().top;

                            // Check if the modal exists and adjust scrolling
                            if (modal.length) {
                                modal.animate({
                                    scrollTop: offsetTop - modal.offset().top + modal.scrollTop() -
                                        100 // Adjust for the modal header
                                }, 500); // Smooth scroll to the error field
                            } else {
                                // Scroll to the first error field on the page if modal is not available
                                $('html, body').animate({
                                    scrollTop: offsetTop - 100 // Adjust 100px for some margin
                                }, 500); // Smooth scroll to the error field
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.toggle-status', function() {
                const $badge = $(this);
                const url = $badge.data('url');

                // Preserve original button width and height
                const originalWidth = $badge.outerWidth();
                const originalHeight = $badge.outerHeight();

                // Replace badge content with centered spinner, preserving size and style
                $badge.css({
                    width: originalWidth,
                    height: originalHeight,
                    padding: 0,
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    // Optional: keep same font-size/color if needed
                }).html(`
                    <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                `);

                // Send POST request
                $.post(url, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#staffTable').DataTable().ajax.reload(null, false);
                        }
                    })
                    .fail(function() {
                        alert('Failed to update status.');
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
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/users/list.blade.php ENDPATH**/ ?>