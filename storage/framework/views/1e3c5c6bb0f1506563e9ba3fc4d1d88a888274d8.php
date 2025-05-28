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
        /* Fix for Select2 inside Bootstrap modal */
        .select2-container {
            z-index: 9999 !important;
        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .modal {
            overflow-y: auto;
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

            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#groupModal" id="newCategoryButton">
                        Add Account Group
                    </button>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Group Name</th>
                                <th scope="col">Accounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($group->group_name); ?></td>
                                <td><?php echo e($group->pairs); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>


        <!-- Group Modal -->
        <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupModalLabel">Add Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="<?php echo e(route('admin.accounts.addpairs')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label for="groupName" class="form-label">Group Name</label>
                                <input type="text" name="group_name" class="form-control" id="groupName"
                                    placeholder="Enter group name">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label" >Select Pairs</label>
                                <select id="" name="pairs[]" class="form-select select2" multiple z-index="9999">
                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($accounts->account_no); ?>"> <?php echo e($accounts->account_no); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Group</button>
                            </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>

    <?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/DataTables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
                   let $select = $('.select2').select2({
                        dropdownParent: $('#groupModal'), // Ensures dropdown appears inside modal
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
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/accounts/groups.blade.php ENDPATH**/ ?>