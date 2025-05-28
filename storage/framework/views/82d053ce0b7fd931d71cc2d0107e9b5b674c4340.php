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
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/dropzone/dropzone.css')); ?>" />
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
                    <?php if(adminAccessRoute(config('role.gateways.access.add'))): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addGatewayModal" id="newCategoryButton">
                        Add New Gateway
                    </button>
                    <?php endif; ?>
                </div>

                <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                    <thead class="thead-dark bg-primary">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Name'); ?></th>
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



    
    <div class="modal fade" id="addGatewayModal" tabindex="-1" aria-labelledby="addGatewayModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header  bg-primary text-white">
                    <h5 class="modal-title" id="addGatewayModalLabel"><?php echo e(trans('Add New Gateway')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="add_gateway_form" method="post" action="<?php echo e(route('admin.deposit.accounts.store')); ?>"
                    class="dropzone" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-4 mt-3">
                                <label><?php echo e(trans('Name')); ?></label>
                                <input type="text" class="form-control " name="name" value="">
                                <span class="error-text name_error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4 mt-3">
                                <label><?php echo e(trans('Currency')); ?></label>
                                <input type="text" class="form-control " name="currency" value="">
                                <span class="error-text currency_error text-danger"></span>
                            </div>

                            <div class="form-group col-md-4 mt-3">
                                <label>Type</label>
                                <select class="form-select" name="type">
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id ?? ''); ?>"><?php echo e($type->name ?? ''); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="error-text type_error text-danger"></span>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-md-4 mt-3 col-4">
                                <label><?php echo e(trans('Minimum Deposit Amount')); ?></label>
                                <div class="input-group ">
                                    <input type="text" class="form-control " name="minimum_deposit_amount" value="">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <?php echo e($basic->currency ?? trans('USD')); ?>

                                        </div>
                                    </div>
                                    <span class="error-text minimum_deposit_amount_error text-danger"></span>
                                </div>
                            </div>

                            <div class="form-group col-md-4 mt-3 col-5">
                                <label><?php echo e(trans('Minimum WithDrawl Amount')); ?></label>
                                <div class="input-group ">
                                    <input type="text" class="form-control " name="minimum_withdrawal_amount" value="">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <?php echo e($basic->currency ?? trans('USD')); ?>

                                        </div>
                                    </div>
                                    <span class="error-text minimum_withdrawal_amount_error text-danger"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-4 mt-3 col-4">
                                <label><?php echo e(trans('Maximum Deposit Amount')); ?></label>
                                <div class="input-group ">
                                    <input type="text" class="form-control " name="maximum_deposit_amount" value="">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <?php echo e($basic->currency ?? trans('USD')); ?>

                                        </div>
                                    </div>
                                    <span class="error-text maximum_deposit_amount_error text-danger"></span>
                                </div>
                            </div>

                            <div class="form-group col-md-4 mt-3 col-4">
                                <label><?php echo e(trans('Maximum WithDrawl Amount')); ?></label>
                                <div class="input-group ">
                                    <input type="text" class="form-control " name="maximum_withdrawal_amount" value="">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <?php echo e($basic->currency ?? trans('USD')); ?>

                                        </div>
                                    </div>
                                </div>

                                <span class="error-text maximum_withdrawal_amount_error text-danger"></span>
                            </div>
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-sm-6 col-md-4 mt-3 dropzone needsclick">
                                <div class="dz-message needsclick">
                                    Drop files here or click to upload
                                    <span class="note needsclick">(This is just a demo dropzone. Selected files are
                                        <span class="fw-medium">not</span> actually uploaded.)</span>
                                </div>
                                <div class="fallback">
                                    <input name="file" type="file" id="file_input" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 justify-content-between">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('Status'); ?></label>
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                                        <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                                            value="1">
                                        <span id="enableText" class="ms-2 text-secondary"><?php echo app('translator')->get('Yes'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(trans('Close')); ?></button>
                        <button type="submit" id="addGatewayBtn" class="btn btn-primary"><?php echo e(trans('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="editMethodModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel"><?php echo e(__('Edit Deposit Method')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="edit_gateway_form" method="post" class="dropzone" action="" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('post'); ?>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label><?php echo e(__('Name')); ?></label>
                                <input type="text" class="form-control" name="edit_name" value="" required>
                                <span class="error_text text-danger edit_name_erorr"></span>
                            </div>

                            <div class="form-group col-md-4">
                                <label><?php echo e(__('Currency')); ?></label>
                                <input type="text" class="form-control" name="edit_currency" value="">
                                <span class="error_text text-danger edit_currency_error"></span>
                            </div>

                            <div class="form-group col-md-4">
                                <label><?php echo e(__('Conversion Rate')); ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">1
                                            <?php echo e($basic->currency ?? 'USD'); ?> =</span></div>
                                    <input type="text" name="edit_convention_rate" class="form-control" value="">
                                    
                                </div>
                                <span class="error_text text-danger edit_convention_rate_error"></span>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Minimum Deposit Amount')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_minimum_deposit_amount" value="">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?php echo e($basic->currency ?? 'USD'); ?></span>
                                    </div>
                                </div>
                                <span class="error_text text-danger edit_minimum_deposit_amount_error"></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Maximum Deposit Amount')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_maximum_deposit_amount" value="">
                                    <div class="input-group-append"><span class="input-group-text"><?php echo e($basic->currency
                                            ?? 'USD'); ?></span></div>
                                </div>
                                <span class="error_text text-danger edit_maximum_deposit_amount_error"></span>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Minimum Withdrawal Amount')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_minimum_withdrawal_amount">
                                    <div class="input-group-append"><span class="input-group-text"><?php echo e($basic->currency
                                            ?? 'USD'); ?></span></div>
                                </div>
                                <span class="error_text text-danger edit_minimum_withdrawal_amount_error"></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Maximum Withdrawal Amount')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_maximum_withdrawal_amount">
                                    <div class="input-group-append"><span class="input-group-text"><?php echo e($basic->currency
                                            ?? 'USD'); ?></span></div>
                                </div>
                                <span class="error_text text-danger edit_maximum_withdrawal_amount_error"></span>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Percentage Charge')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_percentage_charge">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <span class="error_text text-danger edit_percentage_charge_error"></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label><?php echo e(__('Fixed Charge')); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="edit_fixed_charge">
                                    <div class="input-group-append"><span class="input-group-text"><?php echo e($basic->currency
                                            ?? 'USD'); ?></span></div>
                                </div>
                                <span class="error_text text-danger edit_fixed_charge_error"></span>
                            </div>
                        </div>

                        
                        <div class="row mt-3">
                            <!-- File Upload (col-3) -->
                            <div class="col-md-3 mt-5">
                                <div class="dropzone needsclick">
                                    <div class="dz-message needsclick text-center">
                                        Drop files here or click to upload<br />
                                        <span class="note">(Replace Image)</span>
                                    </div>
                                    <div class="fallback">
                                        <input name="edit_file" type="file" />
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="mt-2">
                                    <label><?php echo e(trans('Current Image')); ?></label><br />
                                    <img id="image_preview_container" src="" style="max-width: 100%; height: auto;"
                                        class="img-thumbnail" />
                                </div>
                            </div>

                            <!-- Note Textarea (col-9) -->
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label><?php echo e(__('Note')); ?></label>
                                    <textarea class="form-control summernote" name="edit_note" rows="10"></textarea>
                                    <span class="error_text text-danger edit_note_error"></span>
                                </div>
                            </div>
                        </div>


                        
                        <div class="form-group">
                            <label><?php echo app('translator')->get('Status'); ?></label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                                <input class="form-check-input" type="checkbox" id="statusSwitch" name="edit_status"
                                    value="1">
                                <span id="enableText" class="ms-2 text-secondary"><?php echo app('translator')->get('Yes'); ?></span>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(trans('Close')); ?></button>
                            <button type="submit" id="editGatewayBtn" class="btn btn-primary"><?php echo e(trans('Save')); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/dropzone/dropzone.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/forms-file-upload.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('.categories-show-table').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    ajax: {
                        url: "<?php echo e(route('admin.account_management.gateway')); ?>", // Make sure this route returns JSON for DataTable
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


            // Handle form submission
            $('#add_gateway_form').on('submit', function (e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                // Append Dropzone files manually to FormData
                if (myDropzone) {
                    if (myDropzone.files.length > 0) {
                    // If multiple files allowed, loop over files
                    // Here maxFiles=1, so just one file expected
                    formData.append('file', myDropzone.files[0]); // 'file' matches your input name in Laravel
                    }
                }

                // Disable submit button and show loading text
                $('#addGatewayBtn').attr('disabled', true).text('Saving...');

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                    // Reset form & dropzone preview
                    $(form)[0].reset();
                    if (myDropzone) myDropzone.removeAllFiles();

                    // Hide modal (assuming Bootstrap modal)
                    $('#addGatewayModal').modal('hide');

                    // Reload datatable
                    $('.categories-show-table').DataTable().ajax.reload(null, false);

                    // Success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    },
                    error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('.error-text').text(''); // clear old errors
                        $.each(errors, function (key, value) {
                        $(`.${key}_error`).text(value[0]);
                        });
                    } else {
                        // Handle other errors
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again.',
                        });
                    }
                    },
                    complete: function () {
                    $('#addGatewayBtn').attr('disabled', false).text('Save');
                    },
                });
            });

            //Handle    Update  gateWAy
            $('#edit_gateway_form').on('submit', function (e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                // Append Dropzone files manually to FormData
                if (myDropzone) {
                    if (myDropzone.files.length > 0) {
                    // If multiple files allowed, loop over files
                    // Here maxFiles=1, so just one file expected
                    formData.append('edit_file', myDropzone.files[0]); // 'file' matches your input name in Laravel
                    }
                }

                // Disable submit button and show loading text
                $('#editGatewayBtn').attr('disabled', true).text('Updating...');

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                    // Reset form & dropzone preview
                    // $(form)[0].reset();
                    if (myDropzone) myDropzone.removeAllFiles();

                    // Hide modal (assuming Bootstrap modal)
                    $('#editMethodModal').modal('hide');

                    // Reload datatable
                    $('.categories-show-table').DataTable().ajax.reload(null, false);

                    // Success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    },
                    error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('.error-text').text(''); // clear old errors
                        $.each(errors, function (key, value) {
                        $(`.${key}_error`).text(value[0]);
                        });
                    } else {
                        // Handle other errors
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again.',
                        });
                    }
                    },
                    complete: function () {
                    $('#editGatewayBtn').attr('disabled', false).text('Save');
                    },
                });
            });


        });

        $(document).on('click', '.toggle-status', function () {
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
            .done(function (res) {
                if (res.success) {
                     $('.categories-show-table').DataTable().ajax.reload(null, false);
                }
            })
            .fail(function () {
                alert('Failed to update status.');
            });
        });

        const previewTemplate = `<div class="dz-preview dz-file-preview">
        <div class="dz-details">
        <div class="dz-thumbnail">
            <img data-dz-thumbnail>
            <span class="dz-nopreview">No preview</span>
            <div class="dz-success-mark"></div>
            <div class="dz-error-mark"></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="progress">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
            </div>
        </div>
        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
        </div>
        </div>`;

        const dropzoneBasic = document.querySelector('#add_gateway_form');
        let myDropzone;

        if (dropzoneBasic) {
            // Initialize Dropzone with autoProcessQueue disabled (we'll handle upload manually)
            myDropzone = new Dropzone(dropzoneBasic, {
                url: '#', // We disable Dropzone's own upload by setting a dummy URL
                autoProcessQueue: false,
                previewTemplate: previewTemplate,  // your existing preview template variable
                parallelUploads: 1,
                maxFilesize: 1, // in MB
                addRemoveLinks: true,
                maxFiles: 1,
                clickable: true,
            });
        }

        const editDropZoneBasic = document.querySelector('#edit_gateway_form');
            let editDropZone;

            if (editDropZoneBasic) {
                Dropzone.autoDiscover = false; // Move this line outside the `if`

                editDropZone = new Dropzone(editDropZoneBasic, {
                    url: "#", // ✅ Prevents error — can be "#" or your actual route
                    autoProcessQueue: false,
                    maxFiles: 1,
                    previewTemplate: previewTemplate,
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                    dictDefaultMessage: "Drop file or click to upload",
                });
            }


    </script>

    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/add_gateway.blade.php ENDPATH**/ ?>