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
        tr th{
          color: white !important
        }
    </style>

<div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
    <form action="<?php echo e(route('admin.balance.logs.search')); ?>" method="get">
        <div class="row justify-content-between align-items-center">

            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>E-Wallet</label>
                    <input type="text" class="form-control" value="<?php echo e(@request()->ewallet); ?>" name="ewallet" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Account No</label>
                    <input type="text" class="form-control" value="<?php echo e(@request()->account_no); ?>" name="account_no" />
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    <label>Transection Type</label>
                    <select name="type" class="form-select">
                        <option value=""><?php echo app('translator')->get('All'); ?></option>
                        <option value="plus" <?php if(@request()->type == 'plus'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Add Credit'); ?></option>
                        <option value="minus" <?php if(@request()->type == 'minus'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Subtract Credit'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Account Type</label>
                    <select name="a_type" class="form-select">
                        <option value=""><?php echo app('translator')->get('All'); ?></option>
                        <option value="Merchant" <?php if(@request()->a_type == 'Merchant'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Merchant'); ?></option>
                        <option value="Personal" <?php if(@request()->a_type == 'Personal'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Personal'); ?></option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
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
                    <table class="categories-show-table table table-hover table-striped table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col"><?php echo app('translator')->get('E-Wallet Name'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Account No.'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Type'); ?></th>
                                <th scope="col">Amount</th>
                                <th scope="col">Transection Type</th>
                                <th scope="col">Date-Time</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $accountlog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e(optional($item->e_wallet_account)->e_wallet_name); ?></td>
                                <td><?php echo e(optional($item->e_wallet_account)->account_no); ?></td>
                                <td><?php echo e(optional($item->e_wallet_account)->type); ?></td>

                                <td><?php echo e($item->amount); ?></td>
                                <?php if($item->type=="plus"): ?>
                                <td><span class="badge bg-success text-white"><b>+ Add Credit</b></span></td>
                                <?php else: ?>
                                <td><span class="badge bg-danger text-white"><b>- Subtract Credit</b></span></td>
                                <?php endif; ?>
                                <td><?php echo e($item->created_at); ?></td>

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
                <div class="card-footer mt-2">
                    <?php echo e($accountlog->appends($_GET)->links('partials.pagination')); ?>

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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/balance_logs.blade.php ENDPATH**/ ?>