<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">

            <!-- / Navbar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-6">
                                    <div class="user-profile-header-banner">
                                        <img src="../../assets/img/pages/profile-banner.png" alt="Banner image"
                                            class="rounded-top img-fluid" />
                                    </div>
                                    <div
                                        class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                            <img src="../../assets/img/avatars/1.png" alt="user image"
                                                class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
                                        </div>
                                        <div class="flex-grow-1 mt-3 mt-lg-5">
                                            <div
                                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                                                <div class="user-profile-info">
                                                    <h4 class="mb-2 mt-lg-6"><?php echo e($data->name); ?> </h4>

                                                </div>
                                                <?php
                                                $depositColor = 'text-danger';
                                                ?>

                                                <?php if($total_deposit > 60): ?>
                                                <?php $depositColor = 'text-success'; ?>
                                                <?php elseif($total_deposit >= 40 && $total_deposit <= 60): ?> <?php
                                                    $depositColor='text-warning' ; ?> <?php endif; ?> <span>
                                                    <h4 class="mb-n3">Gateway performance</h4>
                                                    <br>
                                                    Deposit: <span
                                                        class="<?php echo e($depositColor); ?>"><?php echo e($total_deposit); ?>%</span>
                                                    <br>
                                                    Withdrawal: <span class="text-danger">##%</span>
                                                    </span>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Header -->

                        <!-- Navbar pills -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-align-top">
                                    <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="javascript:void(0);"><i
                                                    class="icon-base ti tabler-user-check icon-sm me-1_5"></i>
                                                Profile</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('admin.merchant.logs',$data->id)); ?>"><i
                                                    class="icon-base ti tabler-list icon-sm me-1_5"></i> Logs</a>
                                        </li>


                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Navbar pills -->

                        <!-- User Profile Content -->
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-5">
                                <!-- About User -->
                                <div class="card mb-6">
                                    <div class="card position-relative">
                                        
                                        <a class="btn btn-sm position-absolute end-0 top-0 m-2 edit_button"
                                            href="<?php echo e(route('admin.apis.login', $data->id)); ?>" target="_blank">
                                            <i class="ti tabler-login me-1 fs-4"></i>
                                        </a>

                                        <div class="card-body">
                                            <ul class="list-unstyled my-3 py-1">
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Username:</span>
                                                    <span><?php echo e($data->username ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Password:</span>
                                                    <span
                                                        style="overflow: hidden;"><?php echo e($data->password_string ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Api Key:</span>
                                                    <span><?php echo e($data->api_key ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-4">
                                                    <span class="fw-medium mx-2">Secret Key:</span>
                                                    <span
                                                        style="overflow: hidden;"><?php echo e($data->secret_key ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Deposit Url:</span>
                                                    <span><?php echo e($data->api_endpoint_deposit ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Withdrawal Url:</span>
                                                    <span><?php echo e($data->api_endpoint_withdrawal ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Min Deposit:</span>
                                                    <span><?php echo e($data->min_deposit ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Min Withdrawal:</span>
                                                    <span><?php echo e($data->min_withdrawal ?? '-'); ?></span>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <span class="fw-medium mx-2">Provider Name:</span>
                                                    <span><?php echo e($data->provider_name ?? '-'); ?></span>
                                                </li>
                                            </ul>
                                        </div>

                                        
                                        <a class="btn btn-sm position-absolute end-0 bottom-0 m-2 edit_button"
                                        data-copy="Username: <?php echo e($data['username']); ?>&#10;Password: <?php echo e($data['password_string']); ?>&#10;Api Key: <?php echo e($data['api_key']); ?>&#10;Secret Key: <?php echo e($data['secret_key']); ?>"
                                            onclick="copyToClipboard(this)">
                                            <i class="ti tabler-copy-check me-1 fs-4"></i>
                                        </a>
                                    </div>

                                </div>
                                <!--/ About User -->
                                <!-- Profile Overview -->
                                <div class="card mb-6">
                                    <div class="card-body">
                                        <p class="card-text text-uppercase text-body-secondary small">Commission</p>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex align-items-center mb-4">
                                                <i class="icon-base ti tabler-check icon-lg"></i><span
                                                    class="fw-medium mx-2">Task Compiled:</span> <span>13.5k</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-4">
                                                <i class="icon-base ti tabler-layout-grid icon-lg"></i><span
                                                    class="fw-medium mx-2">Projects Compiled:</span> <span>146</span>
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <i class="icon-base ti tabler-users icon-lg"></i><span
                                                    class="fw-medium mx-2">Connections:</span> <span>897</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php if(isset($data->category_id)): ?>
                                <div class="card mb-6">
                                    <div class="card-body position-relative">
                                        <p
                                            class="card-text text-uppercase text-body-secondary small d-flex justify-content-between align-items-center">
                                            Parent Commissions
                                            <!-- Plus Button -->
                                            <a href="<?php echo e(route('admin.partner.commision.form', ['id' => $id])); ?>"
                                                class="btn btn-sm btn-primary" title="Add New">
                                                <i class="fa fa-plus"></i>
                                            </a>

                                        </p>

                                                <?php $__empty_1 = true; $__currentLoopData = $PartnerCommission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pcom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <div class="row">

                                                        <div class="col-3"><?php echo e($pcom->partner->name ?? '-'); ?></div>
                                                        <div class="col-3 text-danger"><?php echo e($pcom->from_amount); ?> - <?php echo e($pcom->to_amount); ?></div>
                                                        <div class="col-2 text-success"><?php echo e($pcom->deposit_percentage); ?>%</div>
                                                        <div class="col-2 text-warning"><?php echo e($pcom->withdrawal_percentage); ?>%</div>
                                                        <div class="col-2 d-flex align-items-center gap-2">
                                                            <!-- Edit Button -->
                                                            <a href="<?php echo e(route('admin.partner.commisionedit.form', ['id' => $pcom->id])); ?>">
                                                                <i class="fa fa-edit text-warning"></i>
                                                            </a>

                                                            <!-- Delete Form -->
                                                            <form action="<?php echo e(route('admin.partner.commission.delete', $pcom->id)); ?>"
                                                                method="POST"
                                                                class="delete-form"
                                                                data-id="<?php echo e($pcom->id); ?>">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="btn btn-sm btn-icon edit_button p-0 m-0">
                                                                    <i class="fa fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                    </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <div class="row">
                                                        <div class="col-12">No partner commissions found.</div>
                                                    </div>
                                                <?php endif; ?>
                                            <div class="col-4">

                                            </div>


                                    </div>
                                </div>

                                <div class="card mb-6">
                                    <div class="card-body position-relative">
                                        <p
                                            class="card-text text-uppercase text-body-secondary small d-flex justify-content-between align-items-center">
                                            Merchant Commissions
                                            <!-- Plus Button -->


                                        </p>

                                                <?php $__empty_1 = true; $__currentLoopData = $MCommissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pcom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php if($index>0): ?>
                                                <hr>
                                                <?php endif; ?>
                                                    <div class="row">

                                                        <div class="col-3"><?php echo e(implode(', ', json_decode($pcom->type, true))); ?></div>
                                                        <div class="col-3"><?php echo e(implode(', ', json_decode($pcom->gateway_id, true))); ?></div>
                                                        <div class="col-2 text-danger"><?php echo e($pcom->from_amount); ?> - <?php echo e($pcom->to_amount); ?></div>
                                                        <div class="col-2 text-success"><?php echo e($pcom->deposit_percentage); ?>%</div>
                                                        <div class="col-2 text-warning"><?php echo e($pcom->withdrawal_percentage); ?>%</div>


                                                    </div>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <div class="row">
                                                        <div class="col-12">No commissions found.</div>
                                                    </div>
                                                <?php endif; ?>
                                            <div class="col-4">

                                            </div>


                                    </div>
                                </div>
                                <?php endif; ?>

                                <!--/ Profile Overview -->
                            </div>
                            <div class="col-xl-8 col-lg-7 col-md-7">
                                <!-- Activity Timeline -->
                                <div class="card card-action mb-6">
                                    <div class="card-header align-items-center">

                                        <form action="<?php echo e(route('admin.apis.update', $data->id)); ?>" method="POST"
                                            enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <h5 class="card-action-title mb-0">
                                                <i
                                                    class="icon-base ti tabler-chart-bar-popular icon-lg me-4 mb-2"></i>Profile
                                                Editing Fields
                                            </h5>
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                'name', 'username', 'email', 'phone', 'website', 'api_endpoint_deposit',
                                                'api_endpoint_withdrawal',
                                                 'api_key',
                                                'min_deposit',
                                                'min_withdrawal',  'secret_key', 'redirect_url', 'timezone'
                                                ];
                                                ?>

                                                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6 mb-3">
                                                    <label><?php echo e(ucwords(str_replace('_', ' ', $field))); ?></label>
                                                    <input type="text" name="<?php echo e($field); ?>"
                                                        value="<?php echo e(old($field, $data->$field)); ?>" class="form-control">
                                                </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <div class="col-md-6 mb-3">
                                                    <label>Account Type</label>
                                                    <select name="acc_type" class="form-control">
                                                        <option value="Partner"
                                                            <?php echo e($data->acc_type == 'Partner' ? 'selected' : ''); ?>>Partner
                                                        </option>
                                                        <option value="Agent"
                                                            <?php echo e($data->acc_type == 'Agent' ? 'selected' : ''); ?>>Agent
                                                        </option>
                                                    </select>
                                                </div>



                                                <div class="col-md-6 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" <?php echo e($data->status == 1 ? 'selected' : ''); ?>>
                                                            Active</option>
                                                        <option value="0" <?php echo e($data->status == 0 ? 'selected' : ''); ?>>
                                                            Inactive</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Sign</label>
                                                    <select name="sign" class="form-control">
                                                        <option value="1" <?php echo e($data->sign == 1 ? 'selected' : ''); ?>>Yes
                                                        </option>
                                                        <option value="0" <?php echo e($data->sign == 0 ? 'selected' : ''); ?>>No
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Txn Verification</label>
                                                    <select name="txn_verification" class="form-control">
                                                        <option value="1"
                                                            <?php echo e($data->txn_verification == 1 ? 'selected' : ''); ?>>Enabled
                                                        </option>
                                                        <option value="0"
                                                            <?php echo e($data->txn_verification == 0 ? 'selected' : ''); ?>>
                                                            Disabled</option>
                                                    </select>
                                                </div>
                                                <?php if(isset($categories)): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label>Comission Category</label>
                                                    <select name="category_id" class="form-select">
                                                        <option value="">Please select category</option>
                                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>"
                                                            <?php echo e(isset($data) && $data->category_id == $category->id ? 'selected' : ''); ?>>
                                                            <?php echo e($category->title); ?>

                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <?php endif; ?>

                                                <div class="col-md-6 mb-3">
                                                    <label>Password </label>
                                                    <input type="text" class="form-control" name="password">

                                                </div>

                                                <div class="col-12 text-end mt-3">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>


                                    </div>

                                </div>

                            </div>
                        </div>
                        <!--/ User Profile Content -->
                    </div>
                    <!--/ Content -->




                </div>
                <!--/ Content wrapper -->
            </div>

            <!--/ Layout container -->
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // stop form

                const itemId = form.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `This will permanently delete item ID: ${itemId}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // proceed to submit
                    }
                });
            });
        });
        });


    function copyToClipboard(element) {
        const text = element.getAttribute('data-copy');
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied to clipboard!');
        }, function(err) {
            alert('Failed to copy text: ', err);
        });



    }
    </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/merchant/merchant-profile.blade.php ENDPATH**/ ?>