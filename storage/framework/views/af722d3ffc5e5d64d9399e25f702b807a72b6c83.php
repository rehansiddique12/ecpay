<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>

            <form action="<?php echo e(route('admin.apis.balance.add')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row justify-content-between align-items-center">

                        <div class="col-md-12">
                            <div class="form-group">
                                <select name="partner_id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain" required>
                                    <option></option>
                                    <option value=""><?php echo app('translator')->get('Select Domain'); ?></option>
                                    <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($domain->id); ?>"
                                        <?php if(@request()->domain == $domain->id): ?> selected <?php endif; ?>>
                                            <?php echo e($domain->name); ?> ===> ( <?php echo e($domain->website); ?> )
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">Amount</label>
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="pr-3  mt-4">Charges</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="charges"/ required>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="pr-3  mt-4">Charges Type</label>
                                <select class="form-select" name="charges_type">
                                        <option value="1">Amount</option>
                                        <option value="2">Percentage</option>
                                    </select>
                            </div>
                        </div>



                        <!--<div class="col-md-12">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="pr-3">Adjustment</label>-->

                        <!--    </div>-->
                        <!--</div>-->




                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3  mt-4">Type</label>
                                <select class="form-select" name="adjustment" id="adjustment" required>
                                    <option value="4">Top-Up</option>
                                    <option value="1">Balance Adjustment</option>
                                    <option value="2">Deposit Adjustment</option>
                                    <option value="3">Withdrawal Adjustment</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mt-4">
                                <input value="1" type="radio" name="amount_type" id="amount_type1" checked>
                                <label class="pr-3">(+) Add</label>
                                <input value="2" type="radio" name="amount_type" id="amount_type2">
                                <label class="pr-3">(-) Deduct</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">Source</label>
                                <select class="form-select" name="source" required>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">Transactions Id</label>
                                <input type="text" class="form-control" name="txn" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3 mt-4">Remarks</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary mt-4"><?php echo app('translator')->get('Add'); ?></button>
                </div>

            </form>


        </div>
    </div>

    <div class="pagination float-right mr-4">
</div>


 <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/add_balance.blade.php ENDPATH**/ ?>