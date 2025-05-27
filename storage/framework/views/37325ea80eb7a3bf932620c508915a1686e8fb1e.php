<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('admin.payment.apiLogunclaimed.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">

                <div class="col-md-10">
                    <div class="form-group">
                        <input type="date" value="<?php echo e(@request()->date_time); ?>" class="form-control" name="date_time"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Sender'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Received Account'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Payable'); ?></th>
                            <th scope="col " class="text-center"><?php echo app('translator')->get('Status'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $funds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($fund->created_at, 'd M,Y H:i')); ?></td>
                                <td><?php echo e($fund->txn_id); ?></td>
                                <td class="font-weight-bold text-uppercase"><?php echo e($fund->sender); ?></td>
                                <td class="font-weight-bold text-uppercase"><?php echo e($fund->e_wallet_name); ?></td>
                                <td class="font-weight-bold text-uppercase"><?php echo e($fund->e_wallet_phone_number); ?></td>
                                <td class="font-weight-bold text-uppercase"><?php echo e(getAmount($fund->amount, 2)); ?></td>
                                <td class="font-weight-bold text-uppercase"><?php echo e(getAmount($fund->charge, 2)); ?></td>
                                <td class="font-weight-bold text-uppercase">
                                    <?php echo e(getAmount($fund->amount - $fund->charge, 2)); ?></td>
                                <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                    <?php if($fund->status == "Complete"): ?>
                                    <span class="badge bg-success">
                                        <i class="fa fa-circle text-white success font-12"></i> <?php echo app('translator')->get('Completed'); ?></span>
                                    <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fa fa-circle text-white success font-12"></i> <?php echo app('translator')->get('Pending'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-weight-bold text-uppercase"><?php echo e($fund->request_source); ?></td>
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
                <div class="mt-5">
                <?php echo e($funds->appends($_GET)->links('partials.pagination')); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
        <script>
            "use strict";
            $(document).ready(function() {
                $('select[name=status]').select2({
                    selectOnClose: true
                });

                $(document).on("click", '.edit_button', function(e) {
                    var id = $(this).data('id');
                    var feedback = $(this).data('feedback');

                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo =
                                `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                        } else {
                            var singleInfo =
                                `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] =
                            ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });
                    $('.withdraw-detail').html(list);

                    if (feedback == '') {
                        var $res = `<div class="form-group"><br>
                                <label class="font-weight-bold"><?php echo e(trans('Send You Feedback')); ?></label>
                                <textarea name="feedback" class="form-control" row="3" required><?php echo e(old('feedback')); ?></textarea>
                            </div>`
                    } else {
                        var $res = `<h5><?php echo e(trans('Feedback')); ?></h5>
                    <p>${feedback}</p>`
                    }

                    $('.get-feedback').html($res)
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/unclaimed.blade.php ENDPATH**/ ?>