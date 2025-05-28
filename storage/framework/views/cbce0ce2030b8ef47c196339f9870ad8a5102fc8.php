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
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.payout-report.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <label>User</label>-->
                <!--        <input type="text" name="name" value="<?php echo e(@request()->name); ?>" class="form-control"-->
                <!--               placeholder="<?php echo app('translator')->get('Email/ Username'); ?>">-->
                <!--    </div>-->
                <!--</div>-->
                <input type="text" hidden name="name" value="<?php echo e(@request()->name); ?>" class="form-control"
                    placeholder="<?php echo app('translator')->get('Email/ Username'); ?>">

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
                        <input type="text" class="form-control" value="<?php echo e(@request()->account_no); ?>" name="account_no" />
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

                <div class="col-md-5">
                    <div class="form-group">
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








                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                        <button type="submit" name="export" value="export"
                            class="btn btn-success mt-2"><i class="icon-base ti tabler-download me-1"></i>
                            <?php echo app('translator')->get('Export Data'); ?></button>
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
                            <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate"><?php echo app('translator')->get('Total Withdrawal Amount'); ?>
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
                            <th scope="col"><?php echo app('translator')->get('Date'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Partner Trx Number'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('User Account'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Request Status'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Remarks'); ?></th>
                            <th scope="col"><?php echo app('translator')->get('Sent From'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="<?php echo app('translator')->get('Date'); ?>"> <?php echo e(convertToUserTimezone($item->created_at)); ?></td>
                                <td data-label="<?php echo app('translator')->get('Trx Number'); ?>" class="font-weight-bold text-uppercase">
                                    <?php echo e($item->trx_id); ?><br>
                                    <span class="text text-success"><?php echo e($item->txn_id); ?></span>

                                </td>
                                <td><?php echo e($item->partner_transection_id); ?>

                                    <br>
                                    <?php echo e($item->member_id); ?>

                                </td>
                                <td data-label="<?php echo app('translator')->get('Method'); ?>"><?php echo e($item->user_account_no); ?></td>
                                <td><?php echo e($item->e_wallet_name); ?></td>
                                <td data-label="<?php echo app('translator')->get('Amount'); ?>" class="font-weight-bold">
                                    <?php echo e(getAmount($item->amount)); ?> <?php echo e($basic->currency_symbol); ?></td>
                                <td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success">
                                    <?php echo e($item->charge); ?> <?php echo e($basic->currency_symbol); ?></td>
                                <!--<td data-label="<?php echo app('translator')->get('Charge'); ?>" class="text-success">-->
                                <?php
                                // if(isset($item->charge)){
                                //     if(!empty($item->source)){
                                //         echo "5%";
                                //     }

                                // }
                                ?>
                                <!--</td>-->
                                <td data-label="<?php echo app('translator')->get('Net Amount'); ?>" class="font-weight-bold">
                                    <?php echo e(getAmount($item->amount + $item->charge)); ?> <?php echo e($basic->currency_symbol); ?></td>

                                <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                    <?php if($item->transfer_status == 2): ?>
                                        <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i>
                                            <?php echo app('translator')->get('Request Approved'); ?></span>
                                    <?php elseif($item->transfer_status == 1): ?>
                                        <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i>
                                            <?php echo app('translator')->get('Request Pending'); ?></span>
                                    <?php elseif($item->transfer_status == 3): ?>
                                        <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i>
                                            <?php echo app('translator')->get('Request Rejected'); ?></span>
                                    <?php endif; ?>
                                    <br>
                                    <?php if($item->status == "Complete"): ?>
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> <?php echo app('translator')->get('Transfered'); ?></span>
                                <?php elseif($item->status == "Reject"): ?>
                                <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> <?php echo app('translator')->get('Transfer Rejected'); ?></span>
                                <?php else: ?>

                                <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> <?php echo app('translator')->get('Transfer Pending'); ?></span>
                                    
                                <?php endif; ?>
                                </td>

                                <td><?php echo e($item->feedback); ?></td>

                                <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                    <?php echo e($item->e_wallet_phone_number); ?>

                                    <br>
                                    <?php echo e($item->e_wallet_type); ?>

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
                <?php echo e($records->appends($_GET)->links('partials.pagination')); ?>

            </div>
        </div>
    </div>




    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="myModalLabel"><?php echo app('translator')->get('Payout Information'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    <div class="modal-body">
                        <ul class="list-group withdraw-detail">
                        </ul>

                        <?php if(Request::routeIs('partner.payout-request')): ?>
                            <div class="form-group addForm">

                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo app('translator')->get('Close'); ?>
                        </button>
                        <?php if(Request::routeIs('partner.payout-request')): ?>
                            <input type="hidden" class="action_id" name="id">
                            <button type="submit" class="btn btn-primary" name="status"
                                value="2"><?php echo app('translator')->get('Approve'); ?></button>
                            <button type="submit" class="btn btn-danger" name="status"
                                value="3"><?php echo app('translator')->get('Reject'); ?></button>
                        <?php endif; ?>
                    </div>

                </form>


            </div>
        </div>
    </div>

<?php $__env->startPush('js'); ?>
    <!-- jQuery UI -->
    
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <!-- DateTimePicker Add-on -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
    </script>


    <script>


        $(document).ready(function() {
            // $('select').select2({
            //     selectOnClose: true
            // });

            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i',
                step: 1,
                datepicker: true,
                timepicker: true
            });


            $(document).on("click", '.edit_button', function(e) {
                    var id = $(this).data('id');
                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    var ImgPath = "<?php echo e(asset(config('location.withdrawLog.path'))); ?>";
                    details.map(function(item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo =
                                `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        } else {
                            var singleInfo =
                                `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] =
                            ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });


                    if ($(this).data('status') != '1') {
                        list[details.length + 1] =
                            `<li class="list-group-item"><span class="font-weight-bold"><?php echo app('translator')->get('Partner Feedback'); ?></span> : <span">${$(this).data('feedback')}</span></li>`;
                        $('.addForm').html(``)
                    } else {
                        list[details.length + 1] = ``;
                        $('.addForm').html(`
                                <div class="form-group">
                                <label for="feedback"><?php echo app('translator')->get('feedback'); ?></label>
                                <textarea class="form-control" name="feedback"></textarea>
                                </div>
                        `);
                    }

                    $('.withdraw-detail').html(list);
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
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/report.blade.php ENDPATH**/ ?>