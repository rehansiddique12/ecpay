
<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('Select Method'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<center>
    <div class="" style="margin-top:10%">
        <div class="row">
            <div class="col-md-6">
                <a href="deposit">
                    <div>
                        <!-- <i class="fas fa-hand-holding-usd" style="font-size:200px;color:red"></i> -->
                        <p style="font-size:80px;color:red"><span class="badge badge-success">Deposit</span></p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="withdrawal">
                    <div>
                        <!-- <i class="fas fa-credit-card" style="font-size:200px;color:blue"></i> -->
                        <p style="font-size:80px;"><span class="badge badge-primary">Withdrawal</span></p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</center>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('partner.layouts.open', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/payout/methods.blade.php ENDPATH**/ ?>