<?php if (isset($component)) { $__componentOriginal5f882291579fa517cc3c1c28ed6ccffbe9f8efd3 = $component; } ?>
<?php $component = App\View\Components\AdminGuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin-guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdminGuestLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-6">
                        <a href="<?php echo e(route('admin.login')); ?>" class="app-brand-link">

                            <img src="<?php echo e(asset('assets/uploads/logo/logo.png')); ?>" height="70" viewBox="0 0 128 128"
                            fill="none" alt="ECPay logo">
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1">Admin Login</h4>
                    <p class="mb-6">Please sign-in to your account and start the adventure</p>

                    <form id="formAuthentication" class="mb-4" action="<?php echo e(route('admin.login')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="mb-6 form-control-validation">
                            <label for="email" class="form-label">Email or Username</label>
                            <input id="username" type="text"
                                class="form-control
                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            "
                                name="username" autocomplete="off" autofocus>

                            <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                
                                <input id="password" type="password"
                                    class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password"
                                    autocomplete="current-password">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="my-8">
                            
                        </div>
                        <div class="mb-6">
                            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f882291579fa517cc3c1c28ed6ccffbe9f8efd3)): ?>
<?php $component = $__componentOriginal5f882291579fa517cc3c1c28ed6ccffbe9f8efd3; ?>
<?php unset($__componentOriginal5f882291579fa517cc3c1c28ed6ccffbe9f8efd3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>