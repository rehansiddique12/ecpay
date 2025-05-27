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
    <form action="<?php echo e(route('admin.reports.master_report')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
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
                            <tr class="text-center">
                            <th rowspan="2">Date</th>
                            <th colspan="5">Deposit</th>
                            <th colspan="5">Withdrawal</th>
                            <th colspan="1">Commission</th>
                            <th colspan="2">Top Up</th>
                            <th colspan="2">Adjustment</th>
                            <th rowspan="2">Transfer Fees (BDT)</th>
                            <th colspan="2">Settlement (BDT)</th>
                            <th rowspan="2">Revenue (BDT)</th>
                            <th rowspan="2">Total Balance (BDT)</th>
                        </tr>
                        <tr >
                            <th>Qty</th>
                            <th>Total (BDT)</th>
                            <th>Merchant Charges (BDT)</th>
                            <th>E-Wallet Fee (BDT)</th>
                            <th>E-Wallet Commission (BDT)</th>
                            <th>Qty</th>
                            <th>Total (BDT)</th>
                            <th>Merchant Charges (BDT)</th>
                            <th>E-Wallet Fee (BDT)</th>
                            <th>E-Wallet Commission (BDT)</th>
                            <th>BDT</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                            <th>Total (BDT)</th>
                            <th>Charges (BDT)</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>

                                <td><?php echo e($item['date']); ?></td>
                                <td><?php echo e($item['deposit_record_count']); ?></td>
                                <td><?php echo e(number_format($item['deposit_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_e_wallet_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_commission'], 2)); ?></td>
                                <td><?php echo e($item['withdrawal_record_count']); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_e_wallet_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_commission'], 2)); ?></td>
                                <td><?php echo e(number_format($item['commission_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['top_up_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['top_up_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['adjustment_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['adjustment_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['transfer_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['revenue'], 2)); ?></td>
                                <td><?php echo e(number_format($item['total'], 2)); ?></td>
                                

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
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/master_report.blade.php ENDPATH**/ ?>