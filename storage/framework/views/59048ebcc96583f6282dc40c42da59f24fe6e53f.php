<!doctype html>

<html
  lang="en"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-skin="default"
  data-assets-path="<?php echo e(asset('public/assets/')); ?>"
  data-template="horizontal-menu-template"
  data-bs-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport"
    content="width=device-width, initial-scale=2.0, user-scalable=no, minimum-scale=2.0, maximum-scale=2.0" />

    <title><?php echo e($title ? 'ECPay System - ' . $title : 'ECPay System'); ?></title>



<meta name="description" content="" />
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon.png')); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/iconify-icons.css')); ?>" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/node-waves/node-waves.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/pickr/pickr-themes.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/core.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/fontawesome.css')); ?>" />
    <!-- Vendors CSS -->

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />

    <!-- endbuild -->

    <!-- Page CSS -->
    

    <!-- Helpers -->
    <script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="<?php echo e(asset('assets/js/config.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('styles'); ?>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->

        <?php echo $__env->make('admin.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- / Navbar -->

        <!-- Layout container -->

            <!--/ Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                  <div class="text-body">
                    Copyrights ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    ,  All Rights Reserved By Your Company Name
                  </div>
                  <div class="d-none d-lg-inline-block">
                    <a href="https://themeforest.net/licenses/standard" class="footer-link me-4" target="_blank"
                      >License</a
                    >
                    <a href="https://themeforest.net/user/pixinvent/portfolio" target="_blank" class="footer-link me-4"
                      >More Themes</a
                    >

                    <a
                      href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
                      target="_blank"
                      class="footer-link me-4"
                      >Documentation</a
                    >

                    <a href="https://pixinvent.ticksy.com/" target="_blank" class="footer-link d-none d-sm-inline-block"
                      >Support</a
                    >
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!--/ Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="<?php echo e(asset('/assets/vendor/libs/jquery/jquery.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('/assets/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('/assets/vendor/libs/node-waves/node-waves.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/@algolia/autocomplete-js.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/pickr/pickr.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/hammer/hammer.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/libs/i18n/i18n.js')); ?>"></script>

    <script src="<?php echo e(asset('/assets/vendor/js/menu.js')); ?>"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="<?php echo e(asset('/assets/js/main.js')); ?>"></script>

    <!-- Page JS -->
    
    <?php echo $__env->yieldPushContent('js'); ?>
  </body>
</html>
<?php /**PATH C:\xampp\htdocs\ecpay\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>