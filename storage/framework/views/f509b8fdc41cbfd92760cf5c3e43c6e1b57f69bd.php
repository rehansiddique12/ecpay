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
        #currency-wrapper {
            white-space: nowrap;
        }
    </style>

    <?php $__env->stopPush(); ?>
    <?php
    $currentRoute = Route::currentRouteName();
    ?>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <div>
                            <button
                                class="btn <?php echo e(in_array($currentRoute, ['admin.ewallet.accounts.details', 'admin.accounts.edit']) ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.ewallet.accounts.details')); ?>" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.account_group')); ?>" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>

                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.gateway')); ?>" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_category')); ?>" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="row">
                <h3 class="text-primary text-bold">Edit Account In Batch
                </h3>
                <form method="post" action="<?php echo e(route('admin.accounts.update' , $e_wallet_account->id)); ?>"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label><?php echo e(trans('Category Name')); ?></label>
                            <select class="form-select" name="category_id" id="category-select">
                                <option value="">Select Category</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e((isset($e_wallet_account) && $e_wallet_account->
                                    category_id == $category->id) ? 'selected' : ''); ?>>
                                    <?php echo e($category->name ?? ''); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group col-md-4 col-4">
                            <label><?php echo e(trans('Select Account Name')); ?></label>

                            <div class="input-group">
                                <select class="form-select" name="account_id" id="account-select">
                                    <option value="">Select Account Name</option>
                                </select>
                                <span class="input-group-text" id="currency-wrapper" style="display: none;">
                                    <span id="currency-code"></span>
                                </span>
                            </div>

                            <?php $__errorArgs = ['account_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div>

                    <hr style="border-top: 1px solid white;">
                    <h6 class="mb-0"><?php echo e(trans(' CONFIGURATION')); ?></h6>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="daily_limit"
                                value="<?php echo e(old('daily_limit', $e_wallet_account->daily_limit ?? '')); ?>" required>

                            <?php $__errorArgs = ['daily_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group col-md-6 col-6">
                            <label>Daily Withdrawal Amount Limit </label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal"
                                value="<?php echo e(old('daily_limit_withdrawal', $e_wallet_account->daily_limit_withdrawal ?? '')); ?>"
                                required>

                            <?php $__errorArgs = ['daily_limit_withdrawal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit"
                                value="<?php echo e(old('monthly_limit', $e_wallet_account->monthly_limit ?? '')); ?>" required>

                            <?php $__errorArgs = ['monthly_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label>Monthly Withdrawal Amount Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                value="<?php echo e(old('monthly_limit_withdrawal', $e_wallet_account->monthly_limit_withdrawal ?? '')); ?>"
                                required>

                            <?php $__errorArgs = ['monthly_limit_withdrawal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Daily Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_limit_transaction"
                                value="<?php echo e(old('daily_limit_transaction', $e_wallet_account->daily_limit_transaction ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['daily_limit_transaction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Daily Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="daily_limit_withdrawal_transaction"
                                value="<?php echo e(old('daily_limit_withdrawal_transaction', $e_wallet_account->daily_limit_withdrawal_transaction ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['daily_limit_withdrawal_transaction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Deposit Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_transaction"
                                value="<?php echo e(old('monthly_limit_transaction', $e_wallet_account->monthly_limit_transaction ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['monthly_limit_transaction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Monthly Withdrawl Transaction Limit</label>
                            <input type="number" class="form-control" name="monthly_limit_withdrawal_transaction"
                                value="<?php echo e(old('monthly_limit_withdrawal_transaction', $e_wallet_account->monthly_limit_withdrawal_transaction ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['monthly_limit_withdrawal_transaction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 col-6">
                            <label> Max Transaction Per Minute</label>
                            <input type="number" class="form-control" name="max_transaction_per_minute"
                                value="<?php echo e(old('max_transaction_per_minute', $e_wallet_account->max_transaction_per_minute ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['max_transaction_per_minute'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group col-md-6 col-6">
                            <label> Max Amount Per Minute</label>
                            <input type="number" class="form-control" name="max_amount_per_minute"
                                value="<?php echo e(old('max_amount_per_minute', $e_wallet_account->max_amount_per_minute ?? '')); ?>"
                                required>
                            <?php $__errorArgs = ['max_amount_per_minute'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>


                    <hr style="border-top: 1px solid white;">
                    <div class="row">
                        <div class="form-group col-md-12 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><?php echo e(trans('Time Configuration')); ?></h6>
                                <div>
                                    <input type="checkbox" id="check_all_slots" class="form-check-input">
                                    <label for="check_all_slots" class="form-check-label text-white">Check All</label>
                                </div>
                            </div>

                            <?php
                            $start = strtotime('00:00');
                            $end = strtotime('24:00');
                            $i = 0;
                            $slots = [];

                            for ($time = $start; $time < $end; $time +=1800) { $from=date('H:i', $time); $to=date('H:i',
                                $time + 1800); $slots[]="$from - $to" ; } $chunks=array_chunk($slots, ceil(count($slots)
                                / 6)); // 6 columns ?> <div class="row">
                                <?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-2 col-sm-4 col-6">
                                    <?php $__currentLoopData = $column; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="time_slots[]"
                                            value="<?php echo e($slot); ?>" id="slot_<?php echo e($i); ?>" <?php echo e(in_array($slot, $savedSlots ?? [])
                                            ? 'checked' : ''); ?>>
                                        <label class="form-check-label text-white" for="slot_<?php echo e($i); ?>">
                                            <?php echo e($slot); ?>

                                        </label>
                                    </div>
                                    <?php $i++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
            </div>

            <hr style="border-top: 1px solid white;">
            <div class="row">
                <h6><?php echo e(trans('THRESHOLD ALERT')); ?></h6>
                <div class="form-group col-md-3 col-3">
                    <label>Daily Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="deposit_daily_limit_percentage"
                        value="<?php echo e(old('deposit_daily_limit_percentage', $e_wallet_account->deposit_daily_limit_percentage ?? '')); ?>"
                        required>

                    <?php $__errorArgs = ['deposit_daily_limit_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-danger"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Daily Withdrawl Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100" name="withdrawal_daily_limit_percentage"
                        value="<?php echo e(old('withdrawal_daily_limit_percentage', $e_wallet_account->withdrawal_daily_limit_percentage ?? '')); ?>"
                        required>

                    <?php $__errorArgs = ['withdrawal_daily_limit_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-danger"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Deposit Limit Alert (%)</label>
                    <input type="number" class="form-control" name="deposit_monthly_limit_percentage" min="1" max="100"
                        value="<?php echo e(old('deposit_monthly_limit_percentage', $e_wallet_account->deposit_monthly_limit_percentage ?? '')); ?>"
                        required>

                    <?php $__errorArgs = ['deposit_monthly_limit_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-danger"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Monthly Withdrawal Limit Alert (%)</label>
                    <input type="number" class="form-control" min="1" max="100"
                        name="withdrawal_monthly_limit_percentage"
                        value="<?php echo e(old('withdrawal_monthly_limit_percentage', $e_wallet_account->withdrawal_monthly_limit_percentage ?? '')); ?>"
                        required>

                    <?php $__errorArgs = ['withdrawal_monthly_limit_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-danger"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group col-md-3 col-3">
                    <label>Low Balance Alert Amount</label>
                    <input type="number" class="form-control" name="low_balance_amount" min="1"
                        value="<?php echo e(old('low_balance_amount', $e_wallet_account->low_balance_amount ?? '')); ?>" required>

                    <?php $__errorArgs = ['low_balance_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-danger"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>


            <hr>
            <div class="col-12 mb-3">
                <h6><?php echo e(__('Add Account')); ?></h6>
            </div>
            <div id="inputGroupContainer">
                <div class="row input-group-row">
                    <div class="form-group col-md-2 col-12">
                        <label>Account Name</label>
                        <input type="text" name="e_wallet_name[]" value="<?php echo e(old('e_wallet_name', $e_wallet_account->e_wallet_name ?? '')); ?>" required class="form-control" required>
                    </div>
                    <input type="hidden" name="first_account_id" value="<?php echo e($e_wallet_account->id ?? ''); ?>">

                    <div class="form-group col-md-2 col-12">
                        <label>Device Name</label>
                        <input type="text" name="device_name[]" value="<?php echo e(old('device_name', $e_wallet_account->device_name ?? '')); ?>" required class="form-control" required>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>Account Number</label>
                        <input type="text" name="account_number[]" class="form-control" value="<?php echo e(old('account_number', $e_wallet_account->account_no ?? '')); ?>" required>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label for="">Account Group</label>
                        <select class="form-select select3" name="account_group[0][]" multiple
                            data-placeholder="Select Groups" data-allow-clear="true">
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($group->id); ?>"
                                    <?php echo e(in_array($group->id, $selectedGroupIds) ? 'selected' : ''); ?>>
                                    <?php echo e($group->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>


                    <div class="form-group col-md-1 col-12">
                        <label>Type</label>
                        <select name="account_type[]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Agent"    <?php echo e($e_wallet_account->type == "Agent" ?  "selected" : ""); ?> >Agent</option>
                            <option value="Merchant" <?php echo e($e_wallet_account->type == "Merchant" ?  "selected" : ""); ?> >Merchant</option>
                            <option value="Personal" <?php echo e($e_wallet_account->type == "Personal" ?  "selected" : ""); ?> >Personal</option>
                        </select>
                    </div>

                    <div class="form-group col-md-1 col-12">
                        <label>In/Out</label>
                        <select name="in_out[]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Deposit" <?php echo e($e_wallet_account->account_type == "Deposit" ?  "selected" : ""); ?>>Deposit</option>
                            <option value="Withdrawal" <?php echo e($e_wallet_account->account_type == "Withdrawal" ?  "selected" : ""); ?>>Withdrawal</option>
                            <option value="Both" <?php echo e($e_wallet_account->account_type == "Both" ?  "selected" : ""); ?>>Both</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>Location</label>
                        <select name="location[]" class="form-select select2" data-placeholder="Select Location"
                            data-allow-clear="true">
                            <option></option>
                            <option value=""><?php echo app('translator')->get('Select Location'); ?></option>
                            <?php $__currentLoopData = $users_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php echo e($location->id == $e_wallet_account->location_id ?  "selected" : ""); ?> value="<?php echo e($location->id); ?>"><?php echo e($location->location); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group col-md-2 col-12">
                        <label>QR</label>
                        
                            <?php if(!empty($e_wallet_account->image)): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(asset('assets/uploads/withdraw/' . $e_wallet_account->image)); ?>"
                                        alt="QR Code"
                                        class="img-thumbnail"
                                        style="max-width: 100px;">
                                </div>
                            <?php endif; ?>
                        <input type="file" name="image[]" class="form-control qr-file" accept="image/png, image/jpeg">
                    </div>

                </div>
            </div>

            <!-- More Button -->
            <div class="mt-3">
                <button type="button" id="addMoreBtn" class="btn btn-primary">+ More</button>
            </div>

            <div class="row mt-3 justify-content-between">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label><?php echo app('translator')->get('Status'); ?></label>
                        <div class="form-check form-switch d-flex align-items-center">
                            <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="statusSwitch"
                                name="status"
                                value="1"
                                <?php echo e(isset($e_wallet_account) && $e_wallet_account->status == 1 ? 'checked' : ''); ?>>
                            <span id="enableText" class="ms-2 text-secondary"><?php echo app('translator')->get('Yes'); ?></span>
                        </div>
                    </div>
                    <button type="submit" class="btn  btn-primary btn-block mt-3"><?php echo app('translator')->get('Save Changes'); ?></button>

                </div>
            </div>


            </form>
        </div>

    </div>
    </div>

    <!-- Hidden template for cloning -->
    <div id="rowTemplate" style="display:none;">
        <div class="row input-group-row">
            <div class="form-group col-md-2 col-12">
                <label>Account Name</label>
                <input type="text" name="e_wallet_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Device Name</label>
                <input type="text" name="device_name[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Account Number</label>
                <input type="text" name="account_number[]" class="form-control" required>
            </div>

            <div class="form-group col-md-2 col-12">
                <label for="">Account Group</label>
                <select class="form-select select2" name="account_group[__INDEX__][]" multiple
                    data-placeholder="Select Groups" data-allow-clear="true">
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($group->id); ?>"><?php echo e($group->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>Type</label>
                <select name="account_type[]" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Agent">Agent</option>
                    <option value="Merchant">Merchant</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>

            <div class="form-group col-md-1 col-12">
                <label>In/Out</label>
                <select name="in_out[]" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Deposit">Deposit</option>
                    <option value="Withdrawal">Withdrawal</option>
                    <option value="Both">Both</option>
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>Location</label>
                <select name="location[]" class="form-select select2" data-placeholder="Select Location"
                    data-allow-clear="true">
                    <option></option>
                    <option value=""><?php echo app('translator')->get('Select Location'); ?></option>
                    <?php $__currentLoopData = $users_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->location); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-2 col-12">
                <label>QR</label>
                <input type="file" name="image[]" class="form-control qr-file" accept="image/png, image/jpeg">
            </div>

            <div class="form-group col-md-1 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-btn">Remove</button>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
        $(document).ready(function() {


            let $select = $('.select3').select2({
                // placeholder: "Select Partner",
                allowClear: true,
                // selectOnClose: true,
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

            let rowIndex = 1;

            // Add first row on page load
            // addNewRow();

            // Add more button functionality
            $('#addMoreBtn').click(function() {
                addNewRow();
            });

            // Remove button functionality
            $(document).on('click', '.remove-btn', function() {
                if ($('#inputGroupContainer .input-group-row').length > 1) {
                    $(this).closest('.input-group-row').remove();
                } else {
                    alert("You need at least one row");
                }
            });

            function addNewRow() {
                // Get HTML from template
                let rowHtml = $('#rowTemplate').html();

                // Replace __INDEX__ with current index
                rowHtml = rowHtml.replace(/__INDEX__/g, rowIndex);

                // Convert HTML to jQuery object
                let $clone = $(rowHtml);

                // Generate unique IDs if needed
                let timestamp = Date.now();
                $clone.find('[id]').each(function() {
                    let newId = $(this).attr('id') + '_' + timestamp;
                    $(this).attr('id', newId);
                });

                // Append to container
                $('#inputGroupContainer').append($clone);

                // Initialize Select2
                $clone.find('.select2').select2({
                    placeholder: function() {
                        return $(this).data('placeholder');
                    },
                    allowClear: true
                });

                // Hide remove button if it's the first row
                if ($('#inputGroupContainer .input-group-row').length === 1) {
                    $clone.find('.remove-btn').hide();
                } else {
                    $clone.find('.remove-btn').show();
                }

                // Increment for next row
                rowIndex++;
            }
        });

        $(document).ready(function () {
            const accountRoute = "<?php echo e(route('admin.get.e_wallet_accounts', ['category_id' => '__CATEGORY_ID__'])); ?>";
            const selectedAccountId = "<?php echo e($e_wallet_account->gateway_id ?? ''); ?>";

            function loadAccounts(categoryId, selectedId = '') {
                const url = accountRoute.replace('__CATEGORY_ID__', categoryId);

                $('#account-select').empty().append('<option value="">Select Account Name</option>');
                $('#currency-wrapper').hide();
                $('#currency-code').text('');

                if (categoryId) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data.length === 0) {
                                $('#account-select').append('<option value="">No accounts found</option>');
                            } else {
                                $.each(data, function (index, account) {
                                    const selected = account.id == selectedId ? 'selected' : '';
                                    $('#account-select').append(
                                        `<option value="${account.id}" data-currency="${account.currency}" ${selected}>${account.name}</option>`
                                    );
                                });

                                // Show currency if selected
                                const selectedOption = $('#account-select').find('option:selected');
                                const currency = selectedOption.data('currency');
                                if (currency) {
                                    $('#currency-code').text(currency);
                                    $('#currency-wrapper').show();
                                }
                            }
                        },
                        error: function (xhr) {
                            alert('Failed to fetch accounts. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            }

            // Load accounts on page load if category is selected (edit mode)
            const currentCategory = $('#category-select').val();
            if (currentCategory) {
                loadAccounts(currentCategory, selectedAccountId);
            }

            // On category change
            $('#category-select').on('change', function () {
                const categoryId = $(this).val();
                loadAccounts(categoryId);
            });

            // When account changes
            $('#account-select').on('change', function () {
                const selected = $(this).find(':selected');
                const currency = selected.data('currency');

                if (currency) {
                    $('#currency-code').text(currency);
                    $('#currency-wrapper').show();
                } else {
                    $('#currency-wrapper').hide();
                    $('#currency-code').text('');
                }
            });



            function toggleMaxWithdrawalLimit() {
                if ($('#account_type').val() === 'Deposit') {
                    $('#max_withdrawal_limit').hide();
                } else {
                    $('#max_withdrawal_limit').show();
                }
            }

            $('#account_type').on('change', toggleMaxWithdrawalLimit);

            // Initialize the visibility on page load
            toggleMaxWithdrawalLimit();

            function toggleTimeFields() {
                if ($('#apply_time_limit').val() == 0) {
                    $('#from_time_div').hide();
                    $('#to_time_div').hide();
                } else {
                    $('#from_time_div').show();
                    $('#to_time_div').show();
                }
            }

            $('#apply_time_limit').on('change', toggleTimeFields);

            // Initialize the visibility on page load
            toggleTimeFields();


        });

        document.addEventListener("DOMContentLoaded", function() {
            const statusSwitch = document.getElementById("statusSwitch");
            const disableText = document.getElementById("disableText");
            const enableText = document.getElementById("enableText");

            statusSwitch.addEventListener("change", function() {
                if (this.checked) {
                    disableText.classList.remove("text-primary");
                    disableText.classList.add("text-secondary");

                    enableText.classList.remove("text-secondary");
                    enableText.classList.add("text-primary");
                } else {
                    disableText.classList.remove("text-secondary");
                    disableText.classList.add("text-primary");

                    enableText.classList.remove("text-primary");
                    enableText.classList.add("text-secondary");
                }
            });
        });

        // Event delegation for dynamically added remove buttons
        document.getElementById('inputGroupContainer').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-btn')) {
                let rows = document.querySelectorAll('.input-group-row');
                if (rows.length > 1) {
                    e.target.closest('.input-group-row').remove();
                }
            }
        });

        document.getElementById('check_all_slots').addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('input[name="time_slots[]"]');
            checkboxes.forEach(cb => cb.checked = isChecked);
        });
    </script>



    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/edit_account.blade.php ENDPATH**/ ?>