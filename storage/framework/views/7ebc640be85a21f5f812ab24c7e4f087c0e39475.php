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
                                    <div data-i18n="Users">Users</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.manage_location.access.view'))): ?>
                        <div>
                            <button class="btn <?php echo e($currentRoute == 'admin.location' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.location')); ?>" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_and_permission.access.add'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.roles_and_permission')); ?>" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_category.access.view'))): ?>
                        <div>
                            <button class="btn <?php echo e($currentRoute == 'admin.rolescategory' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.rolescategory')); ?>" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                
                        
                            <?php if(adminAccessRoute(config('role.manage_location.access.add'))): ?>
                            <button type="button" class="btn btn-primary  mt-5 mb-3" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                Add New Location
                            </button>
                            <?php endif; ?>
                            

                        <div class="">
                            <table id="locationTableBody"
                                class="categories-show-table table table-hover table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 50px;"><?php echo app('translator')->get('No.'); ?></th>
                                        <th style="width: 200px;"><?php echo app('translator')->get('Location'); ?></th>
                                        <th style="width: 120px;"><?php echo app('translator')->get('Status'); ?></th>
                                        <?php if(adminAccessRoute(config('role.manage_location.access.edit')) || adminAccessRoute(config('role.manage_location.access.delete')) ): ?>
                                        <th style="width: 100px;" class="text-center"><?php echo app('translator')->get('Action'); ?></th>
                                        <?php endif; ?>
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
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newModalLabel">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addLocationForm" action="<?php echo e(route('admin.users.location.add')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location:</label>
                            <input type="text" class="form-control" id="location" name="location" required
                                placeholder="Enter location">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status:</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addLocationForm" id="saveLocationBtn" class="btn btn-primary">Save
                        Location</button>
                    <div id="formErrors" class="text-danger mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLocationForm" action="" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Location:</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required
                                placeholder="Enter location">
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status:</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editLocationForm" id="editLocationBtn" class="btn btn-primary">Update
                        Location</button>
                    <div id="formErrors1" class="text-danger mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script>
        jQuery(document).ready(function() {
                // jQuery(document).on('click', '.edit-location', function(e) {
                //     e.preventDefault();
                //     try {
                //         var id = jQuery(this).data('id');
                //         var location = jQuery(this).data('location');
                //         var status = jQuery(this).data('status');

                //         // Validate data
                //         if (!id || !location) {
                //             console.error('Missing required data');
                //             return;
                //         }

                //         // Populate form fields
                //         jQuery('#edit_id').val(id);
                //         jQuery('#edit_location').val(location).trigger('change');
                //         jQuery('#edit_status').val(status).trigger('change');

                //         // Update form action URL
                //         var updateUrl = "<?php echo e(route('admin.location.update', '')); ?>/" + id;
                //         jQuery('#editLocationForm').attr('action', updateUrl);

                //         // Debugging logs
                //         console.log('Edit form populated:', {
                //             id: id,
                //             location: location,
                //             status: status
                //         });
                //     } catch (error) {
                //         console.error('Error populating edit form:', error);
                //     }
                // });

            $('#locationTableBody').DataTable({
                processing: false,  // We will manually control the loading spinner
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: "<?php echo e(route('admin.location')); ?>",
                    type: 'GET',
                    // data: function (d) {
                    //     d.location = $('#filterLocation').val();
                    //     d.role_type = $('#filterRole').val();
                    //     d.status = $('#filterStatus').val();
                    // },
                    beforeSend: function () {
                        // Show the loader spinner when the DataTable starts loading
                        $('#tableLoader').removeClass('d-none'); // Show the spinner
                        // Disable interactions with the table (edit buttons, etc.)
                        $('#locationTableBody').css('pointer-events', 'none');
                    },
                    complete: function () {
                        // Hide the loader spinner once the DataTable has finished loading
                        $('#tableLoader').addClass('d-none'); // Hide the spinner
                        // Re-enable interactions with the table
                        $('#locationTableBody').css('pointer-events', 'auto');
                    },
                    dataSrc: function (json) {
                        // console.log("DataTables Response:", json);
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
                    { data: 'id', name: 'id' },
                    { data: 'location', name: 'location' },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[0, 'asc']], // Default sorting by SL column
                columnDefs: [
                    { targets: '_all', orderable: false }, // Disable sorting for all columns
                ],
                pageLength: 50, // Default page length
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'All']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Processing...</span></div> <!-- You can customize this text -->", // Custom processing message with spinner
                },
                info: false, // Hide "Showing X to Y of Z entries" text
            });

            //addForm
            $('#addLocationForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = $('#saveLocationBtn');

                submitBtn.prop('disabled', true).text('Saving...'); // Disable button and show loading text

                $('#formErrors').html(''); // Clear previous errors

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Location added',
                            text: 'The new location has been successfully added!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Close the modal
                        $('#newModal').modal('hide');

                        // Reset the form
                        form[0].reset();

                        // Reload DataTable
                        $('#locationTableBody').DataTable().ajax.reload(null, false); // false = retain pagination
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul>';
                            $.each(errors, function (key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#formErrors').html(errorHtml);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again later.',
                            });
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).text('Save Location'); // Re-enable button
                    }
                });
            });

            $('#editLocationForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = $('#editLocationBtn');

                submitBtn.prop('disabled', true).text('Updating...'); // Disable button and show loading text

                $('#formErrors1').html(''); // Clear previous errors

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Location Updated',
                            text: 'The location has been Updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Close the modal
                        $('#editModal').modal('hide');

                        // Reset the form
                        form[0].reset();

                        // Reload DataTable
                        $('#locationTableBody').DataTable().ajax.reload(null, false); // false = retain pagination
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul>';
                            $.each(errors, function (key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#formErrors1').html(errorHtml);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again later.',
                            });
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).text('Update Location'); // Re-enable button
                    }
                });
            });

            //make  location  status    active/inactive
            $(document).on('click', '.toggle-status', function () {
                let locationId = $(this).data('id');
                let $this = $(this);

                // Preserve original size
                const originalWidth = $this.outerWidth();
                const originalHeight = $this.outerHeight();

                // Set spinner in place of the badge
                $this.css({
                    width: originalWidth,
                    height: originalHeight,
                    padding: 0,
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                }).html(`
                    <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                `);

                $.ajax({
                    url: "<?php echo e(route('admin.location.toggleStatus')); ?>",
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        id: locationId
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#locationTableBody').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong!'
                        });
                    }
                });
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/users/user-location.blade.php ENDPATH**/ ?>