<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<style>
    td:hover{
        background-color:lightgray;
        cursor: pointer;
    }
    .modal-auto-width {
    max-width: 80%;
  }
</style>



      <div id="myModal" class="modal modal-top fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-auto-width">
            <div class="modal-content">

                <div class="modal-header modal-colored-header bg-warning">
                    <h5 class="modal-title" id="modalTopTitle"><?php echo app('translator')->get('Record Detail'); ?> </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

      <div class="modal-body" id="modalContent">
        <!-- Content will be dynamically loaded here -->
      </div>
    </div>
  </div>
</div>


    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="<?php echo e(route('partner.payout.report.daily.search')); ?>" method="get">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" value="<?php echo e($from_date); ?>" name="from_date" id="datepicker"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" value="<?php echo e($to_date); ?>" name="to_date" id="datepicker"/>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>E-Wallet</label>
                        <select name="gateway" class="form-select">
                            <option value="">All</option>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gateway->name); ?>"
                                <?php if(@request()->gateway == $gateway->name): ?> selected <?php endif; ?>><?php echo e($gateway->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-2">
                        <br>
                        <button type="submit" class="btn waves-effect waves-light btn-primary"><i class="icon-base ti tabler-search me-1"></i> <?php echo app('translator')->get('Search'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
$gateway = "All";
if(!empty(@request()->gateway)){
$gateway = @request()->gateway;
}
?>

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

                        <th scope="col"><?php echo app('translator')->get('Pending (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Pending Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Approved (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Approved Amount'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Total (QTY)'); ?></th>
                        <th scope="col"><?php echo app('translator')->get('Total Amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payoutsByDate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'All')"> <?php echo e($payout->payout_date); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'Pending')"> <?php echo e($payout->pending_count); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'Pending')"> <?php echo e($payout->pending_amount); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'Approved')"> <?php echo e($payout->complete_count); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'Approved')"> <?php echo e($payout->complete_amount); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'All')"> <?php echo e($payout->payout_count); ?></td>
                            <td onclick="openmodel('<?php echo e($payout->payout_date); ?>', '<?php echo e($gateway); ?>', 'All')"> <?php echo e($payout->total_amount); ?></td>

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


<?php $__env->startPush('js'); ?>
<script>

function openmodel(date, gateway, status) {

  // Ajax request to fetch data
  $.ajax({
   url: "<?php echo e(route('partner.payout.report.detail', ['date' => 'placeholder', 'gateway' => 'placeholder', 'status' => 'placeholder'])); ?>"
         .replace('placeholder', date)
         .replace('placeholder', gateway)
         .replace('placeholder', status),
    method: 'GET',
    success: function(response) {
      console.log(response);
      $('#modalContent').empty();

      // Iterate over the response data and append it to the modal body in a table format
      var table = $('<table class="table"></table>');
      var thead = $('<thead class="thead-dark"><tr><th>Date</th><th>Trx Number</th><th>User Account</th><th>Method</th><th>Amount</th><th>Merchant Charge</th><th>Net Amount</th><th>Status</th><th>Sent From</th><th>Account Type</th></tr></thead>');
      var tbody = $('<tbody></tbody>');

      // Assuming response is an array
      for (var i = 0; i < response.length; i++) {
        var row = $('<tr></tr>');
        var createdAt = new Date(response[i].created_at).toLocaleString('en-GB', { day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
        row.append('<td>' + createdAt + '</td>');
        row.append('<td>' + response[i].payout.txn_id + '</td>');
        row.append('<td>' + response[i].payout.user_account_no + '</td>');
        row.append('<td>' + response[i].method.name + '</td>');
        row.append('<td>' + response[i].amount + 'TK</td>');
        row.append('<td>' + response[i].charge + 'TK</td>');
        row.append('<td>' + response[i].net_amount + 'TK</td>');

        var statusBadge;
        if (response[i].status == 2) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-success success font-12"></i> Completed</span>';

        } else if (response[i].status == 1) {
            statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-warning success font-12"></i> Pending</span>';
        } else {
           statusBadge = '<span class="badge badge-light"><i class="fa fa-circle text-danger danger font-12"></i> Rejected</span>';
        }

        row.append('<td>' + statusBadge + '</td>');

        row.append('<td>' + response[i].payout.e_wallet_phone_number + '</td>');
        row.append('<td>' + response[i].payout.e_wallet_type + '</td>');

        tbody.append(row);
      }

        table.append(thead);
      table.append(tbody);
      $('#modalContent').append(table);

      // Show the modal
      $('#myModal').modal('show');
    },
    error: function(error) {
      console.error('Error fetching data:', error);
    }
  });
}
</script>
<?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/daily_report.blade.php ENDPATH**/ ?>