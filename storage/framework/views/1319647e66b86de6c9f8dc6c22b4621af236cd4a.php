<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <h3 style="color: #7376f0"><?php echo e($pageTitle); ?></h3>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e(Auth::guard('partner')->user()->balance); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('My Balance'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['withdrawal_able_amount']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Withdrawalable Amount'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payment_count']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Deposit Transactions'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_sum']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Deposit Amount'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="me-3" style="font-size: 2rem; color: #65658b;">
                                <i class="fa fa-hand-holding-usd"></i>
                            </span>
                            <h2 class="text-dark mb-0 font-weight-medium">
                                <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_charge']); ?>

                            </h2>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                            <?php echo app('translator')->get('Deposit Charges'); ?>
                        </h6>
                    </div>
                </div>
            </div>


            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payout_count']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Withdrawal Transactions'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_sum']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Withdrawal Amount'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_charge']); ?>

                                    </h2>
                                </div>

                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Withdrawal Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold"><?php echo app('translator')->get('Today Statistics'); ?></h6>
            </div>


            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payment_count_today']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Today Deposit Transactions'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_sum_today']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Today Deposit Amount'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_charge_today']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Deposit Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payout_count_today']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Today Withdrawal Transactions'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_sum_today']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Today Withdrawal Amount'); ?>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_charge_today']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Withdrawal Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold"><?php echo app('translator')->get('This Month Statistics'); ?></h6>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payment_count_current_month']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">This
                                    Month<br>Deposit Transactions
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_sum_current_month']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">This Month<br>
                                    Deposit Amount
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-muted" style="font-size: 2rem;">
                                <i class="fa fa-hand-holding-usd"></i>
                            </div>
                            <h2 class="text-dark mb-0 font-weight-medium">
                                <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payment_charge_current_month']); ?>

                            </h2>
                        </div>
                        <h6 class="text-muted font-weight-normal mb-0 mt-2 w-100 text-truncate">
                            This Month<br><?php echo app('translator')->get('Deposit Charges'); ?>
                        </h6>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_payout_count_current_month']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">This Month<br>
                                    Withdrawal Transactions</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_sum_current_month']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">This Month<br>
                                    Withdrawal Amount</h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_payout_charge_current_month']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">This Month<br>
                                    <?php echo app('translator')->get('Withdrawal Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <h6 class="text-dark font-weight-bold"><?php echo app('translator')->get('Completed Settlements'); ?></h6>
            </div>

        </div>

        <div class="row justify-content-center">

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3" style="margin-bottom: 60px;">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_settlement_count']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Settlements
                                    Count</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_sum']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Settlements
                                    Amount</h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_charge']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Settlements Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_settlement_count_daily']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Today Settlements
                                    Count
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_sum_daily']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Today Settlements
                                    Amount
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_charge_daily']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Today Settlements Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <span class="opacity-7 text-muted"><i
                                            class="fas fa-wallet"></i></span>&nbsp;&nbsp;
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <?php echo e($transection_data['total_settlement_count_current_month']); ?></h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Month Settlements
                                    Count</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_sum_current_month']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Month Settlements
                                    Amount</h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-2">
                <div class="card shadow border-right">
                    <div class="card-body">
                        <div class="d-flex d-lg-flex d-md-block align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">
                                        <span class="opacity-7 text-muted"><i
                                                class="fa fa-hand-holding-usd"></i></span>&nbsp;&nbsp;
                                        <sup><?php echo e(trans($basic->currency_symbol)); ?></sup><?php echo e($transection_data['total_settlement_charge_current_month']); ?>

                                    </h2>
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Month Settlements Charges'); ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title"><?php echo app('translator')->get("This Month's Summary"); ?></h4>
                                <div>
                                    <canvas id="line-chart" height="150"></canvas>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h4 class="card-title"><?php echo app('translator')->get('Gateway Uses'); ?></h4>
                                <div>
                                    <canvas id="pie-chart" height="280"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>











    </div>
    <?php $__env->startPush('js'); ?>
        <script src="<?php echo e(asset('assets/admin/js/Chart.min.js')); ?>"></script>

        <script>
            "use strict";

            $(document).on('click', '.user-login', function() {
                var id = $(this).data('id');
                $('.userId').val(id);
            });

            new Chart(document.getElementById("line-chart"), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($statistics['schedule']->keys(), 15, 512) ?>,
                    datasets: [{
                        data: <?php echo json_encode($statistics['deposit']->values(), 15, 512) ?>,
                        label: "Deposits",
                        borderColor: "#9b18cb",
                        fill: false
                    }, {
                        data: <?php echo json_encode($statistics['payout']->values(), 15, 512) ?>,
                        label: "Payout",
                        borderColor: "#0dd2bb",
                        fill: false
                    }]
                }
            });


            new Chart(document.getElementById("pie-chart"), {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($pieLog->pluck('level'), 15, 512) ?>,
                    datasets: [{
                        backgroundColor: ["#6fbbff", "#ff6f62", "#05ffe4", "#98df8a", "#8b6ef3", "#f9dd7e",
                            "#f34da3"
                        ],
                        data: <?php echo json_encode($pieLog->pluck('value'), 15, 512) ?>,
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItems, data) {
                                return data.labels[tooltipItems.index] + ': ' + data.datasets[0].data[tooltipItems
                                    .index] + '%';
                            }
                        }

                    }
                }
            });


            $(document).on('click', '#details', function() {
                var title = $(this).data('servicetitle');
                var description = $(this).data('description');
                $('#title').text(title);
                $('#servicedescription').text(description);
            });

            $(document).ready(function() {
                let isActiveCronNotification = '<?php echo e($basic->is_active_cron_notification); ?>';
                if (isActiveCronNotification == 1)
                    $('#cron-info').modal('show');
                $(document).on('click', '.copy-btn', function() {
                    var _this = $(this)[0];
                    var copyText = $(this).parents('.input-group-append').siblings('input');
                    $(copyText).prop('disabled', false);
                    copyText.select();
                    document.execCommand("copy");
                    $(copyText).prop('disabled', true);
                    $(this).text('Coppied');
                    setTimeout(function() {
                        $(_this).text('');
                        $(_this).html('<i class="fas fa-copy"></i>');
                    }, 500)
                });
            })
        </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/dashboard.blade.php ENDPATH**/ ?>