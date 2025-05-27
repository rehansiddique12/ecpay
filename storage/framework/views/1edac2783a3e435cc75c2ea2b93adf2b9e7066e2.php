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
    <form action="<?php echo e(route('admin.reports.partner_account_balance_summary_completions')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row justify-content-between align-items-center">
            <input type="hidden" name="search" value="search">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label>Source</label>
                    <select name="website" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
                        <option value="">All Source</option>
                        <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($partner->id); ?>" <?php if(@request()->website == $partner->id): ?> selected <?php endif; ?>><?php echo e($partner->name); ?></option>
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
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">


                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">Partner</th>
                                <th scope="col">Date</th>
                                <th scope="col">Opening Balance</th>
                                <th scope="col">Total Deposit</th>
                                <th scope="col">Total Deposit Charges</th>
                                <th scope="col">Total Withdrawal</th>
                                <th scope="col">Total Withdrawal Charges</th>
                                <th scope="col">Total Settlement</th>
                                <th scope="col">Total Settlement Charges</th>
                                <th scope="col">Total Adjustment</th>
                                <th scope="col">Adjustment Charges</th>
                                <th scope="col">Commission Eearned</th>
                                <th scope="col">Closing Balance</th>
                                <th scope="col">Differance</th>
                                <th scope="col">Current Balance</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $deposit_amount = 0;
                            $deposit_charges = 0;
                            $withdrawal_amount = 0;
                            $withdrawal_charges = 0;
                            $settlement_amount = 0;
                            $settlement_charges = 0;
                            $adjustment = 0;
                            $adjustment_charges = 0;
                            $commission = 0;
                            ?>
                            <?php if(isset($data)): ?>
                            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php

                            $deposit_amount += $item['deposit_amount'];
                            $deposit_charges += $item['deposit_charges'];
                            $withdrawal_amount += $item['withdrawal_amount'];
                            $withdrawal_charges += $item['withdrawal_charges'];
                            $settlement_amount += $item['settlement_amount'];
                            $settlement_charges += $item['settlement_charges'];
                            $adjustment += $item['adjustment'];
                            $adjustment_charges += $item['adjustment_charges'];
                            $commission += $item['commission'];

                            ?>
                            <tr>
                                <td><?php echo e($item['partner']); ?></td>
                                <td><?php echo e($item['date']); ?></td>
                                <td><?php echo e(number_format($item['opening_balance'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['deposit_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['withdrawal_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($item['settlement_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['adjustment'], 2)); ?></td>
                                <td><?php echo e(number_format($item['adjustment_charges'], 2)); ?></td>
                                <td><?php echo e(number_format($item['commission'], 2)); ?></td>
                                <td><?php echo e(number_format($item['closing_balance'], 2)); ?></td>
                                <?php
                                if (@request()->website && !empty(@request()->website)) {
                                    if($item['differance']==0){
                                        echo '<td>'.$item['differance'].'</td>';
                                    }else{
                                        echo '<td style="background-color: red;color:white">'.$item['differance'].'</td>';
                                    }
                                }else{
                                    echo '<td></td>';
                                }
                                ?>

                                    <?php if($item['date']==date('Y-m-d')): ?>
                                    <?php if($item['current_balance']-$item['closing_balance']<1 && $item['current_balance']-$item['closing_balance']>-1): ?>
                                        <td style="background-color: green;color:white"><?php echo e(number_format($item['current_balance'], 2)); ?></td>

                                    <?php else: ?>
                                        <td style="background-color: red;color:white"><?php echo e(number_format($item['current_balance'], 2)); ?></td>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <td></td>
                                    <?php endif; ?>


                            </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <thead class="thead-dark">
                            <tr>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th><?php echo e(number_format($deposit_amount,2)); ?></th>
                                <th><?php echo e(number_format($deposit_charges,2)); ?></th>
                                <th><?php echo e(number_format($withdrawal_amount,2)); ?></th>
                                <th><?php echo e(number_format($withdrawal_charges,2)); ?></th>
                                <th><?php echo e(number_format($settlement_amount,2)); ?></th>
                                <th><?php echo e(number_format($settlement_charges,2)); ?></th>
                                <th><?php echo e(number_format($adjustment,2)); ?></th>
                                <th><?php echo e(number_format($adjustment_charges,2)); ?></th>
                                <th><?php echo e(number_format($commission,2)); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>

                            </tr>

                            </thead>
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/partner_account_balance_summary_completions.blade.php ENDPATH**/ ?>