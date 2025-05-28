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
        <script src="<?php echo e(asset('public/assets/css/select2.min.css')); ?>"></script>
        <style>
            tr th {
                color: white !important
            }
        </style>
    <?php $__env->stopPush(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                    <div class="table-responsive">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#newModal">
                                Add User
                            </button>
                        </div>
                        <table
                            class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark bg-primary">
                                <tr>
                                    <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Category Name'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($item['id']); ?></td>
                                        <td><?php echo e($item['name'] ?? ''); ?></td>
                                        <td>
                                            <label class="switch" style="pointer-events: none;">
                                                <input type="checkbox"
                                                    class="switch-input <?php echo e($item['status'] == 1 ? 'is-valid' : 'is-invalid'); ?>"
                                                    <?php echo e($item['status'] == 1 ? 'checked' : ''); ?>>

                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"></span>
                                                    <span class="switch-off"></span>
                                                </span>

                                                <span class="switch-label">
                                                    <?php echo e($item['status'] == 1 ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    
                                                    <form action="<?php echo e(route('admin.groups.delete', $item['id'])); ?>"
                                                        method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon edit_button"><i
                                                                class="icon-base ti tabler-trash me-1"></i>
                                                            Delete</button>
                                                    </form>
                                                    
                                                    
                                                    <button type="button" class="btn btn-sm btn-icon edit_button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal<?php echo e($item['id']); ?>">
                                                        <i class="icon-base ti tabler-user me-1"></i> Edit
                                                    </button><br>
                                                    
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- Edit Modal -->
        <div id="editModal<?php echo e($item['id']); ?>" class="modal modal-top fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary modal-colored-header">
                        <h5 class="modal-title" style="color: white" id="modalTopTitle"><?php echo app('translator')->get('Edit Record'); ?> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?php echo e(route('admin.type.update', $item['id'])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body">
                            <div class="row justify-content-between align-items-center">
                                <!-- Input fields for editing the record -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="pr-3">Category Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="<?php echo e($item['name']); ?>" required />
                                    </div>
                                    <div class="row mt-3 justify-content-between">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="pr-3">Status</label>
                                                    <select class="form-control" name="status" required>
                                                        <option value="1">Active</option>
                                                        <option value="0">InActive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Update'); ?></button>
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                    aria-label="Close"><?php echo app('translator')->get('Close'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal modal-top fade" id="newModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" style="color: #7367f0"" id="modalTopTitle"><?php echo app('translator')->get('Add New'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.type.add')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Category Name</label>
                                    <input type="text" class="form-control" name="name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">InActive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                            aria-label="Close"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
        <script src="<?php echo e(asset('public/assets/js/select2.min.js')); ?>"></script>
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
                $('select').select2({
                    selectOnClose: true
                });
            });
        </script>
        <script>
            function setBalanceItem(itemId) {
                // Find the input field in the modal
                var balanceInput = document.getElementById("balanceInput");

                // Set the value of the input field to the item id
                balanceInput.value = itemId;
            }

            function setParentID(parentidd, acc_idd) {
                // Find the input field in the modal
                var parentidInput = document.getElementById("parentid");
                var acc_idInput = document.getElementById("acc_id");

                // Set the value of the input field to the item id
                parentidInput.value = parentidd;
                acc_idInput.value = acc_idd;
            }
        </script>

        <script>
            $(document).ready(function() {
                // Attach change event listener to the select element
                $('#adjustment').change(function() {
                    // Get the selected value
                    var selectedValue = $(this).val();

                    // Check if selected value is 1 or 2
                    if (selectedValue == 1 || selectedValue == 2) {
                        // If selected value is 1 or 2, check amount_type1 and uncheck amount_type2
                        $('#amount_type1').prop('checked', true);
                        $('#amount_type2').prop('checked', false);
                    } else if (selectedValue == 3) {
                        // If selected value is 3, check amount_type2 and uncheck amount_type1
                        $('#amount_type2').prop('checked', true);
                        $('#amount_type1').prop('checked', false);
                    }
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/group/paymenttype.blade.php ENDPATH**/ ?>