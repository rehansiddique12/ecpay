
<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = App\View\Components\AdminLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">

        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">

            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Header -->
              <div class="row">
                <div class="col-12">
                  <div class="card mb-6">
                    <div class="user-profile-header-banner">
                      <img src="../../assets/img/pages/profile-banner.png" alt="Banner image" class="rounded-top img-fluid" />
                    </div>
                    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                      <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img
                          src="../../assets/img/avatars/1.png"
                          alt="user image"
                          class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
                      </div>
                      <div class="flex-grow-1 mt-3 mt-lg-5">
                        <div
                          class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                          <div class="user-profile-info">
                            <h4 class="mb-2 mt-lg-6"><?php echo e($data->name); ?> </h4>

                          </div>
                          <?php
    $depositColor = 'text-danger'; // default red

    if ($total_deposit > 60) {
        $depositColor = 'text-success'; // green
    } elseif ($total_deposit >= 40 && $total_deposit <= 60) {
        $depositColor = 'text-warning'; // yellow
    }
?>

                          <span>Live Bank Balance $ 0.00<br> Available Balance $<?php echo e($data->balance); ?></span>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Header -->

              <!-- Navbar pills -->
              <div class="row">
                <div class="col-md-12">
                  <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
                      <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.merchant.profile',$data->id)); ?>"
                          ><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profile</a
                        >
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('admin.merchant.logs',$data->id)); ?>"
                          ><i class="icon-base ti tabler-list icon-sm me-1_5"></i> Logs</a
                        >
                      </li>



                    </ul>
                  </div>
                </div>
              </div>
              <!--/ Navbar pills -->

              <!-- User Profile Content -->
              <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                  <!-- About User -->
                  <div class="card mb-6">
                    <div class="card position-relative">


                        <div class="card-body">
                            <span><h4 class="mb-n3">Gateway performance</h4> <br> Deposit:   <span class="<?php echo e($depositColor); ?>"><?php echo e($total_deposit); ?>%</span> <br> Withdrawal:  <SPAN class="text-danger">##%</SPAN> </span>
                        </div>

                    </div>

                  </div>
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                  <!-- Activity Timeline -->
                  <div class="card card-action mb-6">
                    <div class="card-header align-items-center">



                          <div class="table-responsive">
                            <table class="categories-show-table table table-hover table-striped table-bordered">
                                <h5 class="card-action-title mb-0">
                                    Live Account Balance Log
                                   </h5>
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Transection Id</th>
                                        <th scope="col">Transection Date</th>
                                        <th scope="col">Txn No.</th>
                                        <th scope="col">Partner Txn No.</th>
                                        <th scope="col">Account No.</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">E-Wallet Acc. No.</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Charges</th>

                                        <th scope="col">Final Amount</th>
                                        <th scope="col">Balance</th>
                                        <th scope="col">Differance</th>
                                        <th scope="col">Transection Type</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php if(isset($filter_data)): ?>

                                    <?php $__empty_1 = true; $__currentLoopData = $filter_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>

                                        <td><?php echo e($item['transection_id']); ?></td>
                                        <td><?php echo e($item['txn_created_at']); ?></td>
                                        <td><?php echo e($item['txn_id']); ?></td>
                                        <td><?php echo e($item['partner_transection_id']); ?></td>
                                        <td><?php echo e($item['sender']); ?></td>
                                        <td><?php echo e($item['e_wallet_name']); ?></td>
                                        <td><?php echo e($item['e_wallet_type']); ?></td>
                                        <td><?php echo e($item['e_wallet_phone_number']); ?></td>
                                        <td><?php echo e($item['amount']); ?></td>
                                        <td><?php echo e($item['charge']); ?></td>


                                        <td><?php echo e(number_format($item['final_amount'], 2)); ?></td>
                                        <td><?php echo e(number_format($item['balance'], 2)); ?></td>
                                        <?php

                                            $differance = 0;
                                            if(isset($filter_data[$key+1]['balance'])){
                                                $differance = $filter_data[$key+1]['balance'] + $item['final_amount'] - $item['balance'];
                                            }
                                            $differance = number_format($differance, 2);

                                            if (@request()->website && !empty(@request()->website)) {
                                                if($differance==0){
                                                    echo '<td>'.$differance.'</td>';
                                                }else{
                                                    echo '<td style="background-color: red;color:white">'.$differance.'</td>';
                                                }
                                            }else{
                                                echo '<td></td>';
                                            }

                                        ?>
                                        <td><?php
                                        if($item['transection_type']==1){
                                            echo "Deposit";
                                        }elseif($item['transection_type']==2){
                                            echo "Withdrawal";
                                        }elseif($item['transection_type']==3){
                                            echo "Adjustment";
                                        }elseif($item['transection_type']==4){
                                            echo "Settlement";
                                        }elseif($item['transection_type']==5){
                                            echo "Commission";
                                        }elseif($item['transection_type']==7){
                                            echo "Withdrawal Refunded";
                                        }else{
                                            echo $item['transection_type'];
                                        }
                                        ?></td>
                                        <td><?php echo e($item['source']); ?></td>
                                        <td><?php echo e($item['created_at']); ?></td>

                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="100%">
                                            <p class="text-dark"><?php echo app('translator')->get('No Data Found'); ?></p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>



                    </div>

                  </div>

                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-6">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="categories-show-table table table-hover table-striped table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                                            <th scope="col"><?php echo app('translator')->get('Partner Trx No'); ?></th>
                                            <th scope="col"><?php echo app('translator')->get('Partner Txn Input'); ?></th>
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
                                        <?php $__empty_1 = true; $__currentLoopData = $deposit_logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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

                                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e(optional($fund->gateway)->name); ?></td>
                                            <td class="font-weight-bold"><?php echo e($fund->account_no); ?></td>
                                            <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold"><?php echo e(getAmount($fund->amount)); ?> <?php echo e($fund->gateway_currency); ?></td>
                                            <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success"><?php echo e(getAmount($fund->charge)); ?> <?php echo e($fund->gateway_currency); ?></td>
                                            <td data-label="<?php echo app('translator')->get('Payable'); ?>" class="font-weight-bold"><?php echo e(getAmount($fund->final_amount)); ?> <?php echo e($fund->gateway_currency); ?></td>

                                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                                <?php if($fund->status == 2): ?>
                                                    <?php
                                                        // Get the time difference between now and the created_at timestamp
                                                        $createdAt = \Carbon\Carbon::parse($fund->created_at);
                                                        $currentTime = \Carbon\Carbon::now();
                                                        $diffInMinutes = $createdAt->diffInMinutes($currentTime);
                                                    ?>

                                                    <?php if($diffInMinutes > 10 && @request()->status != 2): ?>
                                                        <span class="badge badge-light">
                                                            <i class="fa fa-circle text-warning warning font-12"></i>
                                                            <?php echo app('translator')->get('Member did not complete'); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light">
                                                            <i class="fa fa-circle text-warning warning font-12"></i>
                                                            <?php echo app('translator')->get('Pending'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <br>
                                                <span class="text text-primary"><?php echo e($fund->e_wallet_phone_number); ?></span>
                                                <?php elseif($fund->status == 1): ?>

                                                    <?php
                                                        // Check if the fund has a payment and if completed_source is set
                                                        if ($fund->payment && isset($fund->payment->completed_source)) {
                                                            // Dynamically assign the class based on completed_source
                                                            if ($fund->payment->completed_source != "AdminPanel") {
                                                                $classColor = "text-success success";
                                                            } else {
                                                                $classColor = "text-purple purple ";
                                                            }
                                                        } else {
                                                            $classColor = "text-purple purple ";
                                                        }
                                                    ?>


                                                <span class="badge badge-light"><i class="fa fa-circle <?php echo e($classColor); ?> font-12"></i> <?php echo app('translator')->get('Completed'); ?></span>
                                                <br>
                                                <span class="<?php echo e($classColor); ?>"><?php echo e($fund->e_wallet_phone_number); ?></span>
                                                <?php elseif($fund->status == 3): ?>
                                                <span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> <?php echo app('translator')->get('Rejected'); ?></span>
                                                <br>
                                                <span class="text text-danger"> <?php echo e($fund->e_wallet_phone_number); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                                <?php echo e(optional($fund->api)->website); ?>

                                                <br>
                                                <?php if(!empty($fund->source)): ?>
                                                <span class="text text-dark">(<?php echo e($fund->source); ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($fund->created_at); ?></td>
                                            <td>
                                                <?php if(!empty($fund->receipt_image)): ?>
                                                <a data-fancybox="images" href="<?php echo e(getFile(config('location.receipts.path').$fund->receipt_image)); ?>">
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
                                                'field_name' => getFile(config('location.deposit.path').date('Y',strtotime($fund->created_at)).'/'.date('m',strtotime($fund->created_at)).'/'.date('d',strtotime($fund->created_at)) .'/'.$v->field_name)
                                                ];
                                                }else{
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

                                                
                                                <button class="edit_button  btn  <?php echo e(($fund->status == 2) ?  'btn-primary' : 'btn-success'); ?> text-white  btn-sm " data-bs-toggle="modal"
                                                     data-bs-target="#myModalDeposit"
                                                      data-title="<?php echo e(($fund->status == 2) ?  trans('Edit') : trans('Details')); ?>"
                                                       data-id="<?php echo e($fund->id); ?>" data-feedback="<?php echo e($fund->feedback); ?>" data-info="<?php echo e(json_encode($details)); ?>"
                                                       data-amount="<?php echo e(getAmount($fund->amount)); ?> <?php echo e($basic->currency); ?>"
                                                       data-username="<?php echo e(optional($fund->user)->username); ?>"
                                                        data-route="<?php echo e(route('admin.payment.action',$fund->id)); ?>"
                                                        data-status="<?php echo e($fund->status); ?>" data-sender="<?php echo e($fund->account_no); ?>"
                                                         data-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">

                                                    <?php if(($fund->status == 2)): ?>
                                                   <i class="icon-base ti tabler-pencil me-1"></i>
                                                    <?php else: ?>
                                                    <i class="icon-base ti tabler-eye me-1"></i>
                                                    <?php endif; ?>

                                                </button>
                                                
                                                
                                                
                                                <button class="edit_buttonc  btn btn-danger text-white  btn-sm" data-bs-toggle="modal" data-bs-target="#myModalDepositc" data-bs-title="Edit" data-bs-id="<?php echo e($fund->id); ?>" data-bs-e_wallet_phone_number="<?php echo e($fund->e_wallet_phone_number); ?>">
                                                   <i class="icon-base ti tabler-device-mobile me-1"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#myModalDepositb" onclick="setBalanceItem(<?php echo e($fund->id); ?>)">
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
                                <?php echo e($deposit_logs->appends($_GET)->links('partials.pagination')); ?>

                            </div>
                        </div>
                      </div>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-6">
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
                                    <?php $__empty_1 = true; $__currentLoopData = $withrawl_logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                                <?php if(optional($item->user)->username!="dummyuser"): ?>
                                                
                                                <?php else: ?>
                                                <?php if($item->api): ?>
                                                <?php echo e(optional($item->api)->name); ?> <b>(<?php echo e(optional($item->api)->acc_type); ?>)</b>
                                                <?php else: ?>
                                                Partner Transection
                                                <?php endif; ?>
                                                <?php endif; ?>

                                            </td>
                                            <td><?php echo e(optional($item->method)->name); ?></td>
                                            <td><?php echo e($item->user_account_no); ?></td>
                                            <td data-label="<?php echo app('translator')->get('Amount'); ?>"
                                                class="font-weight-bold"><?php echo e(getAmount($item->amount,2 )); ?> <?php echo e($basic->currency_symbol); ?></td>
                                            <td data-label="<?php echo app('translator')->get('Charge'); ?>"
                                                class="text-success"><?php echo e(getAmount($item->charge,2)); ?> <?php echo e($basic->currency_symbol); ?></td>

                                            <td data-label="<?php echo app('translator')->get('Net Amount'); ?>"
                                                class="font-weight-bold"><?php echo e(getAmount($item->net_amount,2)); ?> <?php echo e($basic->currency_symbol); ?></td>

                                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                                <?php if($item->transfer_status == 2): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> <?php echo app('translator')->get('Request Approved'); ?></span>
                                                <?php elseif($item->transfer_status == 1): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> <?php echo app('translator')->get('Request Pending'); ?></span>
                                                <?php elseif($item->transfer_status == 3): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> <?php echo app('translator')->get('Request Rejected'); ?></span>
                                                <?php endif; ?>
                                                <br>
                                                
                                                <?php if($item->status == "Complete"): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> <?php echo app('translator')->get('Transfered'); ?></span>
                                                <?php elseif($item->status == "Pending"): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> <?php echo app('translator')->get('Transfer Pending'); ?></span>
                                                <?php elseif($item->status == "Reject"): ?>
                                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> <?php echo app('translator')->get('Transfer Rejected'); ?></span>
                                                <?php else: ?>
                                                <?php echo e($item->status); ?>

                                                <?php endif; ?>
                                                
                                            </td>
                                            <td>
                                                <?php echo e($item->feedback); ?>

                                            </td>
                                            <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                                <?php echo e($item->e_wallet_phone_number); ?>

                                                <br>
                                                <?php echo e($item->e_wallet_type); ?>

                                            </td>
                                            <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->source); ?></td>

                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown">
                                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                            <!-- active / deactive button here -->
                                                            <?php if(adminAccessRoute(config('role.payout_manage.access.edit'))): ?>
                                                            <button type="button" class="btn btn-sm edit_button" data-bs-toggle="modal" data-bs-target="#myModalDepositb" onclick="setBalanceItem(<?php echo e($item->id); ?>)">
                                                                <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                                            </button><br>
                                                            <?php if(isset($item)): ?>
                                                            <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal" data-bs-target="#myModalDepositc" data-title="Edit" data-id="<?php echo e($item->id); ?>" data-e_wallet_phone_number="<?php echo e($item->e_wallet_phone_number); ?>">
                                                                <i class="icon-base ti tabler-device-mobile  me-1"></i> Change E-Wallet No
                                                            </button><br>
                                                            <?php endif; ?>
                                                            <?php

                                                        $details = ($item->information != null) ? json_encode($item->information) : null;
                                                    ?>
                                                    <button type="button" class="btn btn-sm  edit_button"
                                                            data-bs-toggle="modal" data-bs-target="#myModalDeposit"
                                                            data-route="<?php echo e(route('admin.payout-action',$item->id)); ?>"
                                                            data-feedback="<?php echo e($item->feedback); ?>"
                                                            data-info="<?php echo e($details); ?>"
                                                            data-id="<?php echo e($item->id); ?>"
                                                            data-status="<?php echo e($item->status); ?>"
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
                                <?php echo e($withrawl_logs->appends($_GET)->links('partials.pagination')); ?>

                            </div>
                        </div>
                      </div>
                </div>
              </div>
              <!--/ User Profile Content -->
            </div>
            <!--/ Content -->




          </div>
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Modal for Edit button -->
<div class="modal modal-top fade" id="myModalDeposit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Deposit Information'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            date_default_timezone_set('Asia/Kuala_Lumpur');

            ?>
            
                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data" onsubmit="submitForm(this)">
                <?php echo csrf_field(); ?>
                <?php echo method_field('put'); ?>
                <div class="modal-body">
                    <ul class="list-group withdraw-detail">
                    </ul>

                    <div class="get-feedback">
                        <label>Sender Acc. No.</label>
                        <input class="form-control sender" name="sender" type="text" />
                        <label>E-Wallet No.</label>
                        <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number" type="text" />
                        <label>Txn No.</label>
                        <input class="form-control" name="txn_id" type="text" />
                        <label>E-Wallet Type</label>
                        <select class="form-control" name="e_wallet_type">
                            <option value="Personal">Personal</option>
                            <option value="Merchant">Merchant</option>
                        </select>
                        <input type="hidden" name="status" value="1">
                        <label>Payment Receiving DateTime.</label>
                        <input class="form-control" id="e_wallet_phone_number" required value="<?php echo date("Y-m-d H:i"); ?>" name="date_time" type="datetime-local" />
                        <button type="submit" class="btn btn-primary mt-2" id="approvebtn" name="status" value="1"><?php echo app('translator')->get('Approve'); ?></button>
                    </div>

                    <input type="hidden" class="action_id" name="id">
                </div>
            </form>
            <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('put'); ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
                    <?php if(Request::routeIs('admin.payment.pending')): ?>
                    <!-- // -->
                    <?php endif; ?>
                    <input type="hidden" class="action_id" name="id">
                    <button type="submit" class="btn btn-danger" name="status" value="3"><?php echo app('translator')->get('Reject'); ?></button>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-top fade" id="myModalDepositc" tabindex="-1">
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
                        <input class="form-control e_wallet_phone_number" required name="e_wallet_phone_number" type="text" />
                        <button type="submit" class="btn btn-primary mt-2" name="status" value="1"><?php echo app('translator')->get('Change'); ?></button>
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



<div class="modal modal-top fade" id="myModalDepositb" tabindex="-1">
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
                            <div style="background-color: black;color:white;padding:10px"><span id="text3"></span></div>
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
function submitForm(form) {
    // Disable the submit button to prevent multiple submissions
    document.getElementById('approvebtn').disabled = true;

    // Submit the form
    form.submit();
}
</script>
<script>
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
</script>

<script>

    $(document).ready(function() {
        // $('select[name=status]').select2({
        //     selectOnClose: true
        // });

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

</script>



<script>
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

                document.getElementById("text1").innerText = 'An error occurred while processing your request. Please try again.';
                document.getElementById("text2").innerText = '';
                document.getElementById("text3").innerText = '';
            }
        });
    }
</script>


<script>
    jQuery(document).ready(function() {
        jQuery('.modal-header .close').click(function() {
            jQuery('#spinner2').hide();
            jQuery('#tickMark2').hide();
        });
    });
</script>



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

    jQuery(document).ready(function () {
        jQuery('select').select2({
            selectOnClose: true
        });
    });

</script>


<script>
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


<?php $__env->stopPush(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>


<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/merchant/merchant-log.blade.php ENDPATH**/ ?>