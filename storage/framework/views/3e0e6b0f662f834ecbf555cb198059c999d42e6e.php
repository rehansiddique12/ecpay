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
 table td div code {
        color: #ccc;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .table td, .table th {
        vertical-align: top;
    }
</style>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <table class=" table table-sm  table-hover table-striped table-bordered text-white" style="table-layout: fixed; width: 100%;">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 100px;"><?php echo app('translator')->get('No.'); ?></th>
                            <th style="width: 200px;"><?php echo app('translator')->get('Request URL'); ?></th>
                            <th style="width: 100px;"><?php echo app('translator')->get('Request Method'); ?></th>
                            <th style="width: 600px;"><?php echo app('translator')->get('Request Payload'); ?></th>
                            <th style="width: 400px;"><?php echo app('translator')->get('Request Header'); ?></th>
                            <th style="width: 100px;"><?php echo app('translator')->get('Response Code'); ?></th>
                            <th style="width: 300px;"><?php echo app('translator')->get('Response Payload'); ?></th>
                            <th style="width: 200px;"><?php echo app('translator')->get('Response Header'); ?></th>
                            <th style="width: 160px;"><?php echo app('translator')->get('Created At'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="<?php echo app('translator')->get('No.'); ?>"><?php echo e($transaction->id); ?></td>

                                <td data-label="<?php echo app('translator')->get('Request URL'); ?>" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($transaction->request_url); ?>">
                                    <?php echo e($transaction->request_url); ?>

                                </td>

                                <td data-label="<?php echo app('translator')->get('Request Method'); ?>"><?php echo e($transaction->request_method); ?></td>

                                <td data-label="<?php echo app('translator')->get('Request Payload'); ?>">
                                    <div style="max-height: 200px; overflow: auto; background-color: #1e1e2f; padding: 5px; border-radius: 5px; font-size: 16px;">
                                        <code><?php echo e($transaction->request_payload); ?></code>
                                    </div>
                                </td>

                                <td data-label="<?php echo app('translator')->get('Request Header'); ?>">
                                        <?php echo e($transaction->request_headers); ?>

                                </td>

                                <td data-label="<?php echo app('translator')->get('Response Code'); ?>"><?php echo e($transaction->response_code); ?></td>

                                <td data-label="<?php echo app('translator')->get('Response Payload'); ?>">
                                        <?php echo e($transaction->response_payload); ?>

                                </td>

                                <td data-label="<?php echo app('translator')->get('Response Header'); ?>">
                                        <?php echo e($transaction->response_headers); ?>

                                </td>

                                <td data-label="<?php echo app('translator')->get('Created At'); ?>"><?php echo e($transaction->created_at); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center text-danger" colspan="9"><?php echo app('translator')->get('No Record Found'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php echo e($data->appends($_GET)->links('partials.pagination')); ?>

                </div>
        </div>
    </div>

    <div class="pagination float-right mr-4">
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/apiLogs.blade.php ENDPATH**/ ?>