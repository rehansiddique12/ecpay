<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <h1 class="text-center">
        <span class="badge badge-primary">Settlementable Amount: <b><?php echo e($settlementable_amount); ?> TK</b></span>
    </h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.settlements.search')); ?>" method="get">
            <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
            <div class="row justify-content-between align-items-center">

                <div class="col-md-5">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-2"></div>

                <!--<div class="col-md-4">-->
                <!--    <div class="form-group">-->
                <!--        <label>User Account No</label>-->
                <!--        <input type="text" class="form-control" value="<?php echo e(@request()->account_no); ?>" name="account_no"/>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="col-md-5">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-control">
                            <option value="">All</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->source_name); ?>"
                                    <?php if(@request()->gateway == $gateway->source_name): ?> selected <?php endif; ?>><?php echo e($gateway->source_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="all"><?php echo app('translator')->get('All'); ?></option>
                            <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Approved'); ?>
                            </option>
                            <option value="0" <?php if(@request()->status == '0'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending'); ?>
                            </option>
                            <option value="2" <?php if(@request()->status == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Rejected'); ?>
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
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary mr-2 mb-3 " data-target="#newModal"
                        data-toggle="modal">
                        <span><?php echo app('translator')->get('Add New'); ?></span>
                    </a>
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>

                                    <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Source Name'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Account No.'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Charges'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                    <th scope="col">Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($item->source); ?></td>
                                        <td><?php echo e($item->source_name); ?></td>
                                        <td><?php echo e($item->account_no); ?></td>
                                        <td><?php echo e($item->amount); ?></td>
                                        <td><?php echo e($item->charges); ?></td>
                                        <td><?php echo e($item->net_amount); ?></td>
                                        <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                            <?php if($item->status == 2): ?>
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-danger danger font-12"></i>
                                                    <?php echo app('translator')->get('Rejected'); ?> </span>
                                            <?php elseif($item->status == 1): ?>
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-success success font-12"></i>
                                                    <?php echo app('translator')->get('Approved'); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-light">
                                                    <i class="fa fa-circle text-warning success font-12"></i>
                                                    <?php echo app('translator')->get('Pending'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(convertToUserTimezone($item->created_at)); ?></td>
                                        <!--<td data-label="<?php echo app('translator')->get('Action'); ?>">-->
                                        <!--    <div class="dropdown show ">-->
                                        <!--        <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown"-->
                                        <!--           aria-haspopup="true" aria-expanded="false">-->
                                        <!--            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>-->
                                        <!--        </a>-->
                                        <!--        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">-->
                                        <!--            <form action="<?php echo e(route('admin.apis.delete', $item['id'])); ?>" method="POST">-->
                                        <!--                <?php echo csrf_field(); ?>-->
                                        <!--                <?php echo method_field('DELETE'); ?>-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-trash"></i> Delete</button>-->
                                        <!--            </form>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#editModal<?php echo e($item['id']); ?>">-->
                                        <!--                <i class="fa fa-edit"></i> Edit-->
                                        <!--            </button><br>-->
                                        <!--            <button type="button" class="btn btn-sm btn-icon edit_button" data-toggle="modal" data-target="#newModalb" onclick="setBalanceItem(<?php echo e($item['id']); ?>)">-->
                                        <!--                <i class="fa fa-money-bill"></i> Add Balance-->
                                        <!--            </button>-->
                                        <!--            <form action="<?php echo e(route('admin.apis.reset', $item['id'])); ?>" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-key"></i> Reset QR Code</button>-->
                                        <!--            </form>-->
                                        <!--            <form action="<?php echo e(route('admin.apis.commission', $item['id'])); ?>" method="GET">-->
                                        <!--                <button type="submit" class="btn btn-sm btn-icon edit_button"><i class="fa fa-calculator"></i> Commission %</button>-->
                                        <!--            </form>-->
                                        <!--        </div>-->
                                        <!--    </div>-->
                                        <!--</td>-->







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
                </div>
            </div>
        </div>

    </div>





    
    <div id="newModal" class="modal fade show" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title"><?php echo app('translator')->get('Add New'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('partner.settlements.add')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source</label>
                                    <select class="form-control" name="source" required>
                                        <option value="Bank">Bank</option>
                                        <option value="EWallet">EWallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Source Name</label>
                                    <input type="text" class="form-control" name="source_name" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Account No.</label>
                                    <input type="text" class="form-control" name="account_no" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pr-3">Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="amount"
                                        required />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('Save'); ?></button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/settlement.blade.php ENDPATH**/ ?>