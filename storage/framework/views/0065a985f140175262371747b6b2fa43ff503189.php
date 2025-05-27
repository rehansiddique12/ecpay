<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(getFile(config('location.logoIcon.path').'favicon.png')); ?>">
    <title><?php echo app('translator')->get($basic->site_title); ?> | <?php echo $__env->yieldContent('title'); ?></title>
    <?php echo $__env->yieldPushContent('style-lib'); ?>
    <link href="<?php echo e(asset('assets/admin/css/bootstrap4-toggle.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/admin/css/all.min.css')); ?>" />
    <link href="<?php echo e(asset('assets/admin/css/select2.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/admin/css/style.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/admin/css/custom.css')); ?>" rel="stylesheet">

    <?php echo $__env->yieldPushContent('style'); ?>



    <!-- added from user -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset($themeTrue . 'css/bootstrap.min.css')); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset($themeTrue . 'css/style.css')); ?>" />

    <!-- added from user -->



</head>

<body>
    

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">


        <div class="container">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1"><?php echo $__env->yieldContent('title'); ?></h4>

                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item text-muted active" aria-current="page"><?php echo app('translator')->get('Dashboard'); ?></li>
                                    <li class="breadcrumb-item text-muted" aria-current="page"><?php echo $__env->yieldContent('title'); ?></li>
                                </ol>
                            </nav>
                        </div>

                    </div>

                </div>
            </div>

            <?php echo $__env->yieldContent('content'); ?>


            <!-- <footer class="footer text-center text-muted">
                <?php echo e(trans('Copyrights')); ?> © <?php echo e(date('Y')); ?> <?php echo app('translator')->get('All Rights Reserved By'); ?> <?php echo app('translator')->get($basic->site_title); ?>
            </footer> -->

        </div>
    </div>



    <script src="<?php echo e(asset('assets/global/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/global/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/global/js/bootstrap.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('js-lib'); ?>

    <script src="<?php echo e(asset('assets/admin/js/bootstrap4-toggle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/app-style-switcher.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/feather.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/global/js/notiflix-aio-2.7.0.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/perfect-scrollbar.jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/sidebarmenu.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/admin-mart.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/custom.js')); ?>"></script>
    <?php echo $__env->make('partner.layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('assets/global/js/axios.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/global/js/vue.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/global/js/pusher.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('js'); ?>
    <?php echo $__env->yieldPushContent('extra-script'); ?>

    <!-- js from user -->
    <?php echo $__env->yieldPushContent('loadModal'); ?>
    <script src="<?php echo e(asset($themeTrue . 'js/bootstrap.bundle.min.js')); ?>"></script>
    <!-- js from user -->

    <?php echo $__env->yieldPushContent('script'); ?>
    
</body>

</html>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/partner/layouts/open.blade.php ENDPATH**/ ?>