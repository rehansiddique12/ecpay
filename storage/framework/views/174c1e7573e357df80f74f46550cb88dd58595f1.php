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
        <form action="<?php echo e(route('admin.payout-log.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="name" value="<?php echo e(@request()->name); ?>" class="form-control"
                            placeholder="<?php echo app('translator')->get('Email/ Username'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id" value="<?php echo e(@request()->partner_transection_id); ?>"
                            class="form-control" placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="date" class="form-control" value="<?php echo e(@request()->date_time); ?>" name="date_time"
                            id="datepicker" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="4"><?php echo app('translator')->get('All Payment'); ?></option>
                            <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending Payment'); ?>
                            </option>
                            <option value="2" <?php if(@request()->status == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Complete Payment'); ?>
                            </option>
                            <option value="3" <?php if(@request()->status == '3'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Cancel Payment'); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <select name="domain" class="form-select select2" data-allow-clear="true" data-placeholder="Select Domain">
                            <option></option>
                            <option value=""><?php echo app('translator')->get('Select Domain'); ?></option>
                            <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($domain->id); ?>" <?php if(@request()->domain == $domain->id): ?> selected
                                <?php endif; ?>><?php echo e($domain->name); ?> ===> ( <?php echo e($domain->website); ?> )</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 d-flex gap-5">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>
                            <?php echo app('translator')->get('Search'); ?></button>
                        <button type="submit" name="export" value="export" class="btn btn-success"><i
                                class="icon-base ti tabler-download me-1"></i> <?php echo app('translator')->get('Export Data'); ?></button>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <input type="text" value="<?php echo e($letest_record); ?>" id="letest_record" hidden>
    <audio id="notification-sound" src="<?php echo e(asset(config('location.withdrawLog.path'))); ?>/dogru-128492.mp3"
        preload="auto"></audio>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"><?php echo app('translator')->get('ID'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Partner Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Username'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Acc No.'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Remarks'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Sent From'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Source'); ?></th>
                            <?php if(adminAccessRoute(config('role.payout_manage.access.edit'))): ?>
                            <th scope="col"><?php echo app('translator')->get('More'); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($item->id); ?></td>
                            <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($item->created_at,'d M,Y H:i')); ?></td>
                            <td data-label="<?php echo app('translator')->get('Trx Number'); ?>" class="font-weight-bold text-uppercase">
                                <?php echo e($item->trx_id); ?><br>
                                <span class="text text-success"><?php echo e($item->txn_id); ?></span>

                            </td>
                            <td><?php echo e($item->partner_transection_id); ?>

                                <br>
                                <?php echo e($item->member_id); ?>

                            </td>
                            <td data-label="<?php echo app('translator')->get('Username'); ?>">

                                <?php if($item->api): ?>
                                <?php echo e(optional($item->api)->name); ?> <b>(<?php echo e(optional($item->api)->acc_type); ?>)</b>
                                <?php else: ?>
                                Partner Transection
                                <?php endif; ?>

                            </td>
                            <td><?php echo e($item->e_wallet_name); ?></td>
                            <td><?php echo e($item->user_account_no); ?></td>
                            <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold"><?php echo e(getAmount($item->amount,2 )); ?>

                                <?php echo e($basic->currency_symbol); ?></td>
                            <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success">
                                <?php echo e(getAmount($item->charge,2 )); ?> <?php echo e($basic->currency_symbol); ?></td>

                            <td data-label="<?php echo app('translator')->get('Net Amount'); ?>" class="font-weight-bold">
                                <?php echo e(getAmount($item->amount + $item->charge ,2)); ?> <?php echo e($basic->currency_symbol); ?></td>

                           <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <?php if($item->transfer_status == 2): ?>
                                    <span class="badge bg-success mb-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Request Approved'); ?>
                                    </span>
                                <?php elseif($item->transfer_status == 1): ?>
                                    <span class="badge bg-warning mb-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Request Pending'); ?>
                                    </span>
                                <?php elseif($item->transfer_status == 3): ?>
                                    <span class="badge bg-danger mb-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Request Rejected'); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if($item->status == 'Complete'): ?>
                                    <span class="badge bg-success mt-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Transferred'); ?>
                                    </span>
                                <?php elseif($item->status == 'inititate' || $item->status == 'Pending'): ?>
                                    <span class="badge bg-warning mt-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Transfer Pending'); ?>
                                    </span>
                                <?php elseif($item->status == 'Reject'): ?>
                                    <span class="badge bg-danger mt-1">
                                        <i class="fa fa-circle text-white font-12"></i> <?php echo app('translator')->get('Transfer Rejected'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>


                            <td>
                                <?php echo e($item->feedback); ?>

                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                <?php echo e($item->e_wallet_phone_number); ?>

                                <br>
                                <?php echo e($item->e_wallet_type); ?>

                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->request_source); ?></td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- active / deactive button here -->
                                        <?php if(adminAccessRoute(config('role.payout_manage.access.edit'))): ?>
                                            <button type="button" class="btn btn-sm edit_button" data-bs-toggle="modal"
                                                data-bs-target="#newModalb" onclick="setBalanceItem(<?php echo e($item->id); ?>)">
                                                <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                            </button><br>
                                            <?php if(isset($item)): ?>
                                            <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#myModalc" data-title="Edit" data-id="<?php echo e($item->id); ?>"
                                                data-e_wallet_phone_number="<?php echo e($item->e_wallet_phone_number); ?>">
                                                <i class="icon-base ti tabler-device-mobile  me-1"></i> Change E-Wallet No
                                            </button><br>
                                            <?php endif; ?>
                                        <?php

                                        $details = ($item->information != null) ? json_encode($item->information) :
                                        null;
                                        ?>
                                        <button type="button" class="btn btn-sm  edit_button" data-bs-toggle="modal"
                                            data-bs-target="#myModal"
                                            data-route="<?php echo e(route('admin.payout-action',$item->id)); ?>"
                                            data-feedback="<?php echo e($item->feedback); ?>" data-info="<?php echo e($details); ?>"
                                            data-id="<?php echo e($item->id); ?>" data-status="<?php echo e($item->transfer_status); ?>"

                                            data-statusb="<?php echo e($item->status ? $item->status:''); ?>">
                                            <?php if(Request::routeIs('admin.payout-request')): ?>
                                            <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                            <?php else: ?>
                                            <i class="icon-base ti tabler-eye me-1"></i>View
                                            <?php endif; ?>
                                        </button>
                                        <?php endif; ?>

                                    </div>
                                </div>
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
                <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

                </div>
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
                <form role="form" method="POST" action="<?php echo e(route('admin.payout.update_e_wallet')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <div class="get-feedback">

                            <label>E-Wallet No.</label>
                            <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number"
                                type="text" />
                            <button type="submit" class="btn btn-primary mt-3" name="status"
                                value="1"><?php echo app('translator')->get('Change'); ?></button>
                        </div>
                        <input type="hidden" class="action_id" name="id">
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                </div>

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
                <form id="addBalanceForm" action="<?php echo e(route('admin.run.callback')); ?>" method="POST">
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




    <!-- Modal for Edit button -->
    <div class="modal modal-top fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Payout Information'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form role="form" method="POST" class="actionRoute" id="actionRoutee" action=""
                    enctype="multipart/form-data" onsubmit="submitForm(this)">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        

                        <div class="form-group addForm">

                        </div>
                        

                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="status" name="status">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?>
                        </button>

                        <input type="hidden" class="action_id" name="id">
                        <div id="submit1" style="display: none;">
                            <button type="submit" id="btn2" class="btn btn-primary" name="status"
                                value="2"><?php echo app('translator')->get('Approve'); ?></button>
                        </div>
                        <div id="submit2" style="display: none;">
                            <button type="submit" id="btn4" class="btn btn-dark" name="status" value="4"><?php echo app('translator')->get('Mark As
                                Complete'); ?></button>
                        </div>
                        <div id="submit4" style="display: none;">
                            <button type="submit" id="btn5" class="btn btn-warning" name="status" value="5"><?php echo app('translator')->get('Mark
                                As Pending'); ?></button>
                        </div>
                        <div id="submit3" style="display: none;">
                            <button type="submit" id="btn3" class="btn btn-danger" name="status"
                                value="3"><?php echo app('translator')->get('Reject'); ?></button>
                        </div>

                    </div>

                </form>


            </div>
        </div>
    </div>

    <?php $__env->startPush('js'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get references to all buttons
            var btn2 = document.getElementById("btn2");
            var btn3 = document.getElementById("btn3");
            var btn4 = document.getElementById("btn4");
            var btn5 = document.getElementById("btn5");

            // Function to handle button click
            function handleButtonClick(statusValue) {
                // Set the status input field value
                document.getElementById("status").value = statusValue;

                // Disable all buttons
                btn2.disabled = true;
                btn3.disabled = true;
                btn4.disabled = true;
                btn5.disabled = true;

                // Submit the form
                document.querySelector('#actionRoutee').submit();
            }

            // Attach event listeners to each button
            btn2.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default form submission
                handleButtonClick(2);
            });

            btn3.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default form submission

                // Find the select box
                const selectBox = document.querySelector("select[name='feedback']");
                if (selectBox) {
                    // Add the 'required' attribute
                    selectBox.setAttribute("required", "required");

                    // Check if the select box has an empty value
                    if (selectBox.value === "") {
                        alert("Please select an issue before proceeding.");
                        return; // Prevent further execution
                    }
                }

                // Call the function to handle button click
                handleButtonClick(3);
            });

            btn4.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default form submission
                handleButtonClick(4);
            });

            btn5.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default form submission
                handleButtonClick(5);
            });
        });

    </script>

    <script>
        (function (jQuery) {

            jQuery(document).ready(function () {
                jQuery(document).on("click", '.edit_button', function (e) {
                    var id = jQuery(this).data('id');
                    jQuery(".action_id").val(id);
                    jQuery(".actionRoute").attr('action', jQuery(this).data('route'));
                    // var details = Object.entries(jQuery(this).data('info'));
                    var list = [];
                    var ImgPath = "<?php echo e(asset(config('location.withdrawLog.path'))); ?>";
                    // details.map(function (item, i) {
                    //     if (item[1].type == 'file') {
                    //         var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                    //     } else {
                    //         var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>`;
                    //     }
                    //     list[i] = `<li class="list-group-item"><span class="font-weight-bold">${item[0].replace('_', " ")}</span> : ${singleInfo}</li>`;
                    // });

                    console.log(jQuery(this).data('status'));

                    if (jQuery(this).data('status') == '2') {
                        jQuery('#submit1').hide();
                        jQuery('#submit2').show();
                        jQuery('#submit3').show();
                    } else if (jQuery(this).data('status') == '3') {
                        jQuery('#submit1').hide();
                        jQuery('#submit2').hide();
                        jQuery('#submit3').hide();
                    } else {
                        jQuery('#submit1').show();
                        jQuery('#submit2').hide();
                        jQuery('#submit3').show();
                    }

                    if (jQuery(this).data('statusb') == 'Complete') {
                        jQuery('#submit4').show();
                        jQuery('#submit2').hide();
                    } else {
                        jQuery('#submit4').hide();
                    }

                    // list[details.length + 1] = ``;

                    jQuery('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback"><?php echo app('translator')->get('Remarks'); ?></label>
                        <select class="form-control" name="feedback" id="feedback">
                            <option value=""><?php echo app('translator')->get('Select Feedback'); ?></option>
                            <option value="invalid_phone_number"><?php echo app('translator')->get('Invalid phone number'); ?></option>
                            <option value="account_limit_over"><?php echo app('translator')->get('Account limit over'); ?></option>
                            <option value="kyc_incomplete"><?php echo app('translator')->get('Customer account did not complete KYC'); ?></option>
                            <option value="nagad_server_down"><?php echo app('translator')->get('Nagad server down'); ?></option>
                            <option value="bkash_server_down"><?php echo app('translator')->get('bKash server down'); ?></option>
                            <option value="rocket_server_down"><?php echo app('translator')->get('Rocket server down'); ?></option>
                            <option value="others"><?php echo app('translator')->get('Others'); ?></option>
                        </select>
                    </div>
                `);

                    jQuery('.withdraw-detail').html(list);
                });
            });

            jQuery(document).on("click", '.edit_buttonc', function (e) {
                var id = jQuery(this).data('id');
                var e_wallet_phone_number = jQuery(this).data('e_wallet_phone_number');

                jQuery(".action_id").val(id);
                jQuery(".e_wallet_phone_number").val(e_wallet_phone_number);
            });

        })(jQuery);


    </script>

    <script>
        $(document).ready(function () {
            var intervalId; // To store the interval id
            var orderid = document.getElementById("orderid");
            var wid = document.getElementById("wid");
            var acc_no = document.getElementById("acc_no");



            $('#runWithdrawalTest').click(function () {
                if (acc_no.value === "") {
                    alert("Please select an Admin Account");
                    return;
                }

            });

            // Function to perform the AJAX call


            $('.modal-header .close').click(function () {
                $('#runWithdrawalTest').prop('disabled', false);
                $('#spinner2').hide();
                $('#tickMark2').hide();
            });
        });

        function setBalanceItem(itemId) {
            var account_id = jQuery("#account_id");
            account_id.val(itemId);

            jQuery('#spinner2').show();
            jQuery('#runWithdrawalTest').prop('disabled', true);

            var formData = new FormData(jQuery('#addBalanceForm')[0]); // Get form data

            jQuery.ajax({
                type: "POST",
                url: "<?php echo e(route('admin.run.callback')); ?>",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log(response);
                    if (response.status === "success") {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark2').show();
                        jQuery('#apiresponse').show();
                    } else {
                        jQuery('#spinner2').hide();
                        jQuery('#tickMark3').show();
                        jQuery('#apiresponse').hide();
                    }

                    jQuery("#text1").text(response.message);
                    jQuery("#text2").text(response.code);
                    jQuery("#text3").text(response.response_payload);
                },
                error: function (xhr, status, error) {
                    jQuery('#spinner2').hide();
                    jQuery('#tickMark3').show();
                    jQuery('#apiresponse').hide();

                    jQuery("#text1").text(
                        'An error occurred while processing your request. Please try again.');
                    jQuery("#text2").text('');
                    jQuery("#text3").text('');
                }
            });
        }

    </script>

    <script>
        $(document).ready(function () {

            function fetchNotification() {
                var letest_record = document.getElementById("letest_record").value;
                $.ajax({
                    url: "<?php echo e(route('admin.payout-report.getnotification')); ?>",
                    type: "GET",
                    data: {
                        letest_record: letest_record
                    },
                    success: function (response) {
                        // console.log(response.message);
                        if (response.message === "success") {
                            var sound = document.getElementById("notification-sound");
                            const audio = new Audio();
                            audio.addEventListener("canplaythrough", () => {
                                audio.play()
                            });
                            sound.play();
                            window.location.reload();
                        }

                    },
                    error: function (xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            }

            // Run fetchNotification every 5 seconds (5000 milliseconds)
            setInterval(fetchNotification, 5000);
        });

    </script>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
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
    <?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/payout/logs.blade.php ENDPATH**/ ?>