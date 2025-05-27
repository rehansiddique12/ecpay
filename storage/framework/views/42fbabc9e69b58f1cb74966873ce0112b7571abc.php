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
        <form action="<?php echo e(route('admin.payment.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <h3 style="color: #7367f0"><?php echo e($pageTitle); ?></h3>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="<?php echo e(@request()->name); ?>" class="form-control"
                            placeholder="<?php echo app('translator')->get('Username OR Email'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id" value="<?php echo e(@request()->partner_transection_id); ?>"
                            class="form-control" placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="All" <?php if(@request()->status == 'All'): ?> selected <?php endif; ?>><?php echo app('translator')->get('All Payment'); ?>
                            </option>
                            <option value="Complete" <?php if(@request()->status == 'Complete'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Complete Payment'); ?>
                            </option>
                            <option value="Pending" <?php if(@request()->status == 'Pending'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending Payment'); ?>
                            </option>
                            <option value="Reject" <?php if(@request()->status == 'Reject'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Cancel Payment'); ?>
                            </option>
                            <option value="99" <?php if(@request()->status == '99'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Member did not
                                complete'); ?></option>
                        </select>
                    </div>
                </div>


                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <input type="date" class="form-control" value="<?php echo e(@request()->date_time); ?>" name="date_time"
                            id="datepicker" />
                    </div>
                </div>

                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <!--<label>Partner</label>-->
                        <select name="website" class="form-select select2" data-allow-clear="true"
                            data-placeholder="Select Partner">
                            <option></option>
                            <option value="">All Source</option>
                            <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($partner->id); ?>" <?php if(@request()->website == $partner->id): ?> selected
                                <?php endif; ?>><?php echo e($partner->name); ?> ===> ( <?php echo e($partner->website); ?> )</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mt-5">
                    <div class="form-group d-flex gap-5">
                        <button type="submit" class="btn btn-primary"><i
                                class="icon-base ti tabler-search"></i> <?php echo app('translator')->get('Search'); ?></button>&nbsp;
                        <button type="submit" name="export" value="export" class="btn btn-success mt-1"><i
                                class="icon-base ti tabler-download"></i> <?php echo app('translator')->get('Export Data'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add these lines to your HTML header section -->
    <link rel="stylesheet" href="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.css')); ?>">
    <script src="<?php echo e(asset('node_modules/@fancyapps/fancybox/dist/jquery.fancybox.min.js')); ?>"></script>
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Partner Trx No'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Partner Txn Input'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Username'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
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
                            <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($fund->created_at,'d M,Y H:i')); ?></td>
                            <td data-label="<?php echo app('translator')->get('Trx Number'); ?>" class="font-weight-bold text-uppercase">
                                <?php echo e($fund->transaction); ?><br>
                                <span class="text text-success"><?php echo e($fund->txn_id); ?></span>

                            </td>
                            <td><?php echo e(!empty($fund->partner_transection_id)?$fund->partner_transection_id:''); ?>

                                <br>
                                <?php echo e(!empty($fund->member_id)?$fund->member_id:''); ?>

                            </td>

                            <td>
                                <?php echo e(!empty($fund->txn_record)? $fund->txn_record->txn_no : ''); ?>

                            </td>

                            <td data-label="<?php echo app('translator')->get('Username'); ?>">
                                <?php if(optional($fund->user)->username && optional($fund->user)->username !== 'dummyuser'): ?>
                                <a href="<?php echo e(route('admin.user-edit', $fund->user_id)); ?>" target="_blank">
                                    <div class="d-lg-flex d-block align-items-center ">
                                        <div class="mr-3"><img
                                                src="<?php echo e(getFile(config('location.user.path').optional($fund->user)->image)); ?>"
                                                alt="user" class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium"><?php echo e(optional($fund->user)->username); ?></h5>
                                            <span class="text-muted font-14"><?php echo e(optional($fund->user)->email); ?></span>
                                        </div>
                                    </div>
                                </a>
                                <?php elseif($fund->source=="Admin Test"): ?>
                                Admin Test
                                <?php else: ?>
                                <?php echo e(optional($fund->api)->name); ?> <b>(<?php echo e(optional($fund->api)->acc_type); ?>)</b>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e(optional($fund->gateway)->name); ?></td>
                            <td class="font-weight-bold"><?php echo e($fund->sender); ?></td>
                            <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold"><?php echo e(getAmount($fund->amount)); ?>

                                <?php echo e($fund->gateway->currency); ?></td>
                            <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success"><?php echo e(getAmount($fund->charge)); ?>

                                <?php echo e($fund->gateway->currency); ?></td>
                            <td data-label="<?php echo app('translator')->get('Payable'); ?>" class="font-weight-bold">
                                <?php echo e(getAmount($fund->amount) - getAmount($fund->charge)); ?> <?php echo e($fund->gateway->currency); ?>

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
                                <?php elseif($fund->status == "Complete"): ?>

                                <?php
                                // Check if the fund has a payment and if completed_source is set
                                if ($fund->completed_source != "AdminPanel") {
                                    // Dynamically assign the class based on completed_source
                                    // if ($fund->payment->completed_source != "AdminPanel") {
                                    $classColor = "bg-success";
                                    // } else {
                                    // $classColor = "text-purple purple ";
                                    // }
                                } else {
                                    $classColor = "bg-primary";
                                }
                                ?>


                                <span class="badge <?php echo e($classColor); ?>"><i class="fa fa-circle text-white font-12"></i>
                                    <?php echo app('translator')->get('Completed'); ?></span>
                                <br>
                                <span class="<?php echo e($classColor); ?>"><?php echo e(optional($fund->payment)->e_wallet_phone_number); ?></span>
                                <?php elseif($fund->status == "Reject"): ?>
                                <span class="badge bg-danger"><i class="fa fa-circle text-white danger font-12"></i>
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
                                    href="<?php echo e(getFile(config('location.receipts.path').$fund->receipt_image)); ?>">
                                    <h2><i class="fa fa-file"></i></h2>
                                </a>
                                <?php endif; ?>
                            </td>

                            <?php if(adminAccessRoute(config('role.payment_log.access.edit'))): ?>
                            <td data-label="<?php echo app('translator')->get('Action'); ?>">
                                <?php
                                if($fund->detail){
                                    $details =[];
                                    foreach($fund->detail as $k => $v){
                                    if($v->type == "file"){
                                    $details[kebab2Title($k)] = [
                                    'type' => $v->type,
                                    'field_name' =>
                                    getFile(config('location.deposit.path').date('Y',strtotime($fund->created_at)).'/'.date('m',strtotime($fund->created_at)).'/'.date('d',strtotime($fund->created_at))
                                    .'/'.$v->field_name)
                                    ];
                                }
                                else{
                                    $details[kebab2Title($k)] =[
                                    'type' => $v->type,
                                    'field_name' => $v->field_name
                                    ];
                                    }
                                }
                                }else{
                                $details = null;
                                }
                                ?>

                                
                                <button
                                    class="edit_button  btn  <?php echo e(($fund->status == "Pending") ?  'btn-primary' : 'btn-success'); ?> text-white  btn-sm "
                                    data-bs-toggle="modal" data-bs-target="#myModal"
                                    data-title="<?php echo e(($fund->status == "Pending") ?  trans('Edit') : trans('Details')); ?>"
                                    data-id="<?php echo e($fund->id); ?>" data-feedback="<?php echo e($fund->feedback); ?>"
                                    data-info="<?php echo e(json_encode($details)); ?>"
                                    data-amount="<?php echo e(getAmount($fund->amount)); ?> <?php echo e($basic->currency); ?>"
                                    data-username="<?php echo e(optional($fund->user)->username); ?>"
                                    data-route="<?php echo e(route('admin.payment.action',$fund->id)); ?>"
                                    data-status="<?php echo e($fund->status); ?>" data-sender="<?php echo e($fund->sender); ?>"
                                    data-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">

                                    <?php if(($fund->status == "Pending")): ?>
                                    <i class="icon-base ti tabler-pencil me-1"></i>
                                    <?php else: ?>
                                    <i class="icon-base ti tabler-eye me-1"></i>
                                    <?php endif; ?>

                                </button>
                                
                                
                                
                                <button class="edit_buttonc  btn btn-danger text-white  btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#myModalc" data-bs-title="Edit" data-id="<?php echo e($fund->id); ?>"
                                    data-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">
                                    <i class="icon-base ti tabler-device-mobile me-1"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                    data-bs-target="#newModalb" onclick="setBalanceItem(<?php echo e($fund->id); ?>)">
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
                
                    <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data"
                        onsubmit="submitForm(this)">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('put'); ?>
                        <div class="modal-body">
                            <ul class="list-group withdraw-detail">
                            </ul>

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
                                    value="<?php echo date('Y-m-d\TH:i'); ?>"
                                    name="date_time" type="datetime-local" />
                                <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status"
                                    value="Complete"><?php echo app('translator')->get('Approve'); ?></button>
                            </div>

                            <input type="hidden" class="action_id" name="id">
                        </div>
                    </form>
                    <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
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
                        <ul class="list-group withdraw-detail">
                        </ul>

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
                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
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

        $(document).ready(function() {
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
                    var list = [];
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                        } else {
                            var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                        }
                        list[i] = ` <li class="list-group-item"><span class="font-weight-bold"> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`;
                    });
                    jQuery('.withdraw-detail').html(list);

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
        jQuery(document).on("click", ".edit_buttonc", function (e) {
            e.preventDefault();

            var id = jQuery(this).data("bs-id");
            var e_wallet_phone_number = jQuery(this).data("bs-e_wallet_phone_number");

            console.log("Edit clicked:", id, e_wallet_phone_number);

            jQuery(".action_id").val(id);
            jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
        });

        function setBalanceItem(itemId)
        {
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

                    document.getElementById("text1").innerText = 'An error occurred while processing your request. Please try again.';
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



        $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> <?php echo app('translator')->get("Processing..."); ?>');

            // Allow form to proceed
            return true;
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>

     <?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script>
        jQuery(document).ready(function () {

            jQuery(document).on("click", '.edit_button', function (e) {
                var id = jQuery(this).data('id');
                var feedback = jQuery(this).data('feedback');
                var status = jQuery(this).data('status');
                $('#payment_status').val(status);
                // if(status == "Pending")
                // {
                //     $('#showBtns').show();
                // }
                // else
                // {
                //     $('#showBtns').hide();
                // }

                jQuery(".action_id").val(id);
                jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
                if(details != null){
                    var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                        details.map(function (item, i) {
                            if (item[1].type == 'file') {
                                var singleInfo = `<br><img src="${item[1].field_name}" alt="..." class="w-100">`;
                            } else {
                                var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                            }
                            list[i] = `<li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                        });
                }
                jQuery('.withdraw-detail').html(list);

                if (feedback == '') {
                    // var res = `<div class="form-group"><br>
                    //             <label class="font-weight-bold"><?php echo e(trans('Send You Feedback')); ?></label>
                    //             <textarea name="feedback" class="form-control" row="3" required><?php echo e(old('feedback')); ?></textarea>
                    //         </div>`;
                    var res="";
                } else {
                    var res = `<h5><?php echo e(trans('Feedback')); ?></h5>
                    <p>${feedback}</p>`;
                }

                jQuery('.get-feedback').html(res);
            });
        });

    </script>

    <script>
        $(document).ready(function () {
        $('form').on('submit', function () {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text (optional)
            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fa fa-spinner fa-spin me-1"></i> <?php echo app('translator')->get("Processing..."); ?>');

            // Allow form to proceed
            return true;
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payment/logs.blade.php ENDPATH**/ ?>