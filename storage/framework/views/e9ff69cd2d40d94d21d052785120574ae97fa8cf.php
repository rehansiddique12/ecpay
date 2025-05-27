<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <div class="card card-primary shadow">
                    <div class="card-body">
                        <?php if(adminAccessRoute(config('role.payment_gateway.access.add'))): ?>
                        <a href="<?php echo e(route('admin.deposit.manual.create')); ?>"
                           class="btn btn-primary btn-sm float-right mb-3"><i
                                class="fa fa-plus-circle"></i> <?php echo e(trans('Add New')); ?></a>
                        <?php endif; ?>

                        <table class="table ">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col"><?php echo app('translator')->get('Name'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Status'); ?></th>

                                <?php if(adminAccessRoute(config('role.payment_gateway.access.edit'))): ?>
                                    <th scope="col"><?php echo app('translator')->get('Action'); ?></th>
                                <?php endif; ?>
                            </tr>

                            </thead>
                            <tbody id="sortable">
                            <?php if(count($methods) > 0): ?>
                                <?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-code="<?php echo e($method->code); ?>">
                                        <td data-label="<?php echo app('translator')->get('Name'); ?>"><?php echo e($method->name); ?> </td>
                                        <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">

                                            <?php echo $method->status == 1 ? '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i>'.trans(' Active').'</span>' : '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i>'.trans(' Inactive').'</span>'; ?>

                                        </td>
                                        <?php if(adminAccessRoute(config('role.payment_gateway.access.edit'))): ?>
                                            <td data-label="<?php echo app('translator')->get('Action'); ?>">
                                                <a href="<?php echo e(route('admin.deposit.manual.edit', $method->id)); ?>"
                                                   class="btn btn-primary btn-circle"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   data-original-title="<?php echo app('translator')->get('Edit this Payment Methods info'); ?>">
                                                   <i class="icon-base ti tabler-pencil me-1"></i></a>
                                                <button type="button"
                                                        data-code="<?php echo e($method->code); ?>"
                                                        data-status="<?php echo e($method->status); ?>"
                                                        data-message="<?php echo e(($method->status == 0)?'Enable':'Disable'); ?>"
                                                        class="btn btn-sm btn-<?php echo e(($method->status == 0)?'success':'danger'); ?>   btn-circle disableBtn"
                                                        data-bs-toggle="modal" data-bs-target="#disableModal"><i
                                                        class="icon-base ti tabler-<?php echo e(($method->status == 0)?'check':'ban'); ?>"></i>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center text-danger" colspan="8">
                                        <?php echo app('translator')->get('No Data Found'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>





                <div class="modal modal-top fade" id="disableModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Confirmation'); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                <form action="<?php echo e(route('admin.payment.methods.deactivate')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p class="font-weight-bold"><?php echo app('translator')->get('Are you sure to'); ?> <span
                                class="messageShow"></span> <?php echo e(trans('this?')); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn waves-effect waves-light btn-dark"
                                data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                        <button type="submit" class="btn waves-effect waves-light btn-primary messageShow"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>




<?php $__env->startPush('js'); ?>
    <script>
        "use strict";
        $('.disableBtn').on('click', function () {
            var status = $(this).data('status');
            $('.messageShow').text($(this).data('message'));
            var modal = $('#disableModal');
            modal.find('input[name=code]').val($(this).data('code'));
        });
    </script>
<?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment_methods/manual/index.blade.php ENDPATH**/ ?>