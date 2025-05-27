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
        <form action="<?php echo e(route('admin.partner.balance.search')); ?>" method="get">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Partner</label>
                        <select id="select2Basic" name="partner" class="select2 form-select" data-allow-clear="true" data-placeholder="Select Partner">
                            <option></option>
                            <option value="">All</option>
                            <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partner->id); ?>" <?php if(@request()->partner == $partner->id): ?> selected <?php endif; ?>>
                                    <?php echo e($partner->website); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>


               <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select name="adjustment" class="form-select">
                            <option value=""><?php echo app('translator')->get('All'); ?></option>
                            <option value="4" <?php if(@request()->adjustment == '4'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Top-Up'); ?>
                            </option>
                            <option value="1" <?php if(@request()->adjustment == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Balance'); ?>
                            </option>
                            <option value="2" <?php if(@request()->adjustment == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Deposit'); ?>
                            </option>
                            <option value="3" <?php if(@request()->adjustment == '3'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Withdrawal'); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
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

                                    <th scope="col"><?php echo app('translator')->get('Name'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('User-Name'); ?></th>
                                    <th scope="col">Website</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Charges</th>
                                    <th scope="col"><?php echo app('translator')->get('Ajustment Type'); ?></th>
                                    <th scope="col" style="width: 500px;">Remarks</th>
                                    <th scope="col">Created At</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php if(isset($item->api)): ?>
                                        <tr>
                                            <td><?php echo e($item->api->name); ?></td>
                                            <td><?php echo e($item->api->username); ?></td>
                                            <td><?php echo e($item->api->website); ?></td>
                                            <td><?php echo e($item->amount); ?></td>
                                            <td><?php echo e($item->charges); ?></td>

                                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                                <?php if($item->adjustment == 2): ?>
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-warning success font-12"></i>
                                                        <?php echo app('translator')->get('Deposit'); ?></span>
                                                <?php elseif($item->adjustment == 3): ?>
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-danger success font-12"></i>
                                                        <?php echo app('translator')->get('Withdrawal'); ?></span>
                                                <?php elseif($item->adjustment == 4): ?>
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-primary success font-12"></i>
                                                        <?php echo app('translator')->get('Top-Up'); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-dark">
                                                        <i class="fa fa-circle text-success success font-12"></i>
                                                        <?php echo app('translator')->get('Balance'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Remarks">
                                                
                                                    <?php echo e($item->reason); ?>

                                                
                                            </td>
                                            <td><?php echo e($item->created_at); ?></td>
                                        </tr>
                                    <?php endif; ?>
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('js'); ?>
        <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
        <script>
            $(document).ready(function() {
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/partner_balance.blade.php ENDPATH**/ ?>