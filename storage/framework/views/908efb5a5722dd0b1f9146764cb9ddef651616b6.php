<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.partner.balance.search')); ?>" method="get">

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


                <div class="col-md-3">
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select name="adjustment" class="form-control">
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

                <div class="col-md-1">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>
                
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
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
                                    <th scope="col">Remarks</th>
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
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-warning success font-12"></i>
                                                        <?php echo app('translator')->get('Deposit'); ?></span>
                                                <?php elseif($item->adjustment == 3): ?>
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-danger success font-12"></i>
                                                        <?php echo app('translator')->get('Withdrawal'); ?></span>
                                                <?php elseif($item->adjustment == 4): ?>
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-primary success font-12"></i>
                                                        <?php echo app('translator')->get('Top-Up'); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-light">
                                                        <i class="fa fa-circle text-success success font-12"></i>
                                                        <?php echo app('translator')->get('Balance'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($item->reason); ?></td>
                                            <td><?php echo e(convertToUserTimezone($item->created_at)); ?></td>
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
                </div>
            </div>
        </div>

    </div>

    <?php $__env->startPush('js'); ?>
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
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/partner_balance.blade.php ENDPATH**/ ?>