<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <style>
        tr th {
            color: white !important;
        }
    </style>
    <?php $__env->stopPush(); ?>


    <?php
    $key = 0;
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>

                <form action="<?php echo e(route('admin.add.partner.commission')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Select Parent</label>
                        <select name="partner_id" style="border:2px solid green;" id="partner_id" class="form-select" required>
                            <option value="">-- Choose Parent --</option>
                            <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($partner->id); ?>"><?php echo e($partner->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <?php if(count($commissions) > 0): ?>
                    <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div id="row-p<?php echo e($key); ?>">
                        <br>
                        <div style="border:1px solid;padding:20px">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="row">
                                    <div class="col-md-3">
                                        <label>From Amount</label>
                                        <input type="hidden" name="id[]" value="<?php echo e($commission->id); ?>">
                                        <input type="number" class="form-control"
                                            value="<?php echo e($commission->from_amount); ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>To Amount</label>
                                        <input type="number" class="form-control"
                                            value="<?php echo e($commission->to_amount); ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Deposit %</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                 value="<?php echo e($commission->deposit_percentage); ?>"
                                                 readonly>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Withdrawal %</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"

                                                value="<?php echo e($commission->withdrawal_percentage); ?>" readonly>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>

                                </div>
                                </div>

                                <?php
                                $selectedGateways = json_decode($commission->gateway_id ?? '');
                                $selectedtypes = json_decode($commission->type ?? '');
                                ?>
                                <div class="col-md-2">
                                    <label>Type</label>
                                    <select class="form-select select2" multiple  required>

                                        <option value="Agent" <?php echo e(in_array('Agent', $selectedtypes)? 'selected' : ''); ?>>Agent
                                        </option>
                                        <option value="Personal" <?php echo e(in_array('Personal', $selectedtypes)? 'selected' : ''); ?>>Personal</option>
                                        <option value="Merchant" <?php echo e(in_array('Merchant', $selectedtypes)? 'selected' : ''); ?>>Merchant</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Gateway</label>
                                    <select class="form-select select2" multiple >
                                        <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gateway->name); ?>" <?php echo e(in_array($gateway->name, $selectedGateways)
                                            ? 'selected' : ''); ?>>
                                            <?php echo e($gateway->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6 mt-4">
                                    <label for="deposit" class="form-label">Deposit Percentage</label>
                                    <input style="border:2px solid green;" type="number" name="deposit_percentage[]" id="deposit" class="form-control" step="0.01"
                                        placeholder="Enter deposit percentage">
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label for="withdrawal" class="form-label">Withdrawal Percentage</label>
                                    <input style="border:2px solid green;" type="number" name="withdrawal_percentage[]" id="withdrawal" class="form-control" step="0.01"
                                        placeholder="Enter withdrawal percentage">
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                    $key++;
                    ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>

                    <?php endif; ?>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
        var key = '<?php echo $key?>';
        let $select = $('.select2').select2({
            // placeholder: "Select Partner",
            // allowClear: true,
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




    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>



<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/payout/partner_commission.blade.php ENDPATH**/ ?>