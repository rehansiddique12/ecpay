<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>

                    <form method="post" action="<?php echo e(route('admin.accounts.update', $account->id)); ?>"
                          enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('put'); ?>
                        <div class="row">
                            <div class="form-group col-md-4 col-4">
                                <label><?php echo e(trans('Name')); ?></label>
                                <input type="text" class="form-control"
                                       name="e_wallet_name"
                                       value="<?php echo e(old('e_wallet_name', $account->e_wallet_name)); ?>" >

                                <?php $__errorArgs = ['e_wallet_name'];
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
                                <label><?php echo e(trans('Account No')); ?></label>
                                <input type="text" class="form-control"
                                       name="account_no"
                                       value="<?php echo e(old('account_no', $account->account_no)); ?>" >
                                <?php $__errorArgs = ['account_no'];
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
                                <label><?php echo e(trans('Type')); ?></label>
                                <select class="form-control" name="type">
                                    <option value="">Select Name</option>
                                    <option value="Personal" <?php if(old('type', $account->type) === 'Personal'): ?> selected <?php endif; ?>>Personal</option>
                                    <option value="Merchant" <?php if(old('type', $account->type) === 'Merchant'): ?> selected <?php endif; ?>>Merchant</option>
                                    <option value="Agent" <?php if(old('type', $account->type) === 'Agent'): ?> selected <?php endif; ?>>Agent</option>
                                </select>

                                <?php $__errorArgs = ['type'];
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
                                <label>Deposit Daily Limit</label>
                                <input type="number" class="form-control"
                                       name="daily_limit"
                                       value="<?php echo e(old('daily_limit', round($account->daily_limit, 2) ?: '')); ?>" >

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
                                <label>Deposit Monthly Limit</label>
                                <input type="number" class="form-control"
                                       name="monthly_limit"
                                       value="<?php echo e(old('monthly_limit',round($account->monthly_limit, 2) ?: '')); ?>" >

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
                                <label>Withdrawal Daily Limit</label>
                                <input type="number" class="form-control"
                                       name="daily_limit_withdrawal"
                                       value="<?php echo e(old('daily_limit_withdrawal',round($account->daily_limit_withdrawal, 2) ?: '')); ?>" >

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
                                <label>Withdrawal Monthly Limit</label>
                                <input type="number" class="form-control"
                                       name="monthly_limit_withdrawal"
                                       value="<?php echo e(old('monthly_limit_withdrawal',round($account->monthly_limit_withdrawal, 2) ?: '')); ?>" >

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
                                <label><?php echo e(trans('Account Type')); ?></label>
                                <select class="form-control" name="account_type" id="account_type">
                                    <option value="Both" <?php if(old('account_type', $account->account_type) === 'Both'): ?> selected <?php endif; ?>>Both</option>
                                    <option value="Deposit" <?php if(old('account_type', $account->account_type) === 'Deposit'): ?> selected <?php endif; ?>>Deposit</option>
                                    <option value="Withdrawal" <?php if(old('account_type', $account->account_type) === 'Withdrawal'): ?> selected <?php endif; ?>>Withdrawal</option>
                                </select>

                                <?php $__errorArgs = ['account_type'];
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

                            <div class="form-group col-md-6 col-6" id="max_withdrawal_limit">
                                <label>Max Withdrawal Limit</label>
                                <input type="number" class="form-control"
                                    name="max_withdrawal_amount"
                                    value="<?php echo e(old('max_withdrawal_amount',round($account->max_withdrawal_amount, 2) ?: '')); ?>" >

                                <?php $__errorArgs = ['max_withdrawal_amount'];
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
                            <div class="form-group col-md-4 col-4">
                                <label><?php echo e(trans('Apply Time Limit')); ?></label>
                                <select class="form-control" name="apply_time_limit" id="apply_time_limit">
                                    <option value="1" <?php if(old('apply_time_limit', $account->apply_time_limit) === 1): ?> selected <?php endif; ?>>Yes</option>
                                    <option value="0" <?php if(old('apply_time_limit', $account->apply_time_limit) === 0): ?> selected <?php endif; ?>>No</option>
                                </select>

                                <?php $__errorArgs = ['apply_time_limit'];
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

                            <div class="form-group col-md-4 col-4" id="from_time_div">
                                <label>From Time</label>
                                <input type="time" class="form-control"
                                    name="from_time"
                                    value="<?php echo e(old('from_time', $account->apply_time_limit==1?$account->from_time:'')); ?>" >

                                <?php $__errorArgs = ['from_time'];
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

                            <div class="form-group col-md-4 col-4" id="to_time_div">
                                <label>To Time</label>
                                <input type="time" class="form-control"
                                    name="to_time"
                                    value="<?php echo e(old('to_time', $account->apply_time_limit==1?$account->to_time:'')); ?>" >

                                <?php $__errorArgs = ['to_time'];
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
                            <div class="form-group col-md-3 col-3">
                                <label>Deposit Daily Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                        min="1" max="100"
                                       name="deposit_daily_limit_percentage"
                                       value="<?php echo e(old('deposit_daily_limit_percentage',$account->deposit_daily_limit_percentage)); ?>" >

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
                                <label>Deposit Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                       name="deposit_monthly_limit_percentage"
                                       min="1" max="100"
                                       value="<?php echo e(old('deposit_monthly_limit_percentage',$account->deposit_monthly_limit_percentage)); ?>" >

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
                                <label>Withdrawal Daily Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                        min="1" max="100"
                                       name="withdrawal_daily_limit_percentage"
                                       value="<?php echo e(old('withdrawal_daily_limit_percentage',$account->withdrawal_daily_limit_percentage)); ?>" >

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
                                <label>Withdrawal Monthly Limit Alert (%)</label>
                                <input type="number" class="form-control"
                                       name="withdrawal_monthly_limit_percentage"
                                       min="1" max="100"
                                       value="<?php echo e(old('withdrawal_monthly_limit_percentage',$account->withdrawal_monthly_limit_percentage)); ?>" >

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
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-sm-6 col-md-3">
                                <div class="col-12">
                                    <div class="card mt-6">
                                        <div class="card-body">
                                            <div class="dropzone-container" id="my-dropzone">
                                                <input type="file" name="file" id="file-input" class="file-input" multiple>
                                                <div id="image-preview" class="hidden">
                                                    <img id="preview-img" src="" alt="Selected Image" class="img-fluid rounded mt-2" />
                                                </div>
                                                <div class="upload-icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 16V8M12 8L8 12M12 8L16 12" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M3 15V16C3 17.6569 3 18.4853 3.24224 19.0815C3.45338 19.5989
                                                            3.80112 20.0466 4.31853 20.3578C4.91476 20.7 5.74319 20.7 7.4 20.7H16.6C18.2568
                                                            20.7 19.0852 20.7 19.6815 20.3578C20.1989 20.0466 20.5466 19.5989 20.7578 19.0815C21
                                                            18.4853 21 17.6569 21 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
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
                        </div>

                        <script>
                            const fileInput = document.getElementById('file-input');
                            const previewContainer = document.getElementById('image-preview');
                            const previewImage = document.getElementById('preview-img');

                            fileInput.addEventListener('change', function () {
                                const files = fileInput.files;
                                if (files && files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = function (e) {
                                        previewImage.src = e.target.result;
                                        previewContainer.classList.remove('hidden');
                                    };
                                    reader.readAsDataURL(files[0]);
                                }
                            });
                        </script>

                        <div class="row mt-3 justify-content-between">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('Status'); ?></label>
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <span id="disableText" class="me-12 text-primary"><?php echo app('translator')->get('No'); ?></span>
                                        <input class="form-check-input" type="checkbox" id="statusSwitch"
                                            name="status" value="1">
                                        <span id="enableText" class="ms-2 text-secondary"><?php echo app('translator')->get('Yes'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="btn  btn-primary btn-block mt-3"><?php echo app('translator')->get('Save Changes'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>


<?php $__env->startPush('js'); ?>
     <script>

        "use strict";
        $(document).ready(function (e) {


            $('#image').change(function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });


        });

        $(document).ready(function () {
            $('select').select2({
                selectOnClose: true
            });
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
</script>
<?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/edit_account.blade.php ENDPATH**/ ?>