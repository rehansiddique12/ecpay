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
        h4 {
            color: #7367f0 !important
        }
    </style>

    <div class="container-fluid p-4">
        <h4 class="card-title mb-5"><i class="icon-key"></i> <?php echo app('translator')->get('Password Setting'); ?></h4>
        <form action="" method="post" class="form-body file-upload">
            <?php echo csrf_field(); ?>
            <?php echo method_field('put'); ?>
            <div class="form-body">

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2"><?php echo app('translator')->get('Current Password'); ?></label>
                        <div class="col-lg-6">
                            <input type="password" class="form-control" name="current_password"
                                placeholder="<?php echo app('translator')->get('Current Password'); ?>">

                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2"><?php echo app('translator')->get('New Password'); ?></label>
                        <div class="col-lg-6">
                            <input type="password" name="password" class="form-control"
                                placeholder="<?php echo app('translator')->get('New Password'); ?>">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label class="col-lg-2"><?php echo app('translator')->get('Confirm Password'); ?></label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="<?php echo app('translator')->get('Confirm Password'); ?>">
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <div class="row ">
                        <div class="col-md-6 offset-md-2">
                            <button type="submit"
                                class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3"><?php echo app('translator')->get('Change
                                Password'); ?></button>
                        </div>
                    </div>
                </div>
            </div>


        </form>
    </div>


    <?php $__env->startPush('js'); ?>

    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd)): ?>
<?php $component = $__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd; ?>
<?php unset($__componentOriginal63b7a28d13b6766e633b896378b2f4d690a3e2cd); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/password.blade.php ENDPATH**/ ?>