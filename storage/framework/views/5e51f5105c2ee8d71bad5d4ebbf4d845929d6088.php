<?php if (isset($component)) { $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd = $component; } ?>
<?php $component = App\View\Components\PartnerLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('partner-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\PartnerLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center card-title mb-3"><i class="icon-key"></i> <?php echo app('translator')->get('Two Step Verification'); ?></h4>
                <form action="" method="post" class="form-body file-upload" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <?php if($status == "No"): ?>
                        
                            <!-- QR Code Display -->
                            <div class="text-center">
                                <div class="qr-code-container">
                                    <?php echo $qrCodeUrl; ?>

                                </div>
                            
                        </div>

                        <div class="form-group">
                            <label><?php echo app('translator')->get('OTP'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="otp" class="form-control" required />
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3"><?php echo e(trans('Enable')); ?></button>
                        </div>
                    <?php else: ?>
                        <br>
                        <h1 class="text-center">Two Step Verification Successfully Enabled</h1>
                        <br>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </div>
<style>
.qr-code-container svg {
    width: 20% !important; /* Adjust this as needed */
    height: auto;
}
</style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/2fa.blade.php ENDPATH**/ ?>