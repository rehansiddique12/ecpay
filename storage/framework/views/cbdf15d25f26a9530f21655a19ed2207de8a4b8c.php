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
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <style>
        tr th {
            color: white !important
        }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <form action="<?php echo e(route('admin.settlements.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Partner</label>
                        <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select a partner">
                            <option></option>
                            <option value="">All</option>
                            <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($partner->id); ?>" <?php if(@request()->partner == $partner->id): ?> selected
                                <?php endif; ?>><?php echo e($partner->website); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>



                <div class="col-md-4  mt-4">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select select2" data-allow-clear="true" data-placeholder="Select a partner">
                            <option></option>
                            <option value="">All</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($gateway->source_name); ?>" <?php if(@request()->gateway ==
                                $gateway->source_name): ?> selected <?php endif; ?>><?php echo e($gateway->source_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4  mt-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value=""><?php echo app('translator')->get('All'); ?></option>
                            <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Approved'); ?></option>
                            <option value="0" <?php if(@request()->status == '0'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending'); ?></option>
                            <option value="2" <?php if(@request()->status == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Rejected'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>

            </div>
        </form>

    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">

                    <button type="button" class="btn btn-primary mb-4 hover:drop-shadow-xl" data-bs-toggle="modal"
                        data-bs-target="#newModal">
                        Add New Settlement
                    </button>

                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered table-sm">
                            <thead class="thead-dark text-warning"
                                style="background: var(--bs-menu-active-bg); color:#ffffff;">
                                <tr>

                                    <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Source Name'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Account No.'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Charges'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Partner'); ?></th>
                                    <th scope="col">Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($item->source); ?></td>
                                    <td><?php echo e($item->source_name); ?></td>
                                    <td><?php echo e($item->account_no); ?></td>
                                    <td><?php echo e($item->amount); ?></td>
                                    <td><?php echo e($item->charges); ?></td>
                                    <td><?php echo e($item->amount + $item->charge); ?></td>
                                    <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                        <?php if($item->status == 2): ?>
                                        <span class="badge  bg-danger">
                                            
                                            <?php echo app('translator')->get('Rejected'); ?>
                                        </span>
                                        <?php elseif($item->status == 1): ?>
                                        <span class="badge bg-success">
                                            
                                            <?php echo app('translator')->get('Approved'); ?></span>
                                        <?php else: ?>
                                        <span class="badge bg-warning">
                                            
                                            <?php echo app('translator')->get('Pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($item->api->website ?? ''); ?></td>
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td data-label="<?php echo app('translator')->get('Action'); ?>">
                                        <div class="dropdown show ">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="icon-base ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <?php if(adminAccessRoute(config('role.settlements.access.edit'))): ?>
                                                <form action="<?php echo e(route('admin.settlements.approve', $item['id'])); ?>"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                            class="fa fa-check"></i> Approve</button>
                                                </form>
                                                <form action="<?php echo e(route('admin.settlements.reject', $item['id'])); ?>"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-sm btn-icon edit_button"><i
                                                            class="fa fa-times"></i> Reject</button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                    </td>
                                </tr>

                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

                    </div>

                </div>
            </div>
        </div>

    </div>


    
    <div class="modal modal-top fade" id="newModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Add New'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="settlementForm" action="<?php echo e(route('admin.settlements.add')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Partner</label>
                                    <select name="partner" class="form-select" required>
                                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($partner->id); ?>" <?php if(@request()->partner == $partner->id): ?>
                                            selected <?php endif; ?>><?php echo e($partner->website); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="text-danger error-partner"></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-select" name="source" required>
                                        <option value="Bank">Bank</option>
                                        <option value="EWallet">EWallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source Name</label>
                                    <input type="text" class="form-control" name="source_name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account No.</label>
                                    <input type="text" class="form-control" name="account_no" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required />
                                    <div class="text-danger error-amount"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>

    <script>
        "use strict";
        $(document).ready(function(e) {
            $('#settlementForm').on('submit', function (e) {
                e.preventDefault();
                let form = $(this);
                let submitBtn = $('#submitBtn');

                // Clear all previous errors
                $('.text-danger').text('');

                // Disable button and show processing text
                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (response) {
                        $('#newModal').modal('hide');
                        location.reload(); // or show a success message
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                $('.error-' + key).text(messages[0]);
                            });
                        } else {
                            alert('Something went wrong.');
                        }
                    },
                    complete: function () {
                        // Re-enable button and reset text to Save
                        submitBtn.prop('disabled', false).text('Save');
                    }
                });
            });

            let $select = $('.select2').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                selectOnClose: true,
            });

            // Prevent dropdown from opening on clear
            $select.on('select2:unselecting', function (e) {
                $(this).data('unselecting', true);
            });

            $select.on('select2:opening', function (e) {
                if ($(this).data('unselecting')) {
                    $(this).removeData('unselecting');
                    e.preventDefault();
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
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/payout/settlement.blade.php ENDPATH**/ ?>