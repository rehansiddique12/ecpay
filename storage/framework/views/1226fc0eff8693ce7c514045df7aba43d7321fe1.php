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
            <h4 class="mb-10 text-primary font-weight-medium ">Withdraw</h4>
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
        <form id="filterForm" action="<?php echo e(route('admin.payout-report.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>User</label>
                        <input type="text" name="name" value="<?php echo e(@request()->name); ?>" class="form-control"
                            placeholder="<?php echo app('translator')->get('Email/ Username'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->from_date); ?>" name="from_date"
                            id="from_date" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e(@request()->to_date); ?>" name="to_date"
                            id="to_date" />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>Transaction No</label>
                        <input type="text" name="partner_transection_id"
                            value="<?php echo e(@request()->partner_transection_id); ?>" class="form-control"
                            placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label>User Account No</label>
                        <input type="text" class="form-control" value="<?php echo e(@request()->account_no); ?>"
                            name="account_no" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mt-3">
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
                    <div class="form-group mt-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value=""><?php echo app('translator')->get('All Payment'); ?></option>
                            <option value="1" <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending Payment'); ?>
                            </option>
                            <option value="2" <?php if(@request()->status == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Complete Payment'); ?>
                            </option>
                            <option value="3" <?php if(@request()->status == '3'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Cancel Payment'); ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mt-3">
                        <label for="">Domain</label>
                        <select name="domain" class="form-select select2" data-allow-clear="true"
                            data-placeholder="Select Domain">
                            <option></option>
                            <option value=""><?php echo app('translator')->get('Select Domain'); ?></option>
                            <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($domain->id); ?>" <?php if(@request()->domain == $domain->id): ?> selected <?php endif; ?>>
                                    <?php echo e($domain->name); ?> ===> ( <?php echo e($domain->website); ?> )</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 d-flex justify-content-end align-items-center gap-6">
                    <div class="form-group mt-2">
                        <button type="submit" class="btn  btn-primary mt-2"><i
                                class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                    <div class="form-group mt-2">
                        <a href="<?php echo e(route('admin.merchant_reports.export_by_logs_for_WithDrawl', ['from_date' => $from_date])); ?>"
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
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Withdrawal
                                                                                                                                                                            Amount'); ?>
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
                                <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(dateTime($item->created_at, 'd M,Y H:i')); ?>

                                </td>
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
                                <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold">
                                    <?php echo e(getAmount($item->amount, 2)); ?>

                                    <?php echo e($basic->currency_symbol); ?></td>
                                <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success">
                                    <?php echo e(getAmount($item->charge, 2)); ?> <?php echo e($basic->currency_symbol); ?></td>

                                <td data-label="<?php echo app('translator')->get('Net Amount'); ?>" class="font-weight-bold">
                                    <?php echo e(getAmount($item->amount + $item->charge, 2)); ?> <?php echo e($basic->currency_symbol); ?>

                                </td>

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
                                                <button type="button" class="btn btn-sm edit_button"
                                                    data-bs-toggle="modal" data-bs-target="#newModalb"
                                                    onclick="setBalanceItem(<?php echo e($item->id); ?>)">
                                                    <i class="icon-base ti tabler-report-money me-1"></i> Send Callback
                                                </button><br>
                                                <?php if(isset($item)): ?>
                                                    <button class="btn  edit_buttonc  btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#myModalc" data-title="Edit"
                                                        data-id="<?php echo e($item->id); ?>"
                                                        data-e_wallet_phone_number="<?php echo e($item->e_wallet_phone_number); ?>">
                                                        <i class="icon-base ti tabler-device-mobile  me-1"></i> Change
                                                        E-Wallet No
                                                    </button><br>
                                                <?php endif; ?>
                                                <?php
                                                    $details =
                                                        $item->information != null
                                                            ? json_encode($item->information)
                                                            : null;
                                                ?>
                                                <button type="button" class="btn btn-sm  edit_button"
                                                    data-bs-toggle="modal" data-bs-target="#myModal"
                                                    data-route="<?php echo e(route('admin.payout-action', $item->id)); ?>"
                                                    data-feedback="<?php echo e($item->feedback); ?>"
                                                    data-info="<?php echo e($details); ?>" data-id="<?php echo e($item->id); ?>"
                                                    data-status="<?php echo e($item->transfer_status); ?>"
                                                    data-statusb="<?php echo e($item->status ? $item->status : ''); ?>">
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
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo app('translator')->get('Close'); ?></button>
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
                            <button type="submit" id="btn4" class="btn btn-dark" name="status"
                                value="4"><?php echo app('translator')->get('Mark As
                                                                                                                                Complete'); ?></button>
                        </div>
                        <div id="submit4" style="display: none;">
                            <button type="submit" id="btn5" class="btn btn-warning" name="status"
                                value="5"><?php echo app('translator')->get('Mark
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
        <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
        <script>
            (function($) {
                $(document).ready(function() {
                    // Select2 Initialization
                    let $select = $('.select2').select2({
                        allowClear: true,
                        selectOnClose: true,
                    });
                    $select.on('select2:unselecting', function(e) {
                        $(this).data('unselecting', true);
                    });
                    $select.on('select2:opening', function(e) {
                        if ($(this).data('unselecting')) {
                            $(this).removeData('unselecting');
                            e.preventDefault();
                        }
                    });

                    // Disable submit button on form submit
                    $('form').on('submit', function() {
                        const $submitButton = $(this).find('button[type="submit"]');
                        $submitButton.prop('disabled', true).html(
                            '<i class="fa fa-spinner fa-spin me-1"></i> <?php echo app('translator')->get('Processing...'); ?>');
                        return true;
                    });

                    // Date Filter Buttons
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;
                    const filterForm = document.getElementById('filterForm');

                    function setDateInputs(from, to) {
                        document.getElementById('from_date').value = from;
                        document.getElementById('to_date').value = to;
                    }

                    function setActiveButton(buttonId) {
                        document.querySelectorAll('.btn-date-filter').forEach(btn => btn.classList.remove(
                            'active'));
                        document.getElementById(buttonId).classList.add('active');
                    }

                    $('#btn-today').on('click', function() {
                        setDateInputs(todayStr, todayStr);
                        setActiveButton('btn-today');
                        filterForm.submit();
                    });

                    $('#btn-yesterday').on('click', function() {
                        const y = new Date();
                        y.setDate(today.getDate() - 1);
                        const ys =
                            `${y.getFullYear()}-${String(y.getMonth() + 1).padStart(2, '0')}-${String(y.getDate()).padStart(2, '0')}`;
                        setDateInputs(ys, ys);
                        setActiveButton('btn-yesterday');
                        filterForm.submit();
                    });

                    $('#btn-last7').on('click', function() {
                        const from = new Date();
                        from.setDate(today.getDate() - 6);
                        const fromStr =
                            `${from.getFullYear()}-${String(from.getMonth() + 1).padStart(2, '0')}-${String(from.getDate()).padStart(2, '0')}`;
                        setDateInputs(fromStr, todayStr);
                        setActiveButton('btn-last7');
                        filterForm.submit();
                    });

                    // Edit Button Handling
                    $(document).on("click", '.edit_button', function() {
                        const $this = $(this);
                        const id = $this.data('id');
                        const feedback = $this.data('feedback');
                        const status = $this.data('status');
                        const statusb = $this.data('statusb');
                        const info = $this.data('info');
                        const ImgPath = "<?php echo e(asset(config('location.withdrawLog.path'))); ?>";

                        $(".action_id").val(id);
                        $(".actionRoute").attr('action', $this.data('route'));

                        let list = [];
                        if (info) {
                            Object.entries(info).forEach(([key, val]) => {
                                let content = val.type === 'file' ?
                                    `<br><img src="${ImgPath}/${val.field_name}" alt="..." class="w-50">` :
                                    `<span class="font-weight-bold ml-3">${val.field_name}</span>`;
                                list.push(
                                    `<li class="list-group-item"><span class="font-weight-bold">${key.replace('_', ' ')}</span> : ${content}</li>`
                                );
                            });
                        }

                        // Toggle buttons based on status
                        $('#submit1, #submit2, #submit3, #submit4').hide();
                        if (status == 2) {
                            $('#submit2, #submit3').show();
                        } else if (status == 3) {
                            // all hidden
                        } else {
                            $('#submit1, #submit3').show();
                        }
                        if (statusb == 'Complete') {
                            $('#submit4').show();
                        }

                        // Show remarks dropdown
                        $('.addForm').html(`
                    <div class="form-group">
                        <label for="feedback"><?php echo app('translator')->get('Remarks'); ?></label>
                        <select class="form-control" name="feedback" id="feedback" required>
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

                        $('.withdraw-detail').html(list);
                    });

                    // Status Change Buttons
                    const statusForm = document.querySelector('#actionRoutee');
                    ['btn2', 'btn3', 'btn4', 'btn5'].forEach((btnId, idx) => {
                        const btn = document.getElementById(btnId);
                        if (!btn) return;
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const status = idx + 2;
                            if (btnId === 'btn3') {
                                const selectBox = document.querySelector("select[name='feedback']");
                                if (!selectBox || selectBox.value === '') {
                                    alert("Please select an issue before proceeding.");
                                    return;
                                }
                            }
                            document.getElementById("status").value = status;
                            ['btn2', 'btn3', 'btn4', 'btn5'].forEach(id => document.getElementById(
                                id).disabled = true);
                            statusForm.submit();
                        });
                    });

                    // E-wallet phone number handler
                    $(document).on("click", '.edit_buttonc', function() {
                        $(".action_id").val($(this).data('id'));
                        $(".e_wallet_phone_number").val($(this).data('e_wallet_phone_number'));
                    });

                    // Withdrawal Test
                    $('#runWithdrawalTest').click(function() {
                        if ($('#acc_no').val() === "") {
                            alert("Please select an Admin Account");
                            return;
                        }
                    });

                    // Modal reset
                    $('.modal-header .close').click(function() {
                        $('#runWithdrawalTest').prop('disabled', false);
                        $('#spinner2, #tickMark2').hide();
                    });

                    // Set balance
                    window.setBalanceItem = function(itemId) {
                        $('#account_id').val(itemId);
                        $('#spinner2').show();
                        $('#runWithdrawalTest').prop('disabled', true);

                        const formData = new FormData($('#addBalanceForm')[0]);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo e(route('admin.run.callback')); ?>",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#spinner2').hide();
                                if (response.status === "success") {
                                    $('#tickMark2').show();
                                    $('#apiresponse').show();
                                } else {
                                    $('#tickMark3').show();
                                    $('#apiresponse').hide();
                                }
                                $("#text1").text(response.message);
                                $("#text2").text(response.code);
                                $("#text3").text(response.response_payload);
                            },
                            error: function() {
                                $('#spinner2').hide();
                                $('#tickMark3').show();
                                $('#apiresponse').hide();
                                $("#text1").text(
                                    'An error occurred while processing your request. Please try again.'
                                );
                                $("#text2, #text3").text('');
                            }
                        });
                    }

                    // Notification polling
                    setInterval(function() {
                        $.get("<?php echo e(route('admin.payout-report.getnotification')); ?>", {
                            letest_record: $('#letest_record').val()
                        }, function(response) {
                            if (response.message === "success") {
                                document.getElementById("notification-sound").play();
                                window.location.reload();
                            }
                        }).fail(function(xhr) {
                            console.log('Error:', xhr.responseText);
                        });
                    }, 5000);
                });
            })(jQuery);
        </script>
    <?php $__env->stopPush(); ?>



 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/payout/report.blade.php ENDPATH**/ ?>