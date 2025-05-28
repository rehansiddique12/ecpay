<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="<?php echo e(route('admin.adjustments.search')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row justify-content-between align-items-center">

            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                        <option value="">All</option>
                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($partner->id); ?>" <?php if(@request()->partner == $partner->id): ?> selected <?php endif; ?>><?php echo e($partner->website); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>


           <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value=""><?php echo app('translator')->get('All'); ?></option>
                        <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Completed'); ?></option>
                        <option value="0" <?php if(@request()->status == '0'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending'); ?></option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                </div>
            </div>
           </div>

        </div>
    </form>

</div>



<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">



                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col"><?php echo app('translator')->get('Name'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('User-Name'); ?></th>
                                <th scope="col">Website</th>
                                <th scope="col">Payment Amount</th>
                                <th scope="col">Withdrawal Amount</th>
                                <th scope="col">Amount Adjusted</th>
                                <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                <th scope="col">Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item->api->name); ?></td>
                                <td><?php echo e($item->api->username); ?></td>
                                <td><?php echo e($item->api->website); ?></td>
                                <td><?php echo e($item->payment); ?></td>
                                <td><?php echo e($item->payout); ?></td>
                                <td><?php echo e($item->adjustment); ?></td>
                                <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                    <?php if($item->status == 1): ?>
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-success success font-12"></i> <?php echo app('translator')->get('Completed'); ?></span>
                                    <?php else: ?>
                                    <span class="badge badge-light">
                                        <i class="fa fa-circle text-warning success font-12"></i> <?php echo app('translator')->get('Pending'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->created_at); ?></td>
                               


                                <td data-label="<?php echo app('translator')->get('Action'); ?>">
                                    
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
            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('js'); ?>
<script>
    "use strict";
    $(document).ready(function(e) {


        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


    });

</script>
<script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
<script>
    $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> <?php echo app('translator')->get("Processing..."); ?>');

            // Allow form to proceed
            return true;
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
<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/adjustments.blade.php ENDPATH**/ ?>