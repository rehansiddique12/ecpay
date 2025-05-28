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
    <form action="<?php echo e(route('admin.reports.cal2')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label>Source</label>
                    <select name="website" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
                        <option value="">All Source</option>
                        <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($partner->id); ?>" <?php if(@request()->website == $partner->id): ?> selected <?php endif; ?>><?php echo e($partner->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group mt-2">
                    <br>
                    <button type="submit" value="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">
                <h3>Summary</h3>
                <table class="table table-bordered">
                    <thead>
                    <th>Amount</th>
                    <th>Charge</th>
                    <th>Final Amount</th>
                    <th>Type</th>
                    </thead>
                    <tbody>
                        <tr>
                            <?php if(isset(($deposits))): ?>
                            <?php $deposit_final_deposit = ($deposits->payment_amount - $deposits->payment_charge ) ?>
                            <td><?php echo e($deposits->payment_amount); ?></td>
                            <td> <?php echo e($deposits->payment_charge); ?></td>
                            <td><?php echo e($deposit_final_deposit); ?></td>
                            <td>Deposit</td>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <?php if(isset(($withdrawals))): ?>
                            <?php $withdrawal_final_deposit = -($withdrawals->payment_amount + $withdrawals->payment_charge ) ?>
                            <td><?php echo e($withdrawals->payment_amount); ?></td>
                            <td> <?php echo e($withdrawals->payment_charge); ?></td>
                            <td><?php echo e($withdrawal_final_deposit); ?></td>
                            <td>Withdrawal</td>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <?php if(isset(($ApiTransactions))): ?>
                            <?php $api_final_deposit = ($ApiTransactions->payment_amount - $ApiTransactions->payment_charge ) ?>
                            <td><?php echo e($ApiTransactions->payment_amount); ?></td>
                            <td> <?php echo e($ApiTransactions->payment_charge); ?></td>
                            <td><?php echo e($api_final_deposit); ?></td>
                            <td>ApiTransactions</td>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <?php if(isset(($ApiTransactions))): ?>
                            <?php $sat_final_deposit = -($Settlements->payment_amount + $Settlements->payment_charge ) ?>
                            <td><?php echo e($Settlements->payment_amount); ?></td>
                            <td> <?php echo e($Settlements->payment_charge); ?></td>
                            <td><?php echo e($sat_final_deposit); ?></td>
                            <td>Settlements</td>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <?php if(isset(($PartnerCommissions))): ?>
                            <?php $pat_final_deposit = $PartnerCommissions->partner_profit ?>
                            <td><?php echo e($PartnerCommissions->partner_profit); ?></td>
                            <td> </td>
                            <td></td>
                            <td>PartnerCommissions</td>
                            <?php endif; ?>
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td> <?php echo e($deposit_final_deposit + $withdrawal_final_deposit +  $api_final_deposit +  $sat_final_deposit+ $pat_final_deposit); ?>  </td>
                            <td>Balance</td>
                        </tr>


                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('js'); ?>
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/reports/logs3.blade.php ENDPATH**/ ?>