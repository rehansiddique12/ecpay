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
    <form action="<?php echo e(route('admin.merchant_reports.by_date')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row align-items-left">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Date</label>
                    <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <input type="hidden" value="search" name="search_post">
            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    
                    <a href="<?php echo e(route('admin.merchant_reports.export_by_date', ['from_date' => $from_date])); ?>"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                        <i class="icon-base ti tabler-download me-1"></i> <?php echo app('translator')->get('Export'); ?>
                     </a>
                </div>
            </div>

        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3><b>Total Commission</b> <?php echo e(number_format($totalCommissionAll , 2)); ?></h3>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr class="text-center">
                            <th rowspan="2">Merchant Name</th>
                            <th colspan="3">Deposit</th>
                            <th colspan="3">Withdrawal</th>
                           <th></th>
                        </tr>
                        <tr>
                            <th>No. Transaction</th>
                            <th>Total Amount</th>
                            <th>Commission</th>
                            <th>No. Transaction</th>
                            <th>Total Withdrawal</th>
                            <th>Commission</th>
                            <th>Total Commission</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($results)): ?>
                                <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($apis[$result->api_id]); ?></td>
                                    <td><?php echo e(number_format($result->total_deposit_transactions , 2)); ?></td>
                                    <td><?php echo e(number_format($result->total_deposit  ,2)); ?></td>
                                    <td><?php echo e(number_format($result->total_charges_deposit ,2)); ?></td>
                                    <td><?php echo e($result->total_withdrawal_transactions); ?></td>
                                    <td><?php echo e(number_format($result->total_withdrawal ,2)); ?></td>
                                    <td><?php echo e(number_format($result->total_charges_withdrawal ,2)); ?></td>
                                    <td><?php echo e(number_format($result->total_commission , 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                    </td>
                                </tr>
                                <?php endif; ?>
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
    // JavaScript/jQuery to dynamically update the export button href when the date is changed
    document.getElementById('datepicker').addEventListener('change', function() {
        var selectedDate = this.value;  // Get the selected date
        var exportButton = document.getElementById('exportButton');

        // Update the href of the export button with the selected date
        exportButton.href = "<?php echo e(route('admin.merchant_reports.export_by_date', ['from_date' => ''])); ?>/" + selectedDate;
    });
</script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/merchant/report_by_date.blade.php ENDPATH**/ ?>