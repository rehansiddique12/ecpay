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
    <form action="<?php echo e(route('admin.api.post.commissions')); ?>" method="get">
        <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
        <div class="row justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-select">
                        <option value=""><?php echo app('translator')->get('All'); ?></option>
                        <option value="1" <?php if(@request()->type == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Deposit'); ?></option>
                        <option value="2" <?php if(@request()->type == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Withdrawal'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Partner</label>
                    <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                        <option></option>
                        <option value="">All</option>
                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($partner->id); ?>" <?php if(@request()->partner == $partner->id): ?> selected <?php endif; ?>><?php echo e($partner->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                   <label>Parent</label>
                   <select name="partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Partner">
                        <option disabled selected></option>
                        <option value="" <?php if(request()->partner === ''): ?> selected <?php endif; ?>>All</option>
                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($partner->id); ?>" <?php if(request()->partner == $partner->id): echo 'selected'; endif; ?>><?php echo e($partner->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>


            <div class="col-md-4">
                  <div class="form-group">
                    <br>
                    <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo e(route('admin.commissions.export', request()->all())); ?>"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                        <i class="icon-base ti tabler-download me-1"></i> <?php echo app('translator')->get('Export'); ?>
                     </a>
                </div>
            </div>

        </div>
    </form>

</div>



<div class="row">
    <div class="col-md-12">
        <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="categories-show-table table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col"><?php echo app('translator')->get('Partner/Agent'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Type'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Charges'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Profit'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Parent'); ?></th>
                                <th scope="col"><?php echo app('translator')->get('Created At'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item->api->name); ?></td>
                                <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                    <?php if($item->type == 2): ?>
                                    <span class="badge bg-danger">
                                        <i class="fa fa-circle  text-white danger font-12"></i> <?php echo app('translator')->get('Withdrawal'); ?>
                                    </span>
                                    <?php elseif($item->type == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="fa fa-circle text-white success font-12"></i> <?php echo app('translator')->get('Deposit'); ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->amount); ?></td>
                                <td><?php echo e($item->charges); ?> (<?php echo e($item->charges_p); ?>%)</td>
                                <td><?php echo e($item->total_amount); ?></td>
                                <td><?php echo e($item->profit); ?> (<?php echo e($item->profit_p); ?>%)</td>
                                <td><?php echo e($item->fromapi->name); ?></td>
                                <td><?php echo e($item->created_at); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="100%">
                                    <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                             <!-- Add a total row if it's the last page -->
                            <?php if($isLastPage && $totalAmount): ?>
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                <td><strong><?php echo e($totalAmount); ?></strong></td>
                                <td><strong><?php echo e($totalChargesSum); ?></strong></td>
                                <td><strong><?php echo e($totalAAmountSum); ?></strong></td>
                                <td><strong><?php echo e($totalProfitSum); ?></strong></td>
                                <td colspan="4"></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination links -->
               <div class="card-footer">
                    <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

                </div>

            </div>
        </div>
    </div>

</div>

 <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>

    <script>
        "use strict";
        $(document).ready(function(e) {
           let $select = $('.select2').select2({
                // placeholder: "Select Partner",
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
        });
    </script>

    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/commission_report.blade.php ENDPATH**/ ?>