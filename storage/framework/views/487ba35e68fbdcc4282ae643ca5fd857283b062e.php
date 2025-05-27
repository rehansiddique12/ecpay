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
        <form action="<?php echo e(route('admin.payout.report.daily.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker"/>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">All</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->name); ?>"
                                <?php if(@request()->gateway == $gateway->name): ?> selected <?php endif; ?>><?php echo e($gateway->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Partner</label>
                        <select name="website" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                            <option></option>
                            <option value="">All</option>
                            <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partner->id); ?>"
                                <?php if(@request()->website == $partner->id): ?> selected <?php endif; ?>><?php echo e($partner->name); ?> ===> ( <?php echo e($partner->website); ?> )</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>



                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
$gateway = "All";
if(!empty(@request()->gateway)){
$gateway = @request()->gateway;
}
?>

<!-- Add these lines to your HTML header section -->
<link rel="stylesheet" href="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css')); ?>">
<script src="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js')); ?>"></script>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Deposit (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Pending (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Pending Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Approved (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Approved Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Total Amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payoutsByDate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'All'])); ?>';"> <?php echo e($payout->payout_date); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'All'])); ?>';"> <?php echo e($payout->payout_count); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'Pending'])); ?>';"> <?php echo e($payout->pending_count); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'Pending'])); ?>';"> <?php echo e(getAmount($payout->pending_amount,2)); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'Approved'])); ?>';"> <?php echo e($payout->complete_count); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'Approved'])); ?>';"> <?php echo e(getAmount($payout->complete_amount,2)); ?></td>
                            <td onclick="window.location='<?php echo e(route('admin.payout.report.detail', ['date' => $payout->payout_date,'gateway' => $gateway,'status' => 'All'])); ?>';"> <?php echo e(getAmount($payout->total_amount,2)); ?></td>
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('js'); ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/daily_report.blade.php ENDPATH**/ ?>