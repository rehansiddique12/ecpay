<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
    th a {
        color:white !important;
        background: none !important;
    }
</style>
<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <form action="<?php echo e(route('partner.reports.log_completions')); ?>" method="get">
        <div class="row justify-content-between align-items-center">

             <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="text" class="form-control datetimepicker" value="<?php echo e($from_date); ?>" name="from_date"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="text" class="form-control datetimepicker" value="<?php echo e($to_date); ?>" name="to_date" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    <a href="<?php echo e(route('partner.report.export_excel_record_completions', ['from_date' => $from_date, 'to_date' => $to_date , 'order' => request('order') === 'asc' ? 'asc' : 'desc'])); ?>" class="btn waves-effect waves-light btn-success">
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
                <div class="table-responsive">
                            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">
                                    Transaction Date
                                </th>
                                <th scope="col">
                                    <a href="<?php echo e(route('partner.reports.log_completions', array_merge(request()->all(), ['sort_by' => 'updated_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc']))); ?>">
                                        Completed Date
                                        <?php if(request('sort_by') === 'updated_at'): ?>
                                            <?php if(request('order') === 'asc'): ?>
                                                <i class="bi bi-caret-up-fill"></i>
                                            <?php else: ?>
                                                <i class="bi bi-caret-down-fill"></i>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <i class="bi bi-caret-down-fill text-muted"></i> <!-- Default unsorted icon (optional) -->
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th scope="col">Txn No.</th>
                                <th scope="col">Partner Txn No.</th>
                                <th scope="col">Account No.</th>
                                <th scope="col">Source</th>
                                <th scope="col">Type</th>
                                <th scope="col">E-Wallet Acc. No.</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Charges</th>
                                <th scope="col">Final Amount</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Transaction Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($final_data)): ?>
                            <?php if(request('order')=="asc"): ?>
                            <?php $balance = $closing_balance + 0; ?>
                            <?php $__empty_1 = true; $__currentLoopData = $final_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                            $balance += $item['final_amount'];
                            ?>
                            <tr>
                                <td><?php echo e(convertToUserTimezone($item['txn_created_at'])); ?></td>
                                <td><?php echo e(convertToUserTimezone($item['updated_at'])); ?></td>
                                <td><?php echo e($item['transection_id']); ?></td>
                                <td><?php echo e($item['partner_transection_id']); ?></td>
                                <td><?php echo e($item['sender']); ?></td>
                                <td><?php echo e($item['e_wallet_name']); ?></td>
                                <td><?php echo e($item['e_wallet_type']); ?></td>
                                <td><?php echo e($item['e_wallet_phone_number']); ?></td>
                                <td><?php echo e($item['amount']); ?></td>
                                <td><?php echo e($item['charge']); ?></td>
                                <td><?php echo e(number_format($item['final_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($balance, 2)); ?> </td>


                                <td>
                                    <?php
                                    if($item['transection_type']==1){
                                        echo "Deposit";
                                    }elseif($item['transection_type']==2){
                                        echo "Withdrawal";
                                    }elseif($item['transection_type']==3){
                                        echo "Adjustment";
                                    }elseif($item['transection_type']==4){
                                        echo "Settlement";
                                    }elseif($item['transection_type']==5){
                                        echo "Commission";
                                    }elseif($item['transection_type']==7){
                                        echo "Withdrawal Refunded";
                                    }else{
                                        echo $item['transection_type'];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php else: ?>
                            <?php $balance = $closing_balance + $total_amount;  ?>
                            <?php $__empty_1 = true; $__currentLoopData = $final_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                            <tr>
                                <td><?php echo e(convertToUserTimezone($item['txn_created_at'])); ?></td>
                                <td><?php echo e(convertToUserTimezone($item['updated_at'])); ?></td>
                                <td><?php echo e($item['transection_id']); ?></td>
                                <td><?php echo e($item['partner_transection_id']); ?></td>
                                <td><?php echo e($item['sender']); ?></td>
                                <td><?php echo e($item['e_wallet_name']); ?></td>
                                <td><?php echo e($item['e_wallet_type']); ?></td>
                                <td><?php echo e($item['e_wallet_phone_number']); ?></td>
                                <td><?php echo e($item['amount']); ?></td>
                                <td><?php echo e($item['charge']); ?></td>
                                <td><?php echo e(number_format($item['final_amount'], 2)); ?></td>
                                <td><?php echo e(number_format($balance ?? 0, 2)); ?> </td>

                                <td>
                                    <?php
                                    if($item['transection_type']==1){
                                        echo "Deposit";
                                    }elseif($item['transection_type']==2){
                                        echo "Withdrawal";
                                    }elseif($item['transection_type']==3){
                                        echo "Adjustment";
                                    }elseif($item['transection_type']==4){
                                        echo "Settlement";
                                    }elseif($item['transection_type']==5){
                                        echo "Commission";
                                    }elseif($item['transection_type']==7){
                                        echo "Withdrawal Refunded";
                                    }else{
                                        echo $item['transection_type'];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php

                                $balance -= $item['final_amount'];

                            ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
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
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- DateTimePicker Add-on -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>
    $(document).ready(function() {
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i',
            step: 1,
            datepicker: true,
            timepicker: true
        });
    });
</script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/reports/log_completions.blade.php ENDPATH**/ ?>