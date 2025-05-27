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
            color: white !important
        }

        .categories-show-table {
            display: none;
            /* Initially hidden */
        }

        h3 {
            color: #7367f0 !important
        }

        .dropzone-container {
            width: 100%;
        }

        .dropzone {
            border: 1px dashed #ccc;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #999;
            background-color: #f9f9f9;
        }

        .upload-icon {
            background-color: #f0f0f0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .upload-svg {
            color: #666;
        }

        .dropzone-title {
            font-size: 1.125rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .dropzone-description {
            font-size: 0.875rem;
            color: #666;
            margin: 0;
        }

        .hidden-input {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
        }

        .preview-image {
            max-width: 100%;
            margin-top: 1rem;
            border-radius: 4px;
            display: none;
        }

        #image_preview_container:not([src="/placeholder.svg"]) {
            display: block;
        }

        label {
            margin-bottom: 5px;
        }
    </style>

    <?php $__env->stopPush(); ?>
    <?php
    $currentRoute = Route::currentRouteName();
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : ''); ?>">
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
                            <button class="btn <?php echo e($currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_category')); ?>" class="menu-link">
                                    <div data-i18n="Add Category">Add Category</div>
                                </a>
                            </button>
                        </div>
                        


                    </div>
                    <div class="row">
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
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/add_account.blade.php ENDPATH**/ ?>