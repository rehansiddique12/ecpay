
<?php $__env->startSection('title', trans($title)); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="card col-md-3 ms-3">
        <div class="payment-info text-center">
            <ul class="list-group">
                <li class="list-group-item font-weight-bold bg-transparent">
                    <img src="<?php echo e(getFile(config('location.withdraw.path').optional($withdraw->method)->image)); ?>" class="card-img-top w-50" alt="<?php echo e(optional($withdraw->method)->name); ?>">
                </li>
                <li class="list-group-item bg-transparent"><?php echo app('translator')->get('Request Amount'); ?> :
                    <span class="float-right text-success"><?php echo e(@$basic->currency_symbol); ?><?php echo e(getAmount($withdraw->amount)); ?> </span>
                </li>
                <li class="list-group-item bg-transparent"><?php echo app('translator')->get('Charge Amount'); ?> :
                    <span class="float-right text-danger"><?php echo e(@$basic->currency_symbol); ?><?php echo e(getAmount($withdraw->charge)); ?> </span>
                </li>
                <li class="list-group-item bg-transparent"><?php echo app('translator')->get('Total Payable'); ?> :
                    <span class="float-right text-danger"><?php echo e(@$basic->currency_symbol); ?><?php echo e(getAmount($withdraw->net_amount)); ?> </span>
                </li>
                <li class="list-group-item bg-transparent"><?php echo app('translator')->get('Available Balance'); ?> :
                    <span class="float-right text-success"><?php echo e(@$basic->currency_symbol); ?><?php echo e($remaining); ?> </span>
                </li>
            </ul>
        </div>

    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header custom-header text-center">
                <h5 class="card-title"><?php echo app('translator')->get('Additional Information To Withdraw Confirm'); ?></h5>
            </div>
            <div class="card-body">

                <form action="" method="post" enctype="multipart/form-data" class="form-row text-left preview-form" id="withdrawForm">
                    <?php echo csrf_field(); ?>
                    <div class="col-md-12">
                        <label><strong>Phone Number                                 <span class="text-danger">*</span>
                                </strong></label>
                        <div class="form-group input-box  mt-2">
                            <input type="text" name="PhoneNumber" class="form-control" required="">
                                                    </div>
                    </div>
                    
                    <div class="col-md-12 mt-4">
                        <div class=" form-group">
                            <button type="submit" class="btn btn-success" id="submitButton" onclick="disableSubmitButton()">
                                <span><?php echo app('translator')->get('Confirm Now'); ?></span>
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css-lib'); ?>
<link rel="stylesheet" href="<?php echo e(asset($themeTrue.'css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('extra-js'); ?>
<script src="<?php echo e(asset($themeTrue.'js/bootstrap-fileinput.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<script>
        function disableSubmitButton() {
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true; // Disable the button
        submitButton.innerHTML = 'Processing...'; // Change button text to "Processing..."
        document.getElementById('withdrawForm').submit(); // Submit the form
    }
</script>

<?php $__env->startPush('script'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('partner.layouts.open', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/previewopen.blade.php ENDPATH**/ ?>