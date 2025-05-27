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
    <form action="<?php echo e(route('admin.reports.daily_transection_summary')); ?>" method="get">
        <div class="row align-items-center">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Date</label>
                    <input type="date" class="form-control" value="<?php echo e($date); ?>" name="date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
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
                <h4>Transection Report</h4>
                <!-- <h3>Deposit Report</h3> -->
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">DEPOSIT</th>
                                <th scope="col">NAGAD</th>
                                <th scope="col">BKASH</th>
                                <th scope="col">ROCKET</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <tr>
                                <td>Deposit Transactions</td>
                                <td><?php echo e($data['nagad_d']->record_count); ?></td>
                                <td><?php echo e($data['bkash_d']->record_count); ?></td>
                                <td><?php echo e($data['rocket_d']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Deposit Amount</td>
                                <td><?php echo e(getAmount($data['nagad_d']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_d']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_d']->total_amount,2)); ?>TK</td>
                            </tr>
                            <tr>
                                <td>Transfer In Transactions</td>
                                <td><?php echo e($data['nagad_in']->record_count); ?></td>
                                <td><?php echo e($data['bkash_in']->record_count); ?></td>
                                <td><?php echo e($data['rocket_in']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Transfer In Amount</td>
                                <td><?php echo e(getAmount($data['nagad_in']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_in']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_in']->total_amount,2)); ?>TK</td>
                            </tr>
                            <tr>
                                <td>Total Transactions</td>
                                <td><?php echo e($data['nagad_d']->record_count + $data['nagad_in']->record_count); ?></td>
                                <td><?php echo e($data['bkash_d']->record_count + $data['bkash_in']->record_count); ?></td>
                                <td><?php echo e($data['rocket_d']->record_count + $data['rocket_in']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Total Amount</td>
                                <td><?php echo e(getAmount($data['nagad_d']->total_amount + $data['nagad_in']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_d']->total_amount + $data['bkash_in']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_d']->total_amount + $data['rocket_in']->total_amount,2)); ?>TK</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">WITHDRAWAL</th>
                                <th scope="col">NAGAD</th>
                                <th scope="col">BKASH</th>
                                <th scope="col">ROCKET</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <tr>
                                <td>Withdrawal Transactions</td>
                                <td><?php echo e($data['nagad_w']->record_count); ?></td>
                                <td><?php echo e($data['bkash_w']->record_count); ?></td>
                                <td><?php echo e($data['rocket_w']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Withdrawal Amount</td>
                                <td><?php echo e(getAmount($data['nagad_w']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_w']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_w']->total_amount,2)); ?>TK</td>
                            </tr>
                            <tr>
                                <td>Transfer Out Transactions</td>
                                <td><?php echo e($data['nagad_out']->record_count); ?></td>
                                <td><?php echo e($data['bkash_out']->record_count); ?></td>
                                <td><?php echo e($data['rocket_out']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Transfer Out Amount</td>
                                <td><?php echo e(getAmount($data['nagad_out']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_out']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_out']->total_amount,2)); ?>TK</td>
                            </tr>
                            <tr>
                                <td>Total Transactions</td>
                                <td><?php echo e($data['nagad_w']->record_count + $data['nagad_out']->record_count); ?></td>
                                <td><?php echo e($data['bkash_w']->record_count + $data['bkash_out']->record_count); ?></td>
                                <td><?php echo e($data['rocket_w']->record_count + $data['rocket_out']->record_count); ?></td>
                            </tr>
                            <tr>
                                <td>Total Amount</td>
                                <td><?php echo e(getAmount($data['nagad_w']->total_amount + $data['nagad_out']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['bkash_w']->total_amount + $data['bkash_out']->total_amount,2)); ?>TK</td>
                                <td><?php echo e(getAmount($data['rocket_w']->total_amount + $data['rocket_out']->total_amount,2)); ?>TK</td>
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
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/daily_transection_summary.blade.php ENDPATH**/ ?>