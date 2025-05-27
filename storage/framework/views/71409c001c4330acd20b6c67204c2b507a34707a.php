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
        td:hover {
            background-color: lightgray;
            cursor: pointer;
        }
    </style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('admin.payment.payment_gateway_report')); ?>" method="get">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Partners</label>
                        <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
                            <option value="">All</option>
                            <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(@request()->partner == $key): ?> selected <?php endif; ?>>
                                    <?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="fas fa-search"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Partner'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Total Deposit Request'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Total Auto Process'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Total Manual Process'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Total Abandoned'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Success Rate'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Within 10s'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>10 seconds'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>20 seconds'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>30 seconds'); ?></th>
                            <th scope="col"> <?php echo app('translator')->get('>40 seconds'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>50 seconds'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>1 min'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>5 min'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('>10 min'); ?></th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $combined; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $apis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td rowspan="<?php echo e(count($apis) + 1); ?>"><?php echo e($date); ?></td> <!-- Group by Date -->
                                <?php $__currentLoopData = $apis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $api_id => $counts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $partnerName = $partners[$api_id] ?? $api_id; // Fetch partner name from $partners array
                                        $fundCount = $counts['fund_count'] ?? 0;
                                        $autoProcessCount = $counts['auto_process_count'] ?? 0;
                                        $manualProcessCount = $counts['manual_process_count'] ?? 0;
                                        $abandoned = $fundCount - ($autoProcessCount + $manualProcessCount);

                                        $timeLessThan10 = $counts['time_less_than_10'] ?? 0;
                                        $timeBetween10And20 = $counts['time_between_10_and_20'] ?? 0;
                                        $timeBetween20And30 = $counts['time_between_20_and_30'] ?? 0;
                                        $timeBetween30And40 = $counts['time_between_30_and_40'] ?? 0;
                                        $timeBetween40And50 = $counts['time_between_40_and_50'] ?? 0;
                                        $timeBetween50And60 = $counts['time_between_50_and_60'] ?? 0;
                                        $timeBetween60And5Minutes = $counts['time_between_60_and_5_minutes'] ?? 0;
                                        $timeBetween5And10Minutes = $counts['time_between_5_and_10_minutes'] ?? 0;
                                        $time_greater_than_10_minutes = $counts['time_greater_than_10_minutes'] ?? 0;
                                        $successRate =
                                            $fundCount > 0 && $fundCount - $abandoned > 0
                                                ? ($autoProcessCount / ($fundCount - $abandoned)) * 100
                                                : 0;
                                    ?>
                            <tr>
                                <td><?php echo e($partnerName); ?></td>
                                <td><?php echo e($fundCount); ?></td>
                                <td><?php echo e($autoProcessCount); ?></td>
                                <td><?php echo e($manualProcessCount); ?></td>
                                <td><?php echo e(max(0, $abandoned)); ?></td> <!-- Ensure no negative values -->
                                <td><?php echo e(number_format($successRate, 2)); ?>%</td> <!-- Format success rate -->
                                <td><?php echo e($timeLessThan10); ?></td> <!-- Add time-based count -->
                                <td><?php echo e($timeBetween10And20); ?></td> <!-- Add time-based count -->
                                <td><?php echo e($timeBetween20And30); ?></td> <!-- Add time-based count -->
                                <td><?php echo e($timeBetween30And40); ?></td>
                                <td><?php echo e($timeBetween40And50); ?></td>
                                <td><?php echo e($timeBetween50And60); ?></td>
                                <td><?php echo e($timeBetween60And5Minutes); ?></td>
                                <td><?php echo e($timeBetween5And10Minutes); ?></td>
                                <td><?php echo e($time_greater_than_10_minutes); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.payment.payment_gateway_report_detail', ['id' => $api_id, 'from_date' => $date, 'to_date' => $date])); ?>" class="btn btn-success">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="16" class="text-center"><?php echo app('translator')->get('No data available'); ?></td>
                        </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->startPush('js'); ?>
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/payment_gateway_report.blade.php ENDPATH**/ ?>