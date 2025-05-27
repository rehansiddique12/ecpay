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
        <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>

      <?php
        $today = \Carbon\Carbon::today()->toDateString();
        $yesterday = \Carbon\Carbon::yesterday()->toDateString();
        $last7 = \Carbon\Carbon::today()->subDays(6)->toDateString();
    ?>

    
    <style>
        .hover:hover {
            background-color: #ffc000;
            color: white;
        }

        .btn-yellow.active {
            background-color: #ffc000 !important;
            color: white !important;
            border: 2px solid #e0a800;
        }
    </style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="d-flex d-lg-flex d-md-block align-items-center">
            <h4 class="mb-10 text-primary font-weight-medium ">Deposit</h4>
            <div class="ml-20 d-flex gap-5 mb-10" style="margin-left: 30px;">
                <button type="button"
                    class="btn btn-yellow btn-date-filter <?php echo e(request('from_date') == $today && request('to_date') == $today ? 'active' : ''); ?>"
                    id="btn-today">Today</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter <?php echo e(request('from_date') == $yesterday && request('to_date') == $yesterday ? 'active' : ''); ?>"
                    id="btn-yesterday">Yesterday</button>

                <button type="button"
                    class="btn btn-yellow btn-date-filter <?php echo e(request('from_date') == $last7 && request('to_date') == $today ? 'active' : ''); ?>"
                    id="btn-last7">Last 7 days</button>
            </div>
        </div>
        <form action="<?php echo e(route('admin.payment.report.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date"
                            id="from_date" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date"
                            id="to_date" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="<?php echo e(@request()->account_no); ?>"
                            name="account_no" id="datepicker" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Source</label>
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="Select Source">
                            <option></option>
                            <option value="">All Source</option>
                            <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partner->id); ?>" <?php if(@request()->website == $partner->id): ?> selected <?php endif; ?>>
                                    <?php echo e($partner->name); ?> ===> ( <?php echo e($partner->website); ?> )</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-2">
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
                

                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Payments</label>
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
                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <label>Transaction</label>
                        <input type="text" name="partner_transection_id"
                            value="<?php echo e(@request()->partner_transection_id); ?>" class="form-control"
                            placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>

                <div class="col-md-3 d-flex justify-content-end align-items-center gap-6">
                    <div class="form-group mt-2">
                        <button type="submit" class="btn  btn-primary mt-2"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                    <div class="form-group mt-2">
                        <a href="<?php echo e(route('admin.merchant_reports.export_by_logs', ['from_date' => $from_date])); ?>"
                        class="btn waves-effect waves-light btn-success" id="exportButton">
                        <i class="icon-base ti tabler-download me-1"></i> <?php echo app('translator')->get('Export'); ?>
                     </a>
                    </div>
                </div>
                
            </div>
        </form>
    </div>

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
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total
                                                                                                                                                                            Transactions'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i class="icon-base ti tabler-wallet me-1"></i></span>



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
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Deposit
                                                                                                                                                                            Amount'); ?>
                            </h6>
                        </div>
                        <div class="ml-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"> <i
                                    class="icon-base ti tabler-currency-dollar me-1"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <link rel="stylesheet" href="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css')); ?>">
    <script src="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js')); ?>"></script>
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
                            <th scope="col"><?php echo app('translator')->get('Username'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Type'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Code'); ?></th>
                            <th scope="col">Acc. No.</th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Final Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                            <th scope="col">Completed At</th>
                            <th scope="col"><?php echo app('translator')->get('Receipt'); ?></th>
                            <?php if(adminAccessRoute(config('role.payment_log.access.edit'))): ?>
                                <th scope="col"><?php echo app('translator')->get('Action'); ?></th>
                            <?php endif; ?>
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

                                <td data-label="<?php echo app('translator')->get('Username'); ?>">
                                    <?php if($fund->source == 'Admin Test'): ?>
                                        Admin Test
                                    <?php else: ?>
                                        <?php echo e(optional($fund->api)->name); ?> <b>(<?php echo e(optional($fund->api)->acc_type); ?>)</b>
                                    <?php endif; ?>
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
                                                $classColor = 'bg-success';
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
                                <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                    <?php echo e(optional($fund->api)->website); ?>

                                    <br>
                                    <?php if(!empty($fund->request_source)): ?>
                                        <span class="text text-dark">(<?php echo e($fund->request_source); ?>)</span>
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

                                <?php if(adminAccessRoute(config('role.payment_log.access.edit'))): ?>
                                    <td data-label="<?php echo app('translator')->get('Action'); ?>">
                                        

                                        
                                        <button
                                            class="edit_button  btn  <?php echo e($fund->status == 'Pending' ? 'btn-primary' : 'btn-success'); ?> text-white  btn-sm "
                                            data-bs-toggle="modal" data-bs-target="#myModal"
                                            data-title="<?php echo e($fund->status == 'Pending' ? trans('Edit') : trans('Details')); ?>"
                                            data-id="<?php echo e($fund->id); ?>" data-feedback="<?php echo e($fund->feedback); ?>"
                                            data-info=""
                                            data-amount="<?php echo e(getAmount($fund->amount)); ?> <?php echo e($basic->currency); ?>"
                                            data-username="<?php echo e(optional($fund->user)->username); ?>"
                                            data-route="<?php echo e(route('admin.payment.action', $fund->id)); ?>"
                                            data-status="<?php echo e($fund->status); ?>" data-sender="<?php echo e($fund->sender); ?>"
                                            data-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">

                                            <?php if($fund->status == 'Pending'): ?>
                                                <i class="icon-base ti tabler-pencil me-1"></i>
                                            <?php else: ?>
                                                <i class="icon-base ti tabler-eye me-1"></i>
                                            <?php endif; ?>

                                        </button>
                                        
                                        
                                        
                                        <button class="edit_buttonc  btn btn-danger text-white  btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#myModalc" data-bs-title="Edit"
                                            data-id="<?php echo e($fund->id); ?>"
                                            data-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">
                                            <i class="icon-base ti tabler-device-mobile me-1"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                            data-bs-target="#newModalb"
                                            onclick="setBalanceItem(<?php echo e($fund->id); ?>)">
                                            <i class="icon-base ti tabler-direction-sign me-1"></i>
                                        </button>

                                    </td>
                                <?php endif; ?>
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

    <!-- Modal for Edit button -->
    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Deposit Information'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');

                ?>
                
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data" onsubmit="submitForm(this)">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">

                        <div class="get-feedback">
                            <label>Sender Acc. No.</label>
                            <input class="form-control sender" name="sender" type="text" />
                            <label>E-Wallet No.</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <label>Txn No.</label>
                            <input class="form-control" name="txn_id" type="text" />
                            <label>E-Wallet Type</label>
                            <select class="form-select" name="e_wallet_type">
                                <option value="Personal">Personal</option>
                                <option value="Merchant">Merchant</option>
                            </select>
                            <input type="hidden" name="status" value="Complete">
                            <label>Payment Receiving DateTime.</label>
                            <input class="form-control" id="e_wallet_phone_number" required
                                value="<?php echo date('Y-m-d\TH:i'); ?>" name="date_time" type="datetime-local" />
                            <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status"
                                value="Complete"><?php echo app('translator')->get('Approve'); ?></button>
                        </div>

                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                        <?php if(Request::routeIs('admin.payment.pending')): ?>
                            <!-- // -->
                        <?php endif; ?>
                        <input type="hidden" class="action_id" name="id">
                        <input type="hidden" name="status" value="Reject">
                        <button type="submit" class="btn btn-danger" name="status"
                            value="Reject"><?php echo app('translator')->get('Reject'); ?></button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="myModalc" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Change E-Wallet No.'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                date_default_timezone_set('Asia/Kuala_Lumpur');

                ?>
                <form role="form" method="POST" action="<?php echo e(route('admin.payment.update_e_wallet')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">
                       

                        <div class="get-feedback">

                            <label>E-Wallet No.</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-2" name="status"
                                value="1"><?php echo app('translator')->get('Change'); ?></button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>
                <form role="form" method="POST" class="actionRoute" action=""
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-top fade" id="newModalb" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Send Callback'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm" action="<?php echo e(route('admin.run.deposit.callback')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row justify-content-between align-items-center">
                            <input type="text" hidden id="account_id" class="form-control" name="id">

                            <div class="col-md-12">
                                Callback Status
                                <span id="spinner2" style="display: none;">
                                    <span class="spinner-border text-primary" role="status">
                                    </span>
                                </span>
                                <span id="tickMark2" style="display: none;">
                                    <i class="fa fa-check-circle text-success"></i>
                                </span>
                                <span id="tickMark3" style="display: none;">
                                    <i class="fa fa-times-circle text-danger"></i>
                                </span>
                                <br>
                                <br>
                                <p>Message: <span id="text1"></span></p>
                                <br>
                                <div id="apiresponse" style="display: none;">
                                    <h4>Response</h4>
                                    <p>Response Code: <span id="text2"></span></p>
                                    <p>Response Body: </p>
                                    <div style="background-color: black;color:white;padding:10px"><span
                                            id="text3"></span></div>
                                </div>

                            </div>

                            <!-- <br>
                        <br> -->

                            <!-- <div class="col-md-12">
                            <button type="button" disabled id="runWithdrawalTest" class="btn btn-primary">Run Withdrawal Test</button>

                        </div> -->
                        </div>

                    </div>
            </div>
            </form>
        </div>
    </div>




    <?php $__env->startPush('js'); ?>
        <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
        <script>
            $(document).ready(function() {
                $('form').on('submit', function() {
                    const $form = $(this);
                    const $submitButton = $form.find('button[type="submit"]');

                    // Disable button and change text (optional)
                    $submitButton.prop('disabled', true);
                    $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> <?php echo app('translator')->get('Processing...'); ?>');

                    // Allow form to proceed
                    return true;
                });

            });

            function submitForm(form) {
                // Disable the submit button to prevent multiple submissions
                document.getElementById('approvebtn').disabled = true;

                // Submit the form
                form.submit();
            }

            function refreshDateTime() {
                var inputDateTimeString = document.getElementById("e_wallet_phone_number").value;
                var inputDateTime = new Date(inputDateTimeString).getTime();
                var currentDateTimeKL = new Date().toLocaleString("en-US", {
                    timeZone: "Asia/Kuala_Lumpur"
                });

                var date = new Date(currentDateTimeKL);
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                var hours = date.getHours().toString().padStart(2, '0');
                var minutes = date.getMinutes().toString().padStart(2, '0');
                // var seconds = date.getSeconds().toString().padStart(2, '0');

                var formattedDateTimeKL = `${year}-${month}-${day} ${hours}:${minutes}`;

                // console.log('ok');

                var currentDateTime = new Date(currentDateTimeKL).getTime();
                var twoMinutesAgoTimestamp = currentDateTime - (2 * 60 * 1000);
                if (inputDateTime > twoMinutesAgoTimestamp) {
                    // console.log('ok');
                    document.getElementById("e_wallet_phone_number").value = formattedDateTimeKL;
                }
            }

            setInterval(refreshDateTime, 5000);


            jQuery(document).on("click", ".edit_buttonc", function(e) {
                e.preventDefault();

                var id = jQuery(this).data("bs-id");
                var e_wallet_phone_number = jQuery(this).data("bs-e_wallet_phone_number");

                console.log("Edit clicked:", id, e_wallet_phone_number);

                jQuery(".action_id").val(id);
                jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
            });

            function setBalanceItem(itemId) {
                var account_id = document.getElementById("account_id");
                account_id.value = itemId;

                jQuery('#spinner2').show();
                jQuery('#runWithdrawalTest').prop('disabled', true);

                var formData = new FormData(jQuery('#addBalanceForm')[0]); // Get form data

                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo e(route('admin.run.deposit.callback')); ?>",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    data: formData, // Use FormData object
                    processData: false, // Don't process the data
                    contentType: false, // Don't set contentType
                    success: function(response) {
                        if (response.status === "success") {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark2').show();
                            jQuery('#apiresponse').show();
                        } else {
                            jQuery('#spinner2').hide();
                            jQuery('#tickMark3').show();
                            jQuery('#apiresponse').hide();
                        }

                        document.getElementById("text1").innerText = response.message;
                        document.getElementById("text2").innerText = response.code;
                        document.getElementById("text3").innerText = response.response_payload;
                    },
                    error: function(xhr, status, error) {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark3').show();
                        jQuery('#apiresponse').hide();

                        document.getElementById("text1").innerText =
                            'An error occurred while processing your request. Please try again.';
                        document.getElementById("text2").innerText = '';
                        document.getElementById("text3").innerText = '';
                    }
                });
            }

            jQuery(document).ready(function() {
                jQuery('.modal-header .close').click(function() {
                    jQuery('#spinner2').hide();
                    jQuery('#tickMark2').hide();
                });
            });



            $(document).ready(function() {

                let $select = $('.select2').select2({
                    // placeholder: "Select Partner",
                    allowClear: true,
                    selectOnClose: true,
                });

                // Prevent dropdown from opening on clear
                $select.on('select2:unselecting', function(e) {
                    $(this).data('unselecting', true);
                });

                $select.on('select2:opening', function(e) {
                    if ($(this).data('unselecting')) {
                        $(this).removeData('unselecting');
                        e.preventDefault();
                    }
                });
            });


            jQuery(document).ready(function() {

                jQuery(document).on("click", '.edit_button', function(e) {
                    var id = jQuery(this).data('id');
                    var sender = jQuery(this).data('sender');
                    var feedback = jQuery(this).data('feedback');
                    var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');

                    jQuery(".action_id").val(id);
                    jQuery(".sender").val(sender);
                    jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
                    jQuery(".actionRoute").attr('action', jQuery(this).data('route'));

                    var details = Object.entries(jQuery(this).data('info'));

                    if (feedback == '') {
                        var res = `<div class="form-group"><br>
                                        <label class="font-weight-bold"><?php echo e(trans('Send You Feedback')); ?></label>
                                        <textarea name="feedback" class="form-control" row="3" required><?php echo e(old('feedback')); ?></textarea>
                                </div>`;
                    } else {
                        var res = `<h5><?php echo e(trans('Feedback')); ?></h5>
                                    <p>${feedback}</p>`;
                    }

                    jQuery('.get-feedback').html(res);
                });
            });


        document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = `${yyyy}-${mm}-${dd}`;

        function setDateInputs(from, to) {
            document.getElementById('from_date').value = from;
            document.getElementById('to_date').value = to;
        }

        function setActiveButton(buttonId) {
            document.querySelectorAll('.btn-date-filter').forEach(btn => btn.classList.remove('active'));
            document.getElementById(buttonId).classList.add('active');
        }

        document.getElementById('btn-today').addEventListener('click', function () {
            setDateInputs(todayStr, todayStr);
            setActiveButton('btn-today');
               const form = document.querySelector('form[action="<?php echo e(route('admin.payment.report.search')); ?>"]');
          form.submit();
        });

        document.getElementById('btn-yesterday').addEventListener('click', function () {
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            const yyy = yesterday.getFullYear();
            const mmm = String(yesterday.getMonth() + 1).padStart(2, '0');
            const ddd = String(yesterday.getDate()).padStart(2, '0');
            const yesterdayStr = `${yyy}-${mmm}-${ddd}`;
            setDateInputs(yesterdayStr, yesterdayStr);
            setActiveButton('btn-yesterday');
             const form = document.querySelector('form[action="<?php echo e(route('admin.payment.report.search')); ?>"]');
          form.submit();
        });

        document.getElementById('btn-last7').addEventListener('click', function () {
            const from = new Date();
            from.setDate(today.getDate() - 6);
            const yyy = from.getFullYear();
            const mmm = String(from.getMonth() + 1).padStart(2, '0');
            const ddd = String(from.getDate()).padStart(2, '0');
            const fromStr = `${yyy}-${mmm}-${ddd}`;
            setDateInputs(fromStr, todayStr);
            setActiveButton('btn-last7');
             const form = document.querySelector('form[action="<?php echo e(route('admin.payment.report.search')); ?>"]');
          form.submit();
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/report.blade.php ENDPATH**/ ?>