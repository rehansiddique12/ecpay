<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="row">
        <?php if(adminAccessRoute(config('role.ewallet_transfer_balance.access.add'))): ?>
        <div class="col-md-12">
            <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
                <h3 style="color: #7367f0">Add Transfer Record</h3>
                <form action="<?php echo e(route('admin.transfer.balance.add')); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Select Category</label>
                                <select class="form-select" name="category" id="category" required>
                                    <option value="E-wallet to E-wallet">E-wallet to E-wallet</option>
                                    <option value="Bank to E-wallet">Bank to E-wallet</option>
                                    <option value="E-wallet to Bank">E-wallet to Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" id="fromtransfer1">
                                <label class="pr-3">Transfer From</label>
                                <select class="form-select select2" name="transfer_from1" data-allow-clear="true" data-placeholder="Select From Account" required>
                                    <option></option>
                                    <?php $__currentLoopData = $e_wallet_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e_wallet_account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($e_wallet_account->id); ?>"><?php echo e($e_wallet_account->account_no." (".$e_wallet_account->e_wallet_name.") "); ?></option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group" id="fromtransfer2" style="display:none">
                                <label class="pr-3">Transfer From</label>
                                <input type="text" class="form-control" name="transfer_from2" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="totransfer1">
                                <label class="pr-3">Transfer To</label>
                                <select class="form-select select2" name="transfer_to1" data-allow-clear="true" data-placeholder="Select To Account">
                                    <option></option>
                                    <?php $__currentLoopData = $e_wallet_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e_wallet_account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($e_wallet_account->id); ?>"><?php echo e($e_wallet_account->account_no." (".$e_wallet_account->e_wallet_name.") "); ?></option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group" id="totransfer2" style="display:none">
                                <label class="pr-3">Transfer To</label>
                                <input type="text" class="form-control" name="transfer_to2" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Transection No.</label>
                                <input type="text" class="form-control" name="txn_id" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Amount</label>
                                <input type="number" class="form-control" name="amount" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Charges</label>
                                <input type="number" class="form-control" name="charges" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Commission</label>
                                <input type="number" class="form-control" name="comission" required />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="pr-3">Transfer Date Time</label>
                                <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d H:i:s');?>" name="transaction_date_time" id="datepicker" />
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pr-3">Reciept</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <button id="submit-btn" type="submit" class="btn waves-effect waves-light btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if(adminAccessRoute(config('role.ewallet_transfer_balance.access.view'))): ?>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('admin.transfer.balance')); ?>" method="get">
            <div class="row justify-content-between align-items-center">

                <div class="col-md-10">
                    <div class="form-group">
                        <input type="date" class="form-control" name="from_date" value="<?php echo e($from_date); ?>" id="datepicker" />
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary">
                            <i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <br>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <h4 style="color: #7367f0">Transfer Logs</h4>
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered  table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('Category'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('E-Wallet'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('From Account'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('To Account'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Charges'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Commission'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Txn Id'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Date-Time'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Receipt'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Created At'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Updated At'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $e_wallet_transections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->category); ?></td>
                            <td><?php echo e($item->e_wallet); ?></td>
                            <td><?php echo e($item->from_account_no); ?></td>
                            <td><?php echo e($item->to_account_no); ?></td>
                            <td><?php echo e($item->amount); ?></td>
                            <td><?php echo e($item->charges); ?></td>
                            <td><?php echo e($item->comission); ?></td>
                            <td><?php echo e($item->txn_id); ?></td>
                            <td><?php echo e($item->transaction_date_time); ?></td>
                            <td>
                                <?php if(!empty($item->image)): ?>
                                <a data-fancybox="images" href="<?php echo e(getFile(config('location.receipts.path').$item->image)); ?>">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->created_at); ?></td>
                            <td><?php echo e($item->updated_at); ?></td>

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
                <?php echo e($e_wallet_transections->appends($_GET)->links('partials.pagination')); ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
 <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('js'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
    $(document).ready(function () {
        $('#category').change(function () {
            var selectedCategory = $(this).val();

            if (selectedCategory === 'Bank to E-wallet') {
                // Show fromtransfer2 and hide fromtransfer1
                $('#fromtransfer2').show();
                $('#fromtransfer1').hide();

                // Show totransfer1 and hide totransfer2
                $('#totransfer1').show();
                $('#totransfer2').hide();
            } else if (selectedCategory === 'E-wallet to Bank') {
                // Show fromtransfer1 and hide fromtransfer2
                $('#fromtransfer1').show();
                $('#fromtransfer2').hide();

                // Show totransfer2 and hide totransfer1
                $('#totransfer2').show();
                $('#totransfer1').hide();
            } else if (selectedCategory === 'E-wallet to E-wallet') {
                // Show fromtransfer1 and hide fromtransfer2
                $('#fromtransfer1').show();
                $('#fromtransfer2').hide();

                // Show totransfer1 and hide totransfer2
                $('#totransfer1').show();
                $('#totransfer2').hide();
            }
        });

        $('form').on('submit', function () {
            const $btn = $(this).find('button[type="submit"]');
            // Disable the button
            $btn.prop('disabled', true);
            // Optional: Change button text to show loading spinner
            $btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Submitting...');
            return true; // allow form to submit
        });
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/ewallet_transfer.blade.php ENDPATH**/ ?>