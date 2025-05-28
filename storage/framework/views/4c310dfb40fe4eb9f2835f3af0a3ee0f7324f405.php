<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
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

                                <th scope="col"><?php echo app('translator')->get('Partner/Agent'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Deposit Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Deposit Charges'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Deposit Net Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Deposit Profit'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Withdrawal Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Withdrawal Charges'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Withdrawal Net Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Withdrawal Profit'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item->api->name); ?></td>
                                <td><?php echo e(number_format($item->sum_amount_type_1, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_charges_type_1, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_total_amount_type_1, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_profit_type_1, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_amount_type_2, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_charges_type_2, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_total_amount_type_2, 2)); ?></td>
                                <td><?php echo e(number_format($item->sum_profit_type_2, 2)); ?></td>


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
            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('js'); ?>
<script>
    "use strict";
    $(document).ready(function(e) {


        $('#image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


    });

    $(document).ready(function() {
        $('select').select2({
            selectOnClose: true
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/api.blade.php ENDPATH**/ ?>