<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Partner Login</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon/favicon.ico')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <!-- <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" /> -->

    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" />
    <!-- <link rel="stylesheet" href="../../assets/css/demo.css" /> -->

    <!-- Vendors CSS -->

    <!-- <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" /> -->

    <!-- endbuild -->

    <!-- Vendor -->
    <!-- <link rel="stylesheet" href="../../assets/vendor/libs/@form-validation/form-validation.css" /> -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}" />

    <!-- Helpers -->
    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{asset('assets/js/config.js')}}"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
        {{$slot}}
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>

    <!-- <script src="../../assets/vendor/libs/popper/popper.js"></script> -->
    <!-- <script src="../../assets/vendor/js/bootstrap.js"></script> -->
    <!-- <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script> -->

   <!--  <script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>

    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>

    <script src="../../assets/vendor/js/menu.js"></script> -->

    <!-- endbuild -->

    <!-- Vendors JS -->
    <!-- <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script> -->

    <!-- Main JS -->

    {{-- <script src="{{asset('assets/js/main.js')}}"></script> --}}

    <!-- Page JS -->
    <script src="{{asset('assets/js/pages-auth.js')}}"></script>
    <script src="{{ asset('assets/global/js/notiflix-aio-2.7.0.min.js')}}"></script>
    @stack('js')
    @include('partner.layouts.notification')
    <script>
        "use strict";
        $(".preloader ").fadeOut();
    </script>
  </body>
</html>
