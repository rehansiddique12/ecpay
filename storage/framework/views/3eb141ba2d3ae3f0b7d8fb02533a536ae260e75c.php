    <style>
.text-primary {
    color: #7367f0 !important;
}

.text-secondary {
    color: #6c757d !important;
}



.dropzone-container {
    border: 2px dashed #d9d9d9;
    border-radius: 4px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.dropzone-container.dragging {
    border-color: #6c757d;
    background-color: rgba(0, 0, 0, 0.02);
}

.file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.upload-icon {
    margin-bottom: 1rem;
    color: whitesmoke;
}

.dropzone-message {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: whitesmoke;
}

.dropzone-note {
    font-size: 0.875rem;
    color: #6c757d;
}

.fw-medium {
    font-weight: 500;
}

.file-list {
    margin-top: 1rem;
}

.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    border-bottom: 1px solid #eee;
}

.remove-button {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 1.25rem;
    cursor: pointer;
}

.hidden {
    display: none;
}

#preview-img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
}
    </style>
    <div class="row ">
        <div class="col-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">

                    <h6>Create Account In Batch
                    </h6>
                    <form method="post" action="<?php echo e(route('admin.accounts.create')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label><?php echo e(trans('Category Name')); ?></label>
                                <select class="form-select" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id ?? ''); ?>"><?php echo e($category->name ?? ''); ?></option>
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
                                <select class="form-select" name="account_id">
                                    <option value="">Select Account Name</option>
                                    <?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($account->id ?? ''); ?>"><?php echo e($account->name ?? ''); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>

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
                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label><?php echo e(trans('Currency')); ?></label>
                                <select class="form-select" name="currency">
                                    <option value="">Select Currency</option>
                                    <option value="INR">INR</option>
                                </select>
                                <?php $__errorArgs = ['currency'];
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
                                    value="<?php echo e(old('daily_limit')); ?>">

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
                                <label> Monthly Deposit Amount Limit</label>
                                <input type="number" class="form-control" name="monthly_limit"
                                    value="<?php echo e(old('monthly_limit')); ?>">

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
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 col-6">
                                <label>Daily Withdrawal Amount Limit </label>
                                <input type="number" class="form-control" name="daily_limit_withdrawal"
                                    value="<?php echo e(old('daily_limit_withdrawal')); ?>">

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

                            <div class="form-group col-md-6 col-6">
                                <label>Monthly Withdrawal Amount Limit</label>
                                <input type="number" class="form-control" name="monthly_limit_withdrawal"
                                    value="<?php echo e(old('monthly_limit_withdrawal')); ?>">

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
                                <label> Monthly Deposit Transaction Limit</label>
                                <input type="number" class="form-control" name="monthly_deposit_transaction"
                                    value="<?php echo e(old('monthly_deposit_transaction')); ?>">
                                <?php $__errorArgs = ['monthly_deposit_transaction'];
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
                                <input type="number" class="form-control" name="monthly_withdrawl_transaction"
                                    value="<?php echo e(old('monthly_withdrawl_transaction')); ?>">
                                <?php $__errorArgs = ['monthly_withdrawl_transaction'];
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
                                <input type="number" class="form-control" name="daily_deposit_transaction"
                                    value="<?php echo e(old('daily_deposit_transaction')); ?>">
                                <?php $__errorArgs = ['daily_deposit_transaction'];
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
                                <input type="number" class="form-control" name="daily_withdrawl_transaction"
                                    value="<?php echo e(old('daily_withdrawl_transaction')); ?>">
                                <?php $__errorArgs = ['daily_withdrawl_transaction'];
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
                                <label> Max Amount Per Minute</label>
                                <input type="number" class="form-control" name="max_amount_per"
                                    value="<?php echo e(old('max_amount_per')); ?>">
                                <?php $__errorArgs = ['max_amount_per'];
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
                                <input type="number" class="form-control" name="max_amount_per"
                                    value="<?php echo e(old('max_amount_per')); ?>">
                                <?php $__errorArgs = ['max_amount_per'];
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
                            $time + 1800); $label="$from - $to" ; $slots[]=$label; } $chunks=array_chunk($slots,
                            ceil(count($slots) / 6)); // 6 columns ?> <div class="row">
                            <?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-2 col-sm-4 col-6">
                                <?php $__currentLoopData = $column; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="time_slots[]"
                                        value="<?php echo e($slot); ?>" id="slot_<?php echo e($i); ?>">
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
                <hr style="border-top: 1px solid white;">
                <div class="row">
                    <h6><?php echo e(trans('THRESHOLD ALERT')); ?></h6>
                    <div class="form-group col-md-3 col-3">
                        <label>Daily Deposit Limit Alert (%)</label>
                        <input type="number" class="form-control" min="1" max="100"
                            name="deposit_daily_limit_percentage"
                            value="<?php echo e(old('deposit_daily_limit_percentage', 100)); ?>">

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
                        <input type="number" class="form-control" min="1" max="100"
                            name="withdrawl_daily_limit_percentage"
                            value="<?php echo e(old('withdrawl_daily_limit_percentage', 100)); ?>">

                        <?php $__errorArgs = ['withdrawl_daily_limit_percentage'];
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
                        <input type="number" class="form-control" name="deposit_monthly_limit_percentage" min="1"
                            max="100" value="<?php echo e(old('depositC_monthly_limit_percentage', 100)); ?>">

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
                            value="<?php echo e(old('withdrawal_monthly_limit_percentage', 100)); ?>">

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
                            value="<?php echo e(old('low_balance_amount', 100)); ?>">

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
                            <input type="text" name="e_wallet_name[]" class="form-control" required>
                        </div>

                        <div class="form-group col-md-2 col-12">
                            <label>Device Name</label>
                            <input type="text" name="device_name[]" class="form-control" required>
                        </div>

                        <div class="form-group col-md-2 col-12">
                            <label> Account Number</label>
                            <input type="text" name="account_number[]" class="form-control" required>
                        </div>

                        <div class="form-group col-md-2 col-12">
                            <label>Account Group</label>
                            <select name="account_group[]" class="form-select" required>
                                <option value="">Select</option>
                                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($group->id); ?>"><?php echo e($group->group_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group col-md-1 col-12">
                            <label> Type</label>
                            <select name="account_type[]" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Agent">Agent</option>
                                <option value="Personal">Personal</option>
                            </select>
                        </div>

                        <div class="form-group col-md-1 col-12">
                            <label>In/Out</label>
                            <select name="in_out[]" class="form-select" required>
                                <option value="">Select</option>
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="both">Both</option>
                            </select>
                        </div>

                        <div class="form-group col-md-2 col-12">
                            <label>Location</label>
                            <select name="location[]" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Location 1">Location 1</option>
                                <option value="Location 2">Location 2</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-12">
                            <label for="qr_file">QR</label>
                            <input type="file" name="image" id="qr_file" class="form-control"
                                accept="image/png, image/jpeg">
                        </div>

                    </div>
                </div>

                <!-- More Button -->
                <div class="mt-3">
                    <button type="button" id="addMoreBtn" class="btn btn-primary">+ More</button>
                </div>

                <!-- JS for Cloning -->



                <!-- <div class="row justify-content-between">
                    <div class="col-sm-6 col-md-3">
                        <div class="col-12">
                            <div class="card mt-6">
                                <div class="card-body">
                                    <div class="dropzone-container" id="my-dropzone">
                                        <input type="file" name="file" id="file-input" class="file-input" multiple>
                                        <div id="image-preview" class="hidden">
                                            <img id="preview-img" src="" alt="Selected Image"
                                                class="img-fluid rounded mt-2" />
                                        </div>
                                        <div class="upload-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 16V8M12 8L8 12M12 8L16 12" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M3 15V16C3 17.6569 3 18.4853 3.24224 19.0815C3.45338 19.5989
                                                            3.80112 20.0466 4.31853 20.3578C4.91476 20.7 5.74319 20.7 7.4 20.7H16.6C18.2568
                                                            20.7 19.0852 20.7 19.6815 20.3578C20.1989 20.0466 20.5466 19.5989 20.7578 19.0815C21
                                                            18.4853 21 17.6569 21 16V15" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>

                                        <div class="dropzone-content">
                                            <p class="dropzone-message">Drop files here or click to upload</p>
                                        </div>
                                    </div>

                                    <?php $__errorArgs = ['image'];
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
                        </div>
                    </div>
                </div> -->
                <!-- <script>
                const fileInput = document.getElementById('file-input');
                const previewContainer = document.getElementById('image-preview');
                const previewImage = document.getElementById('preview-img');

                fileInput.addEventListener('change', function() {
                    const files = fileInput.files;
                    if (files && files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                            previewContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(files[0]);
                    }
                });
                </script> -->

                <div class="row mt-3 justify-content-between">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label><?php echo app('translator')->get('Status'); ?></label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                                <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                                    value="1">
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
    // $('select').select2({
    //     selectOnClose: true
    // });
});
    </script>
    <script>
