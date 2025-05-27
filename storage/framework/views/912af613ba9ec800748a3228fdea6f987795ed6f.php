<?php if (isset($component)) { $__componentOriginal4fd8e55ee701dd0fadc4e108716f3e269bc33cb7 = $component; } ?>
<?php $component = App\View\Components\ErrorLayout::resolve(['title' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('error-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ErrorLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
          <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">404</h1>
          <h4 class="mb-2 mx-2">Page Not Found️ ⚠️</h4>
          <p class="mb-6 mx-2">we couldn't find the page you are looking for</p>
          <a href="<?php echo e($redirectUrl); ?>" class="btn btn-primary mb-10">Back to home</a>
          <div class="mt-4">
            <img
              src="<?php echo e(asset('assets/img/illustrations/page-misc-error.png')); ?>"
              alt="page-misc-error-light"
              width="225"
              class="img-fluid" />
          </div>
        </div>
      </div>
      <div class="container-fluid misc-bg-wrapper">
        <img
          src="<?php echo e(asset('assets/img/illustrations/bg-shape-image-light.png')); ?>"
          height="355"
          alt="page-misc-error"
          data-app-light-img="<?php echo e(asset('assets/img/illustrations/bg-shape-image-light.png')); ?>"
          data-app-dark-img="<?php echo e(asset('assets/img/illustrations/bg-shape-image-dark.png')); ?>" />
      </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fd8e55ee701dd0fadc4e108716f3e269bc33cb7)): ?>
<?php $component = $__componentOriginal4fd8e55ee701dd0fadc4e108716f3e269bc33cb7; ?>
<?php unset($__componentOriginal4fd8e55ee701dd0fadc4e108716f3e269bc33cb7); ?>
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/errors/403.blade.php ENDPATH**/ ?>