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
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Transactions'); ?>
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
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Deposit Amount'); ?>
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
                        <th scope="col"><?php echo app('translator')->get('Payable'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('E-Wallet No'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Type'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                         <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                         <th scope="col"><?php echo app('translator')->get('Receipt'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $funds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($fund->created_at,'d M,Y H:i')); ?></td>
                            <td data-label="<?php echo app('translator')->get('Trx Number'); ?>"
                                class="font-weight-bold text-uppercase"><?php echo e($fund->txn_id); ?></td>
                            <td data-label="<?php echo app('translator')->get('Username'); ?>">
                                <?php if($fund->user->username != null && optional($fund->user)->username!="dummyuser"): ?>
                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="<?php echo e(getFile(config('location.user.path').optional($fund->user)->image)); ?>"
                                                alt="user"
                                                class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium"><?php echo e(optional($fund->user)->username); ?></h5>
                                            <span class="text-muted font-14"><?php echo e(optional($fund->user)->email); ?></span>
                                        </div>
                                    </div>
                                 <?php else: ?>
                                Partner Transaction
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($fund->sender); ?></td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e(optional($fund->gateway)->name); ?></td>
                            <td data-label="<?php echo app('translator')->get('Amount'); ?>"
                                class="font-weight-bold"><?php echo e(getAmount($fund->amount )); ?> <?php echo e($fund->gateway->currency); ?></td>
                            <td data-label="<?php echo app('translator')->get('Charge'); ?>"
                                class="text-success"><?php echo e(getAmount($fund->charge,2)); ?> <?php echo e($fund->gateway->currency); ?></td>

                            <td data-label="<?php echo app('translator')->get('Payable'); ?>"
                                class="font-weight-bold"><?php echo e(getAmount($fund->amount - $fund->charge)); ?> <?php echo e($fund->gateway->currency); ?></td>

                                <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($fund->e_wallet_phone_number); ?></td>
                                <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($fund->e_wallet_type); ?></td>


                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                <?php if($fund->status == "Pending"): ?>
                                    <span class="badge bg-warning"><i
                                            class="fa fa-circle text-white warning font-12"></i> <?php echo app('translator')->get('Pending'); ?></span>
                                <?php elseif($fund->status == "Complete"): ?>
                                    <span class="badge bg-success"><i
                                            class="fa fa-circle text-white success font-12"></i> <?php echo app('translator')->get('Approved'); ?></span>
                                <?php elseif($fund->status == 'Reject'): ?>
                                    <span class="badge bg-danger"><i
                                            class="fa fa-circle text-white danger font-12"></i> <?php echo app('translator')->get('Rejected'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($fund->request_source); ?></td>
                            <td>
                                <?php if(!empty($fund->receipt_image)): ?>
                                <a data-fancybox="images" href="<?php echo e(getFile(config('location.receipts.path').$fund->receipt_image)); ?>">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                <?php endif; ?>
                            </td>

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
                <?php echo e($funds->appends($_GET)->links('partials.pagination')); ?>

            </div>
        </div>
    </div>



<?php $__env->startPush('js'); ?>
    <script>
        "use strict";
        $(document).ready(function () {
        $('[data-fancybox="images"]').fancybox({
            buttons: ["close"],
            loop: true, // Enables looping through images
        });
    });
    </script>

<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/reportdetail.blade.php ENDPATH**/ ?>