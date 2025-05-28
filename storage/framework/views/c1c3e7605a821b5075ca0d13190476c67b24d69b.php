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
    <form action="<?php echo e(route('admin.reports.daily_ewallet_summary')); ?>" method="get">
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


                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">E-Wallet Name</th>
                                <th scope="col">Account No.</th>
                                <th scope="col">Opening Balance</th>
                                <th scope="col">Total Deposit</th>
                                <th scope="col">Total Withdrawal</th>
                                <th scope="col">Transfer In</th>
                                <th scope="col">Transfer Out</th>
                                <th scope="col">Closing Balance</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item['e_wallet_name']); ?></td>
                                <td><?php echo e($item['account_no']); ?></td>
                                <td><?php echo e($item['opening_balance']); ?></td>
                                <td><?php echo e(getAmount($item['total_deposit'],2)); ?></td>
                                <td><?php echo e(getAmount($item['total_withdrawal'],2)); ?></td>
                                <td><?php echo e($item['transfer_in']); ?></td>
                                <td><?php echo e($item['transfer_out']); ?></td>
                                <td><?php echo e($item['closing_balance']); ?></td>


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
                <div class="card-footer">
                    <?php echo e($EWalletAccounts->appends($_GET)->links('partials.pagination')); ?>

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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/daily_ewallet_summary.blade.php ENDPATH**/ ?>