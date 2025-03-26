<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title>{{ $title ? 'ECPay System - ' . $title : 'ECPay System' }}</title>



    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon/favicon.ico')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    {{-- @vite(['resources/js/app.js']) --}}
    <script type="module" src="{{ asset('public/build/assets/app-Bkr_ShsC.js') }}"></script>

    <!-- Helpers -->
    {{-- <script src="{{asset('assets/vendor/js/helpers.js')}}"></script> --}}
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    {{-- <script src="{{asset('assets/js/config.js')}}"></script> --}}
    @stack('styles')
</head>

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            {{-- Sidebar --}}
            @include('admin.layouts.sidebar')

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="ti tabler-menu icon-base"></i>
                    <i class="ti tabler-chevron-right icon-base"></i>
                </a>
            </div>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base ti tabler-menu-2 icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-sun icon-md theme-icon-active"></i>
                                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="nav-theme-text">
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center active"
                                            data-bs-theme-value="light" aria-pressed="false">
                                            <span><i class="icon-base ti tabler-sun icon-md me-3"
                                                    data-icon="sun"></i>Light</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="dark" aria-pressed="true">
                                            <span><i class="icon-base ti tabler-moon-stars icon-md me-3"
                                                    data-icon="moon-stars"></i>Dark</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item align-items-center"
                                            data-bs-theme-value="system" aria-pressed="false">
                                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-md me-3"
                                                    data-icon="device-desktop-analytics"></i>System</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    {{-- <div class="avatar avatar-online">
                                        <img src="{{ asset('public/uploads/admin/' . auth()->user()->image) }}"
                                        alt="Profile Picture"
                                        class="w-px-40 h-auto rounded-circle" />

                                    </div> --}}
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('public/uploads/admin/' . auth()->user()->image) }}"
                                        alt="Profile Picture"
                                        class="w-px-40 h-auto rounded-circle" />

                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    {{-- <div class="avatar avatar-online">
                                                        <img src="{{ asset('public/uploads/admin/' . auth()->user()->image) }}"
     alt="Profile Picture"
     class="w-px-40 h-auto rounded-circle" />

                                                    </div> --}}
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('public/uploads/admin/' . auth()->user()->image) }}"
                                                        alt="Profile Picture"
                                                        class="w-px-40 h-auto rounded-circle" />

                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ auth()->user()->username }}</h6>
                                                    <small class="text-body-secondary">{{ auth()->user()->email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{route('admin.profile') }}">
                                            <i class="icon-base ti tabler-user icon-md me-3"></i><span>My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{route('admin.password') }}">
                                            <i
                                                class="icon-base ti tabler-settings icon-md me-3"></i><span>Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <span class="d-flex align-items-center align-middle">
                                                <i
                                                    class="flex-shrink-0 icon-base ti tabler-credit-card icon-md me-3"></i><span
                                                    class="flex-grow-1 align-middle">Billing Plan</span>
                                                <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    {{-- <li>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="icon-base ti tabler-power icon-md me-3"></i><span>Log Out</span>
                                        </a>
                                    </li> --}}

                                    <li>
                                        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item" style="background: none; border: none;">
                                                <i class="icon-base ti tabler-power icon-md me-3"></i><span>Log Out</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    {{$slot}}
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    , made with ❤️ by <a href="https://pixinvent.com" target="_blank"
                                        class="footer-link">Pixinvent</a>
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
                                        target="_blank" class="footer-link me-4">Documentation</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        {{-- <div class="layout-overlay layout-menu-toggle"></div> --}}
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        {{-- <div class="drag-target"></div> --}}
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/node-waves/node-waves.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/@algolia/autocomplete-js.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/hammer/hammer.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/i18n/i18n.js')}}"></script>

    <script src="{{asset('assets/vendor/js/menu.js')}}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    {{-- <script src="{{asset('assets/js/main.js')}}"></script> --}}

    <!-- Page JS -->
    @stack('js')
</body>

</html>
