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
    <span class="badge badge-primary">Available to withdraw: <b><?php echo e($withdrawal_able_amount); ?> TK</b></span>
</h1>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.payout-log.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <!--<div class="col-md-3">-->
                <!--    <div class="form-group">-->
                <!--        <input type="text" name="name" value="<?php echo e(@request()->name); ?>" class="form-control"-->
                <!--               placeholder="<?php echo app('translator')->get('Email/ Username/ Trx'); ?>">-->
                <!--    </div>-->
                <!--</div>-->

                <input type="text" name="name" hidden value="<?php echo e(@request()->name); ?>" class="form-control"
                               placeholder="<?php echo app('translator')->get('Email/ Username/ Trx'); ?>">


                <div class="col-md-3">
                    <div class="form-group">
                        <input type="date" class="form-control" value="<?php echo e(@request()->date_time); ?>" name="date_time" id="datepicker"/>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="partner_transection_id" value="<?php echo e(@request()->partner_transection_id); ?>" class="form-control" placeholder="<?php echo app('translator')->get('Transection No.'); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <select name="status" class="form-control">

                            <option value="1"
                                    <?php if(@request()->status == '1'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Pending Payment'); ?></option>
                                    <option value="4" <?php if(@request()->status == '4'): ?> selected <?php endif; ?>><?php echo app('translator')->get('All Payment'); ?></option>
                            <option value="2"
                                    <?php if(@request()->status == '2'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Complete Payment'); ?></option>
                            <option value="3"
                                    <?php if(@request()->status == '3'): ?> selected <?php endif; ?>><?php echo app('translator')->get('Cancel Payment'); ?></option>
                        </select>
                    </div>
                </div>






                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>

            </div>
        </form>

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
                        <th scope="col"><?php echo app('translator')->get('Method'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Merchant Charge'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Net Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Status'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Transfer Status'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Sent From'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('More'); ?></th>

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

                            <td><?php echo e($item->e_wallet_name); ?></td>
                            <td data-label="<?php echo app('translator')->get('Amount'); ?>"
                                class="font-weight-bold"><?php echo e(getAmount($item->amount )); ?> <?php echo e($basic->currency_symbol); ?></td>
                            <td data-label="<?php echo app('translator')->get('Charge'); ?>"
                                class="text-success"><?php echo e($item->charge); ?> <?php echo e($basic->currency_symbol); ?></td>

                            <td data-label="<?php echo app('translator')->get('Net Amount'); ?>"
                                class="font-weight-bold"><?php echo e(getAmount($item->amount + $item->charge)); ?> <?php echo e($basic->currency_symbol); ?></td>

                            <td data-label="<?php echo app('translator')->get('Status'); ?>" class="text-lg-center text-right">
                                <?php if($item->transfer_status == 2): ?>
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> <?php echo app('translator')->get('Request Approved'); ?></span>
                                <?php elseif($item->transfer_status == 1): ?>
                                    <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> <?php echo app('translator')->get('Request Pending'); ?></span>
                                <?php elseif($item->transfer_status == 3): ?>
                                    <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> <?php echo app('translator')->get('Request Rejected'); ?></span>
                                <?php endif; ?>
                                <br>
                                
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                <?php if($item->status == "Complete"): ?>
                                    <span class="badge badge-light"><i class="fa fa-circle text-success font-12"></i> <?php echo app('translator')->get('Transfered'); ?></span>
                                <?php elseif($item->status == "Reject"): ?>
                                <span class="badge badge-light"><i class="fa fa-circle text-danger font-12"></i> <?php echo app('translator')->get('Transfer Rejected'); ?></span>
                                <?php else: ?>

                                <span class="badge badge-light"><i class="fa fa-circle text-warning font-12"></i> <?php echo app('translator')->get('Transfer Pending'); ?></span>
                                    
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php echo app('translator')->get('Method'); ?>">
                                <?php echo e($item->e_wallet_phone_number); ?>

                                <br>
                                <?php echo e($item->e_wallet_type); ?>

                            </td>

                                <td data-label="<?php echo app('translator')->get('More'); ?>">
                                    <?php
                                        $details = ($item->information != null) ? json_encode($item->information) : null;
                                    ?>
                                    <button type="button" class="btn btn-primary btn-icon edit_button"
                                            data-toggle="modal" data-target="#myModal"
                                            data-route="<?php echo e(route('partner.payout-action',$item->id)); ?>"
                                            data-feedback="<?php echo e($item->feedback); ?>"
                                            data-info="<?php echo e($details); ?>"
                                            data-id="<?php echo e($item->id); ?>"
                                            data-status="<?php echo e($item->status); ?>">
                                        <?php if(Request::routeIs('partner.payout-request')): ?>
                                            <i class="fa fa-pencil-alt"></i>
                                        <?php else: ?>
                                            <i class="fa fa-eye"></i>
                                        <?php endif; ?>
                                    </button>
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
    <script>
        (function ($) {

            $(document).ready(function () {
                $(document).on("click", '.edit_button', function (e) {
                    var id = $(this).data('id');
                    $(".action_id").val(id);
                    $(".actionRoute").attr('action', $(this).data('route'));
                    var details = Object.entries($(this).data('info'));
                    var list = [];
                    var ImgPath = "<?php echo e(asset(config('location.withdrawLog.path'))); ?>";
                    details.map(function (item, i) {
                        if (item[1].type == 'file') {
                            var singleInfo = `<br><img src="${ImgPath}/${item[1].field_name}" alt="..." class="w-50">`;
                        } else {
                            var singleInfo = `<span class="font-weight-bold ml-3">${item[1].field_name}</span>  `;
                        }
                        list[i] = ` <li class="list-group-item"><span class="font-weight-bold "> ${item[0].replace('_', " ")} </span> : ${singleInfo}</li>`
                    });


                    if ($(this).data('status') != '1') {
                        list[details.length + 1] = `<li class="list-group-item"><span class="font-weight-bold"><?php echo app('translator')->get('Partner Feedback'); ?></span> : <span">${$(this).data('feedback')}</span></li>`;
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
        })(jQuery);


        $(document).ready(function () {
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

<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/logs.blade.php ENDPATH**/ ?>