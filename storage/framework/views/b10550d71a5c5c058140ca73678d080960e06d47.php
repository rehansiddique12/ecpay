<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.payment.report.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="<?php echo e($from_date); ?>" name="from_date"
                             />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="text" class="form-control datetimepicker" autocomplete="off" value="<?php echo e($to_date); ?>" name="to_date"
                            />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>User Account No</label>
                        <input type="text" class="form-control" autocomplete="off" value="<?php echo e(@request()->account_no); ?>" name="account_no"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Transection No</label>
                        <input type="text" name="partner_transection_id" value="<?php echo e(@request()->partner_transection_id); ?>"
                            class="form-control" placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>


                <div class="col-md-5">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">All</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->name); ?>" <?php if(@request()->gateway == $gateway->name): ?> selected <?php endif; ?>>
                                    <?php echo e($gateway->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <label>User</label>-->

                <!--    </div>-->
                <!--</div>-->
                <input type="text" name="name" hidden value="<?php echo e(@request()->name); ?>" class="form-control "
                    placeholder="<?php echo app('translator')->get('Type Here'); ?>">

                <div class="col-md-5 mt-4">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="All" <?php if(@request()->status == 'All'): ?> selected <?php endif; ?>><?php echo app('translator')->get('All Payment'); ?>
                            </option>
                            <option value="Complete" <?php if(@request()->status == 'Complete'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Complete Payment'); ?>
                            </option>
                            <option value="Pending" <?php if(@request()->status == 'Pending'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending
                                                                                                                                                                            Payment'); ?>
                            </option>
                            <option value="Reject" <?php if(@request()->status == 'Reject'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Cancel
                                                                                                                                                                            Payment'); ?>
                            </option>
                        </select>
                        
                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                        <button type="submit" name="export" value="export"
                            class="btn btn-success mt-2"><i class="icon-base ti tabler-download me-1"></i>
                            <?php echo app('translator')->get('Export Data'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add these lines to your HTML header section -->
    <link rel="stylesheet" href="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css')); ?>">
    <script src="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js')); ?>"></script>



    <div class="row">
        <div class="col-2"></div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium"><?php echo e($fund_count); ?></h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Transactions'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fas fa-wallet"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow border-right">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium"><?php echo e($fund_sum); ?></h2>
                            </div>
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Deposit Amount'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="fa fa-hand-holding-usd"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            








            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
        
                    <div class="table-responsive">
                        <table class="categories-show-table table table-hover table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Date Time'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Partner Trx No'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Partner Txn Input'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Type'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Code'); ?></th>
                                    <th scope="col">User Account</th>
                                    <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Final Amount'); ?></th>
                                    <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                                    
                                    <th scope="col">Completed At</th>
                                    <th scope="col"><?php echo app('translator')->get('Receipt'); ?></th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $funds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td data-label="<?php echo app('translator')->get('ID'); ?>"> <?php echo e($fund->id); ?> </td>
                                        <td data-label="<?php echo app('translator')->get('Date_Time'); ?>"> <?php echo e(dateTime($fund->created_at, 'd M,Y H:i')); ?>

                                        </td>
                                        <td data-label="<?php echo app('translator')->get('Trx Number'); ?>" class="font-weight-bold text-uppercase">
                                            <?php echo e($fund->transaction); ?><br>
                                            <span class="text text-success"><?php echo e($fund->txn_id); ?></span>
        
                                        </td>
                                        <td><?php echo e(!empty($fund->partner_transection_id) ? $fund->partner_transection_id : ''); ?>

                                            <br>
                                            <?php echo e(!empty($fund->member_id) ? $fund->member_id : ''); ?>

                                        </td>
        
                                        <td>
                                            <?php echo e(!empty($fund->txn_record) ? $fund->txn_record->txn_no : ''); ?>

                                        </td>
        
                                        
                                        <td data-label="<?php echo app('translator')->get('Type'); ?>"><?php echo e($fund->gateway?->category?->name ?? 'N/A'); ?></td>
                                        <td data-label="<?php echo app('translator')->get('Code'); ?>"><?php echo e(optional($fund->gateway)->name); ?></td>
                                        <td class="font-weight-bold"><?php echo e($fund->sender); ?></td>
                                        <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold">
                                            <?php echo e(getAmount($fund->amount)); ?>

                                            <?php echo e($fund->gateway?->currency); ?></td>
                                        <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success">
                                            <?php echo e(getAmount($fund->charge)); ?>

                                            <?php echo e($fund->gateway?->currency); ?></td>
                                        <td data-label="<?php echo app('translator')->get('Payable'); ?>" class="font-weight-bold">
                                            <?php echo e(getAmount($fund->amount) - getAmount($fund->charge)); ?>

                                            <?php echo e($fund->gateway?->currency); ?>

                                        </td>
        
                                        <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                            <?php if($fund->status == 'Pending'): ?>
                                                <?php
                                                    // Get the time difference between now and the created_at timestamp
                                                    $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                                    $currentTime = \Carbon\Carbon::now();
                                                    $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                                ?>
        
                                                <?php if($diffInMinutes > 10 && @request()->status != 'Pending'): ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fa fa-circle text-white warning font-12"></i>
                                                        <?php echo app('translator')->get('Member did not complete'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">
                                                        <i class="fa fa-circle text-white font-12"></i>
                                                        <?php echo app('translator')->get('Pending'); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <br>
                                                <span class="text text-primary"><?php echo e($fund->e_wallet_phone_number); ?></span>
                                            <?php elseif($fund->status == 'Complete'): ?>
                                                <?php
                                                    // Check if the fund has a payment and if completed_source is set
                                                    if ($fund->completed_source != 'AdminPanel') {
                                                        // Dynamically assign the class based on completed_source
                                                        // if ($fund->payment->completed_source != "AdminPanel") {
                                                        $classColor = 'bg-success';
                                                        // } else {
                                                        // $classColor = "text-purple purple ";
                                                        // }
                                                    } else {
                                                        $classColor = 'bg-primary';
                                                    }
                                                ?>
        
        
                                                <span class="badge <?php echo e($classColor); ?>"><i
                                                        class="fa fa-circle text-white font-12"></i>
                                                    <?php echo app('translator')->get('Completed'); ?></span>
                                                <br>
                                                <span
                                                    class="<?php echo e($classColor); ?>"><?php echo e($fund->e_wallet_phone_number); ?></span>
                                            <?php elseif($fund->status == 'Reject'): ?>
                                                <span class="badge bg-danger"><i
                                                        class="fa fa-circle text-white danger font-12"></i>
                                                    <?php echo app('translator')->get('Rejected'); ?></span>
                                                <br>
                                                <span class="text text-danger"> <?php echo e($fund->e_wallet_phone_number); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td><?php echo e($fund->created_at); ?></td>
                                        <td>
                                            <?php if(!empty($fund->receipt_image)): ?>
                                                <a data-fancybox="images"
                                                    href="<?php echo e(getFile(config('location.receipts.path') . $fund->receipt_image)); ?>">
                                                    <h2><i class="fa fa-file"></i></h2>
                                                </a>
                                            <?php endif; ?>
                                        </td>
        
                                        
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










        </div>
    </div>

    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel"><?php echo app('translator')->get('Deposit Information'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                        <?php if(Request::routeIs('partner.payment.pending')): ?>
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                value="1"><?php echo app('translator')->get('Approve'); ?></button>
                            <button type="submit" class="btn btn-danger" name="status"
                                value="3"><?php echo app('translator')->get('Reject'); ?></button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->startPush('js'); ?>

     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
     </script>

    <script>
        "use strict";
        $(document).ready(function() {

            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i',
                step: 1,
                datepicker: true,
                timepicker: true
            });

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
    <script>


        $(document).ready(function() {
            $('[data-fancybox="images"]').fancybox({
                buttons: ["close"],
                loop: true, // Enables looping through images
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payment/report.blade.php ENDPATH**/ ?>