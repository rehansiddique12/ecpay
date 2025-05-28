<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="row">
        <div class="col-2"></div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium"><?php echo e($fund_count); ?></h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total
                                Transactions'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium"><?php echo e($fund_sum); ?></h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Withdrawal
                                Amount'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <b>Date:</b><?php echo e($heading['date']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Status:</b><?php echo e($heading['status']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>E-Wallet Name:</b><?php echo e($heading['gateway']); ?>

            <br><br>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Username'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('User Account'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                            <!--<th scope="col"><?php echo app('translator')->get('Transfer Status'); ?></th>-->
                            <th scope="col"><?php echo app('translator')->get('Sent From'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Account Type'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($item->created_at,'d M,Y H:i')); ?></td>
                            <td><?php echo e($item->txn_id); ?></td>
                            <td data-label="<?php echo app('translator')->get('Username'); ?>">
                                <?php if(optional($item->user)->username != null &&
                                optional($item->user)->username!="dummyuser"): ?>

                                <div class="d-lg-flex d-block align-items-center ">
                                    <div class="mr-3"><img
                                            src="<?php echo e(getFile(config('location.user.path').optional($item->user)->image)); ?>"
                                            alt="user" class="rounded-circle" width="45" height="45"></div>


                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium"><?php echo e(optional($item->user)->username); ?></h5>
                                        <span class="text-muted font-14"><?php echo e(optional($item->user)->email); ?></span>
                                    </div>
                                </div>

                                <?php else: ?>
                                Partner Transaction
                                <?php endif; ?>

                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->user_account_no); ?></td>
                            <td><?php echo e(optional($item->gateway)->name); ?></td>
                            <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold"><?php echo e(getAmount($item->amount )); ?> <?php echo e($basic->currency_symbol); ?></td>
                            <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success"><?php echo e(getAmount($item->charge,2)); ?>

                                <?php echo e($basic->currency_symbol); ?></td>

                            <td data-label="<?php echo app('translator')->get('Net Amount'); ?>" class="font-weight-bold"><?php echo e(getAmount($item->amount +
                                $item->charge)); ?> <?php echo e($basic->currency_symbol); ?></td>

                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                <?php if($item->status == "Complete"): ?>
                                <span class="badge bg-success"><i class="fa fa-circle text-white font-12"></i>
                                    <?php echo app('translator')->get('Completed'); ?></span>
                                <?php elseif($item->status == "Pending"): ?>
                                <span class="badge bg-warning"><i class="fa fa-circle text-white font-12"></i>
                                    <?php echo app('translator')->get('Pending'); ?></span>
                                <?php elseif($item->status == "Reject"): ?>
                                <span class="badge bg-danger"><i class="fa fa-circle text-white font-12"></i>
                                    <?php echo app('translator')->get('Rejected'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->e_wallet_phone_number); ?></td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->e_wallet_type); ?></td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->request_source); ?></td>



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
                <div class="mt-5">
                    <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/report_detail.blade.php ENDPATH**/ ?>