$(document).ready(function() {
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


document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("file-input");
    const previewContainer = document.getElementById("image-preview");
    const previewImage = document.getElementById("preview-img");

    fileInput.addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove("hidden");
            };

            reader.readAsDataURL(file);
        }
    });
});
    </script>

    <script>
document.getElementById('addMoreBtn').addEventListener('click', function() {
    let container = document.getElementById('inputGroupContainer');
    let rows = container.querySelectorAll('.input-group-row');
    let lastRow = rows[rows.length - 1];
    let clone = lastRow.cloneNode(true);

    // Clear values in inputs and selects
    clone.querySelectorAll('input, select').forEach(function(el) {
        el.value = ''; // clear values in the clone
    });

    container.appendChild(clone);
});

// Submit form
document.getElementById('myForm').addEventListener('submit', function(event) {
    // Allow the form to submit after cloning rows
    // Optionally, you could validate fields here before submission

    // If any additional checks are needed before form submission, do them here

    // The form will automatically include all dynamically added inputs
});
    </script>
    <script>
document.getElementById('check_all_slots').addEventListener('change', function() {
    const isChecked = this.checked;
    const checkboxes = document.querySelectorAll('input[name="time_slots[]"]');
    checkboxes.forEach(cb => cb.checked = isChecked);
});
    </script>


    <?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/create_account.blade.php ENDPATH**/ ?>