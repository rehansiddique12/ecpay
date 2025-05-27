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
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>

                                <th scope="col">E-Wallet</th>
                                <th scope="col">Live Balance</th>
                                <th scope="col">Deposit</th>
                                <th scope="col">Withdrawal</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($data)): ?>
                            <tr>
                                <td>Daily Total</td>
                                <td><?php echo e(number_format($sumBalance, 2)); ?></td>
                                <td><?php echo e(number_format($sumDailySent, 2)); ?></td>
                                <td><?php echo e(number_format($sumDailyReceived, 2)); ?></td>

                            </tr>
                            <tr>
                                <td colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <h3><b>Nagad</b></h3>
                                </td>
                            </tr>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->e_wallet_name=="Nagad"): ?>
                            <tr>
                                <td><?php echo e($item->account_no); ?></td>
                                <td><?php echo e(number_format($item->balance, 2)); ?></td>
                                <td><?php echo e(number_format($item->received, 2)); ?></td>
                                <td><?php echo e(number_format($item->send, 2)); ?></td>

                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr>
                                <td colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <h3><b>bKash</b></h3>
                                </td>
                            </tr>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->e_wallet_name=="bKash"): ?>
                            <tr>
                                <td><?php echo e($item->account_no); ?></td>
                                <td><?php echo e(number_format($item->balance, 2)); ?></td>
                                <td><?php echo e(number_format($item->received, 2)); ?></td>
                                <td><?php echo e(number_format($item->send, 2)); ?></td>

                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr>
                                <td colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <h3><b>Rocket</b></h3>
                                </td>
                            </tr>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->e_wallet_name=="Rocket"): ?>
                            <tr>
                                <td><?php echo e($item->account_no); ?></td>
                                <td><?php echo e(number_format($item->balance, 2)); ?></td>
                                <td><?php echo e(number_format($item->received, 2)); ?></td>
                                <td><?php echo e(number_format($item->send, 2)); ?></td>

                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/live_ewallet_balance.blade.php ENDPATH**/ ?>