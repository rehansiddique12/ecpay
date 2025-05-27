<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="mb-3 text-right">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                    id="newCategoryButton">
                    Add Commission Category
                </button>
            </div>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th><?php echo app('translator')->get('Name'); ?></th>
                            <th><?php echo app('translator')->get('Created At'); ?></th>
                            
                            <th><?php echo app('translator')->get('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->title); ?></td>
                            <td><?php echo e(dateTime($item->created_at, 'd M, Y H:i')); ?></td>
                            
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal" data-id="<?php echo e($item->id); ?>"
                                    data-name="<?php echo e($item->title); ?>" data-status="<?php echo e($item->status); ?>">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteCategoryModal" data-id="<?php echo e($item->id); ?>">
                                    Delete
                                </button>

                                <!-- Commission Button -->
                                <a href="<?php echo e(route('admin.apis.commission', ['id' => $item->id])); ?>" class="btn btn-primary btn-sm">
    Commission
</a>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4"><?php echo app('translator')->get('No Data Found'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="<?php echo e(route('admin.commission.categories.store')); ?>" method="POST" class="modal-content">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo app('translator')->get('Add New'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="<?php echo e(route('admin.commission.categories.update')); ?>" method="POST" class="modal-content">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="id" id="editCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo app('translator')->get('Edit Category'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required />
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Update'); ?></button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="<?php echo e(route('admin.commission.categories.destroy')); ?>" method="POST" class="modal-content">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <input type="hidden" name="id" id="deleteCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo app('translator')->get('Delete Category'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo app('translator')->get('Are you sure you want to delete this category?'); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><?php echo app('translator')->get('Cancel'); ?></button>
                    <button type="submit" class="btn btn-danger"><?php echo app('translator')->get('Delete'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script>
    $(document).ready(function() {
        $('#editCategoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            // var status = button.data('status').toString();
            $('#editCategoryId').val(id);
            $('#editCategoryName').val(name);
            // $('#editCategoryStatus').val(status == 1 ? '1' : '0');
        });

        $('#deleteCategoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            $('#deleteCategoryId').val(button.data('id'));
        });
    });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\ecpay\resources\views/commission_category/index.blade.php ENDPATH**/ ?>