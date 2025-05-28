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

   <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <div class="row ">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <?php if(adminAccessRoute(config('role.account_management.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.ewallet.accounts.details' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.ewallet.accounts.details')); ?>" class="menu-link">
                                    <div data-i18n="Accounts List">Accounts List</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_management.access.add'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.e_wallet_accounts.access.edit'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.on_off_account' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.on_off_account')); ?>" class="menu-link">
                                    <div data-i18n="Add Accounts">On/Off Accounts</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_group.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.account_group' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.account_group')); ?>" class="menu-link">
                                    <div data-i18n="Account Group">Account Group</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.gateways.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.gateway' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.gateway')); ?>" class="menu-link">
                                    <div data-i18n="Gateway">Gateway</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.categories.access.view'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.account_management.add_category' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.account_management.add_category')); ?>" class="menu-link">
                                    <div data-i18n="Add Category">Categories</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>


                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div id="listaccountsSection">
                <?php echo $__env->make('admin.payout.accounts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/ewallet_accounts.blade.php ENDPATH**/ ?>