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
        .fa-ellipsis-v:before {
            content: "\f142";
        }

        .custom-checkbox input[type="checkbox"] {
            filter: invert(100%) brightness(1.7);
            width: 20px;
            height: 20px;

        }

    .custom-checkbox {
        transform: scale(1.5); /* Make checkbox bigger */
        margin: auto;
        display: block;
    }
   .custom-checkbox-lg {
        transform: scale(1.5); /* Makes checkbox bigger */
        margin-right: 10px;
        vertical-align: middle;
    }

    .custom-label-lg {
        font-size: 1.4rem; /* Increases label text size */
        font-weight: 500;
    }
    </style>
    <?php
    $currentRoute = Route::currentRouteName();
    ?>
    <div class="page-header m-0 m-md-4 my-4 m-md-0 p-5">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <div class="row">
                    <div class="col-md-5 gap-6 d-flex justify-content-between">
                        <?php if(adminAccessRoute(config('role.manage_staff.access.view'))): ?>
                        <div>
                            <button class="btn <?php echo e($currentRoute == 'admin.users' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.users')); ?>" class="menu-link">
                                    <div data-i18n="Users">Users</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.manage_location.access.view'))): ?>
                        <div>
                            <button class="btn <?php echo e($currentRoute == 'admin.location' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.location')); ?>" class="menu-link">
                                    <div data-i18n="Location">Location</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_and_permission.access.add'))): ?>
                        <div>
                            <button
                                class="btn <?php echo e($currentRoute == 'admin.roles_and_permission' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.roles_and_permission')); ?>" class="menu-link">
                                    <div data-i18n="Roles and Permission">Roles and Permission</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.roles_category.access.view'))): ?>
                        <div>
                            <button class="btn <?php echo e($currentRoute == 'admin.rolescategory' ? 'btn-primary' : ''); ?>">
                                <a href="<?php echo e(route('admin.rolescategory')); ?>" class="menu-link">
                                    <div data-i18n="Roles Category">Roles Category</div>
                                </a>
                            </button>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="assign-permissions-content">
                    <?php
                    // You can pass a selected role from controller or set a default here

                    $selectedRoleId = old('role_select') ?? ($selectedRoleId ?? null);
                    // $storedPermissions = $selectedRole->admin_access ?? [];
                    // dd($selectedRoleId);
                    ?>
                    <div class="row align-items-center mb-3 mt-4">
                        <h4 for="role_select" class="col-md-2">Role</h4>

                        <div class="col-md-4">

                            <select name="role_select" id="role_select" class="form-select">

                                <option value="">-- Select Role --</option>

                                <?php $__currentLoopData = $roles_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($role->id); ?>" <?php echo e($selectedRoleId==$role->id ? 'selected' : ''); ?>>
                                    <?php echo e($role->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php if($selectedRoleId > 0): ?>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between text-center">
                                <h5 class="card-title text-center"><?php echo e(trans('Accessibility')); ?></h5>
                            </div>
                            <form role="form" method="POST" class="actionRoute"
                                action="<?php echo e(route('admin.update_role_permissions' , $selectedRoleId)); ?>"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="card-body select-all-access">
                                    <div class="form-group">
                                        
                                            <input type="checkbox" class="selectAll custom-checkbox-lg mb-3" name="accessAll">
                                            <span class="custom-label-lg"><?php echo e(trans('Select
                                            All')); ?></span>
                                            
                                    </div>

                                    <table class=" table table-hover table-striped table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th><?php echo app('translator')->get('Permissions'); ?></th>
                                                <th class="text-center"><?php echo app('translator')->get('View'); ?></th>
                                                <th class="text-center"><?php echo app('translator')->get('Add'); ?></th>
                                                <th class="text-center"><?php echo app('translator')->get('Edit'); ?></th>
                                                <th class="text-center"><?php echo app('translator')->get('Delete'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="permissionsTableBody">
                                            <?php $__currentLoopData = config('role'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td data-label="Permissions" class="text-left"><?php echo e($value['label']); ?></td>
                                                <td data-label="View">
                                                    <?php if(!empty($value['access']['view'])): ?>
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="<?php echo e(join(',',$value['access']['view'])); ?>" name="access[]"
                                                        <?php if(in_array_any( $value['access']['view'],
                                                        $storedPermissions??[] )): ?> checked <?php endif; ?> />
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Add">
                                                    <?php if(!empty($value['access']['add'])): ?>
                                                    <input type="checkbox" class="custom-checkbox" value="<?php echo e(join(',',$value['access']['add'])); ?>"
                                                        name="access[]" <?php if(in_array_any($value['access']['add'],
                                                        $storedPermissions??[] )): ?> checked <?php endif; ?> />
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Edit">
                                                    <?php if(!empty($value['access']['edit'])): ?>
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="<?php echo e(join(',',$value['access']['edit'])); ?>" name="access[]"
                                                        <?php if(in_array_any($value['access']['edit'],
                                                        $storedPermissions??[])): ?> checked <?php endif; ?> />
                                                    <?php endif; ?>
                                                </td>

                                                <td data-label="Delete">
                                                    <?php if(!empty($value['access']['delete'])): ?>
                                                    <input type="checkbox" class="custom-checkbox"
                                                        value="<?php echo e(join(',',$value['access']['delete'])); ?>" name="access[]"
                                                        <?php if(in_array_any( $value['access']['delete'],
                                                        $storedPermissions??[])): ?> checked <?php endif; ?> />
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        </tbody>
                                    </table>

                                </div>
                                <!-- Action Buttons -->
                                <div class="row mb-4">
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary me-2" id="updatePermissions">
                                            <i class="fas fa-save"></i> <?php echo app('translator')->get('Update Permissions'); ?>
                                        </button>
                                        
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php $__env->startPush('js'); ?>
            <script>
                $(document).ready(function() {
                    // Load permissions when role changes
                    $('#role_select').change(function() {
                        const roleId = $(this).val();
                        if (roleId) {
                            window.location.href = `?role_select=${roleId}`; // Reload page with selected role
                        }
                    });

                    // Select All Checkbox
                    $('.selectAll').click(function() {
                        $('input[name="access[]"]').prop('checked', $(this).prop('checked'));
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/users/rolespermission.blade.php ENDPATH**/ ?>