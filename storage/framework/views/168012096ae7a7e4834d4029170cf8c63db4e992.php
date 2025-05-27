<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="<?php echo e(route('partner.reports.partner_account_balance_summary')); ?>" method="get">
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


                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>


                                <th scope="col">Date</th>
                                <th scope="col">Opening Balance</th>
                                <th scope="col">Total Deposit</th>
                                <th scope="col">Total Deposit Charges</th>
                                <th scope="col">Total Withdrawal</th>
                                <th scope="col">Total Withdrawal Charges</th>
                                <th scope="col">Total Settlement</th>
                                <th scope="col">Total Settlement Charges</th>
                                <th scope="col">Total Adjustment</th>
                                <th scope="col">Commission Eearned</th>
                                <th scope="col">Closing Balance</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>

                                <td><?php echo e($item['date']); ?></td>
                                <td><?php echo e(number_format($item['opening_balance'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['adjustment'], 2)); ?></td>
                                <td><?php echo e(number_format($item['commission'], 2)); ?></td>
                                <td><?php echo e(number_format($item['closing_balance'], 2)); ?></td>

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
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/reports/partner_account_balance_summary.blade.php ENDPATH**/ ?>