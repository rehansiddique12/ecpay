<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/DataTables/datatables.min.css')); ?>" />
    <?php $__env->stopPush(); ?>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-4 mb-6"><?php echo e($pageTitle); ?></h4>
        <?php if(count($errors) > 0 ): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li> <?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">

                
                <div class="d-flex justify-content-end mb-2 text-right">
                    <button data-bs-target="#addModal" data-bs-toggle="modal" class="btn btn-primary btn-sm"><i
                            class="fa fa-user-plus"></i> <?php echo e(trans('Add New')); ?> </button>
                </div>
                

                <div class="">
                    <table id="partner_staff_table"
                        class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col"><?php echo app('translator')->get('SL'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Username'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Email'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Phone'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!-- Loading Overlay (hidden initially) -->
                    <div id="tableLoader" class="loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Edit API Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editApiLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form id="editForm" role="form" method="POST" class="actionRoute" action=""
                        enctype="multipart/form-data" onsubmit="return submitForm(this);">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('put'); ?>

                        <div class="modal-header">
                            <h5 class="modal-title" id="editApiLabel"><?php echo e(__('Edit Staff')); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="<?php echo e(__('Close')); ?>"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <!-- Name -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('Name')); ?>:</label>
                                    <input class="form-control" id="edit_name" name="edit_name"
                                        placeholder="<?php echo e(__('Name')); ?>" value="" required autocomplete="off">
                                </div>

                                <!-- Username -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('Username')); ?>:</label>
                                    <input class="form-control" id="edit_username" name="edit_username"
                                        placeholder="<?php echo e(__('Username')); ?>" value="" required autocomplete="off">
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('E-Mail')); ?>:</label>
                                    <input class="form-control" id="edit_email" name="edit_email"
                                        placeholder="Email Address" value="" required autocomplete="off">
                                </div>

                                <!-- Phone -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('Phone')); ?>:</label>
                                    <input class="form-control" id="edit_phone" name="edit_phone"
                                        placeholder="<?php echo e(__('Mobile Number')); ?>" value="" required autocomplete="off">
                                </div>

                                <!-- Password -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('Password')); ?>:</label>
                                    <input type="password" name="password" placeholder="Password" autocomplete="off"
                                        class="form-control">
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group col-md-6">
                                    <label class="text-dark"><?php echo e(__('Confirm Password')); ?>:</label>
                                    <input id="edit_password_confirmation" type="password"
                                        name="edit_password_confirmation" placeholder="Password" autocomplete="off"
                                        class="form-control">
                                </div>

                                <!-- Status -->
                                <div class="form-group col-md-12 mt-3">
                                    <label class="text-dark"><?php echo e(__('Select Status')); ?>:</label>
                                    <select name="status" id="edit-event-status" class="form-control" required>
                                        <option value="1"><?php echo e(__('Active')); ?></option>
                                        <option value="0"><?php echo e(__('DeActive')); ?></option>
                                    </select>
                                </div>

                                <!-- Access Control -->
                                <div class="form-group col-md-12 mt-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title"><?php echo e(__('Accessibility')); ?></h5>
                                        </div>
                                        <div class="card-body select-all-access">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input selectAll" type="checkbox"
                                                    name="accessAll" id="selectAllAccess">
                                                <label class="form-check-label" for="selectAllAccess"><?php echo e(__('Select
                                                    All')); ?></label>
                                            </div>

                                            <div class="table-responsive">
                                                <table
                                                    class="table table-hover table-striped table-bordered text-center">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th class="text-start"><?php echo app('translator')->get('Permissions'); ?></th>
                                                            <th><?php echo app('translator')->get('View'); ?></th>
                                                            <th><?php echo app('translator')->get('Add'); ?></th>
                                                            <th><?php echo app('translator')->get('Edit'); ?></th>
                                                            <th><?php echo app('translator')->get('Delete'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = config('rolep'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td data-label="Permissions" class="text-left">
                                                                <?php echo e($value['label']); ?></td>
                                                            <td data-label="View">
                                                                <?php if(!empty($value['access']['view'])): ?>
                                                                <input type="checkbox"
                                                                    value="<?php echo e(join(',',$value['access']['view'])); ?>"
                                                                    name="edit_access[]" <?php if(in_array_any(
                                                                    $value['access']['view'], $data->admin_access??[] )): ?>
                                                                checked
                                                                <?php endif; ?>
                                                                />
                                                                <?php endif; ?>
                                                            </td>
                                                            <td data-label="Add">
                                                                <?php if(!empty($value['access']['add'])): ?>
                                                                <input type="checkbox"
                                                                    value="<?php echo e(join(',',$value['access']['add'])); ?>"
                                                                    name="edit_access[]"
                                                                    <?php if(in_array_any($value['access']['add'],
                                                                    $data->admin_access??[] )): ?>
                                                                checked
                                                                <?php endif; ?>
                                                                />
                                                                <?php endif; ?>
                                                            </td>
                                                            <td data-label="Edit">
                                                                <?php if(!empty($value['access']['edit'])): ?>
                                                                <input type="checkbox"
                                                                    value="<?php echo e(join(',',$value['access']['edit'])); ?>"
                                                                    name="edit_access[]"
                                                                    <?php if(in_array_any($value['access']['edit'],
                                                                    $data->admin_access??[])): ?>
                                                                checked
                                                                <?php endif; ?>/>
                                                                <?php endif; ?>
                                                            </td>

                                                            <td data-label="Delete">
                                                                <?php if(!empty($value['access']['delete'])): ?>
                                                                <input type="checkbox"
                                                                    value="<?php echo e(join(',',$value['access']['delete'])); ?>"
                                                                    name="edit_access[]" <?php if(in_array_any(
                                                                    $value['access']['delete'],
                                                                    $data->admin_access??[])): ?>
                                                                checked
                                                                <?php endif; ?>
                                                                />
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                            <button type="submit" class="btn btn-success"><?php echo app('translator')->get('Update'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Modal for Add button -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content ">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="myModalLabel"><?php echo app('translator')->get('Manage Staff Role'); ?></h4>
                        
                    </div>

                    <form role="form" method="POST" class="actionRoute" action="<?php echo e(route('partner.storeStaff')); ?>"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('Name')); ?> :</label>
                                    <input class="form-control" id="name" name="name" placeholder="<?php echo e(trans('Name')); ?>"
                                        value="<?php echo e(old('name')); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('Username')); ?> :</label>
                                    <input class="form-control " name="username" placeholder="<?php echo e(trans('Username')); ?>"
                                        value="<?php echo e(old('username')); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('E-Mail')); ?> :</label>
                                    <input class="form-control " name="email" placeholder="Email Address"
                                        value="<?php echo e(old('email')); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('Phone')); ?> :</label>
                                    <input class="form-control " name="phone" placeholder="<?php echo e(trans('Mobile Number')); ?>"
                                        value="<?php echo e(old('phone')); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('Password')); ?> :</label>
                                    <input type="password" name="password" placeholder="Password" class="form-control "
                                        value="<?php echo e(old('password')); ?>" autocomplete="off">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-dark"> <?php echo e(trans('Password')); ?> :</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        placeholder="Password" class="form-control " value="<?php echo e(old('password')); ?>"
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="text-dark"> <?php echo e(trans('Select Status')); ?> :</label>
                                    <select name="status" id="event-status" class="form-control " required>
                                        <option value="1" <?php if(old('status')=='1' ): ?> selected <?php endif; ?>>
                                            <?php echo e(trans('Active')); ?>

                                        </option>
                                        <option value="0" <?php if(old('status')=='0' ): ?> selected <?php endif; ?>>
                                            <?php echo e(trans('DeActive')); ?>

                                        </option>
                                    </select>
                                    <br>
                                </div>


                                <div class="form-group col-md-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between text-center">
                                            <h5 class="card-title text-center"><?php echo e(trans('Accessibility')); ?></h5>
                                        </div>

                                        <div class="card-body select-all-access">
                                            <div class="form-group">
                                                <label><input type="checkbox" class="selectAll" name="accessAll">
                                                    <?php echo e(trans('Select All')); ?></label>
                                            </div>

                                            <table class=" table table-hover table-striped table-bordered text-center">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th class="text-left"><?php echo app('translator')->get('Permissions'); ?></th>
                                                        <th><?php echo app('translator')->get('View'); ?></th>
                                                        <th><?php echo app('translator')->get('Add'); ?></th>
                                                        <th><?php echo app('translator')->get('Edit'); ?></th>
                                                        <th><?php echo app('translator')->get('Delete'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = config('rolep'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td data-label="Permissions" class="text-left">
                                                            <?php echo e($value['label']); ?></td>
                                                        <td data-label="View">
                                                            <?php if(!empty($value['access']['view'])): ?>
                                                            <input type="checkbox" style="width: 20px; height: 20px;"
                                                                value="<?php echo e(implode(',', array_map('trim', $value['access']['view']))); ?>"
                                                                name="access[]" />
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-label="Add">
                                                            <?php if(!empty($value['access']['add'])): ?>
                                                            
                                                            <input type="checkbox" style="width: 20px; height: 20px;"
                                                                value="<?php echo e(implode(',', array_map('trim', $value['access']['add']))); ?>"
                                                                name="access[]" />
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-label="Edit">
                                                            <?php if(!empty($value['access']['edit'])): ?>
                                                            
                                                            <input type="checkbox" style="width: 20px; height: 20px;"
                                                                value="<?php echo e(implode(',', array_map('trim', $value['access']['edit']))); ?>"
                                                                name="access[]" />
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-label="Delete">
                                                            <?php if(!empty($value['access']['delete'])): ?>
                                                            
                                                            <input type="checkbox" style="width: 20px; height: 20px;"
                                                                value="<?php echo e(implode(',', array_map('trim', $value['access']['delete']))); ?>"
                                                                name="access[]" />
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                        </div>

                    </form>


                </div>
            </div>
        </div>



    </div>



    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script>
        // Handle form submission via AJAX
        $('#partner_staff_table').DataTable({
            processing: false,  // We will manually control the loading spinner
            serverSide: true,
            stateSave: true,
            ajax: {
                url: "<?php echo e(route('partner.staff')); ?>",
                type: 'GET',
                beforeSend: function () {
                    // Show the loader spinner when the DataTable starts loading
                    $('#tableLoader').removeClass('d-none'); // Show the spinner
                    // Disable interactions with the table (edit buttons, etc.)
                    $('#partner_staff_table').css('pointer-events', 'none');
                },
                complete: function () {
                    // Hide the loader spinner once the DataTable has finished loading
                    $('#tableLoader').addClass('d-none'); // Hide the spinner
                    // Re-enable interactions with the table
                    $('#partner_staff_table').css('pointer-events', 'auto');
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
                { data: 'id', name: 'id' },
                { data: 'username', name: 'username' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'status', name: 'status', orderable: false },
                <?php if(adminAccessRoute(config('role.manage_staff.access.edit'))): ?>
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                <?php endif; ?>
            ],
            order: [[0, 'asc']], // Default sorting by SL column
            columnDefs: [
                { targets: '_all', orderable: false }, // Disable sorting for all columns
            ],
            pageLength: 10, // Default page length
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

        $(document).ready(function () {
            "use strict";
            $('.selectAll').on('click', function () {
                if ($(this).is(':checked')) {
                    $(this).parents('.select-all-access').find('input[type="checkbox"]').attr('checked', 'checked')
                } else {
                    $(this).parents('.select-all-access').find('input[type="checkbox"]').removeAttr('checked')
                }
            });

            $('#addModal form').on('submit', function (e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                // Clear existing validation error messages
                form.find('.text-danger').remove();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function (response) {
                        $('#addModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        // If there are validation errors, display them in the modal
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, errorMessage) {
                                var inputField = form.find('[name="' + field + '"]');
                                var errorDiv = $('<div class="text text-danger">' + errorMessage[0] + '</div>');
                                inputField.closest('.form-group').append(errorDiv);
                            });
                        }
                    }
                });
            });

        })

        function submitForm(form) {
            event.preventDefault();

            let $form = $(form);
            let actionUrl = $form.attr('action');
            let formData = new FormData(form);

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    // Optional: disable button / show spinner
                },
                success: function (response) {
                    $('#editModal').modal('hide'); // Hide modal
                    $('#editForm')[0].reset(); // Optional: reset form
                    // Reload DataTable
                    if ($.fn.DataTable.isDataTable('#partner_staff_table')) {
                        $('#partner_staff_table').DataTable().ajax.reload(null, false); // false to stay on the current page
                    }
                    // Optional: Show success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Staff details updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                    });
                }
            });

            return false; // Prevent default form submit
        }
    </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/staff/index.blade.php ENDPATH**/ ?>