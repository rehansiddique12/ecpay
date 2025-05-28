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
            tr th{
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
                        <table class="categories-show-table table table-hover table-striped table-bordered settable table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Partner'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Parent1'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Parent2'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Parent3'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->username); ?></td>
                                    <td><?php echo e(optional($item->parent)->username ?? ' '); ?></td> <!-- Display Parent Username -->
                                    <td><?php echo e(optional(optional($item->parent)->parent)->username ?? ' '); ?></td> <!-- Parent2 Username -->
                                    <td><?php echo e(optional(optional(optional($item->parent)->parent)->parent)->username ?? ' '); ?></td> <!-- Parent3 Username -->
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

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/parant/parant.blade.php ENDPATH**/ ?>