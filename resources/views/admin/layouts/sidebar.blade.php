@php
// $isAccountsActive =
// // Request::routeIs('admin.accounts.add') ||
// Request::routeIs('admin.accounts') ||
// Request::routeIs('admin.groups') ||
// Request::routeIs('admin.accounts.management') ||
// Request::routeIs('admin.balance.logs');

$isPartnerActive = in_array(Route::currentRouteName(), [
'admin.apis.balance.add.get',
'admin.transfer.balance',
'admin.settlements',
'admin.settlements.search',
'admin.api.commissions',
'admin.api.post.commissions',
'admin.adjustments',
'admin.adjustments.search',
'admin.partner.balance',
'admin.partner.balance.search',
'admin.transections.apilogs',
'admin.commission.categories.index',
]);
$isReportsActive = in_array(Route::currentRouteName(), [
'admin.reports.live_ewallet_balance',
'admin.reports.daily_ewallet_summary',
'admin.reports.daily_transection_summary',
'admin.reports.merchant_charges_summary',
'admin.reports.merchant_charges_summary.search',
'admin.reports.partner_account_summary',
'admin.reports.partner_account_balance_summary',
'admin.payment.payment_gateway_report',
'admin.reports.partner_account_balance_summary_completions',
'admin.reports.revenue_center',
'admin.reports.logs',
'admin.reports.cal',
'admin.reports.cal2',
'admin.reports.master_report',
'admin.payment_gateway_performance_report',

'admin.type',
]);
// $isMerchantReportsActive = in_array(Route::currentRouteName(), [
// 'partner.merchant_reports.by_date',
// 'partner.merchant_reports.by_name',
// 'partner.merchant_reports.by_month'

// ]);

$isMerchantReportsActive = in_array(Route::currentRouteName(), [
'admin.merchant_reports.by_date',
'admin.merchant_reports.by_name',
'admin.merchant_reports.by_month',
]);

$isTransactionActive = in_array(Route::currentRouteName(), [
'admin.payment.log',
'admin.payment.search',
'admin.payout-log',
'admin.payout-log.search',
'admin.payment.apiLog',
'admin.payment.apisearch',
'admin.payment.apiLogunclaimed',
'admin.payment.apiLogunclaimed.search',
'admin.payment.report',
'admin.payment.report.search',
'admin.payment.report.daily',
'admin.payment.report.daily.search',
'admin.payment.report.all',
'admin.payment.report.all.search',
'admin.payout-report',
'admin.payout-report.search',
'admin.payout.report.daily',
'admin.payout.report.daily.search',
]);
// $isAccountsActive =
// Request::routeIs('admin.accounts.add') ||
// Request::routeIs('admin.accounts') ||
// Request::routeIs('admin.balance.logs') ||
// Request::routeIs('');

$isAccountsActive =
Request::routeIs('admin.groups') ||
Request::routeIs('admin.accounts.add') ||
Request::routeIs('admin.accounts') ||
Request::routeIs('admin.balance.logs') ||
Request::routeIs('admin.accounts.management') ||
Request::routeIs('admin.ewallet.accounts') ||
Request::routeIs('');

$isMainActive = in_array(Route::currentRouteName(), [
'admin.dashboard',
'admin.staff',
'admin.apis',
'admin.agent.list',
'admin.parant',
'admin.workboard',
'admin.users',
'admin.deposit.manual.index',
]);

use Illuminate\Support\Facades\App;

$currentLocale = App::getLocale();
$languages = [
'en' => 'English',
//'ms' => 'Malaysian',
'cn' =>'Chinese'
];

@endphp



<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
            <a href="index.html" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <span class="text-primary">

                        <img src="{{ asset('assets/uploads/logo/logo.png') }}" height="50" viewBox="0 0 128 128"
                            fill="none" alt="ECPay logo">
                    </span>
                </span>
                {{-- <span class="app-brand-text demo menu-text fw-bold text-heading">Vuexy</span> --}}
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base ti tabler-menu-2 icon-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                <!-- Search -->
                <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
                    </a>
                </li>
                <!-- /Search -->

                <!-- Language -->
                @if (adminAccessRoute(config('role.language.access.view')))
                <li class="nav-item dropdown-language dropdown">
                    {{-- <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        --}} <a class="nav-link dropdown-toggle btn btn-text-secondary rounded-pill" href="#"
                        data-bs-toggle="dropdown">
                        {{ $languages[$currentLocale] ?? 'Select Language' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($languages as $code => $label)
                        <li>
                            <a class="dropdown-item" href="{{ route('lang.switch', ['locale' => $code]) }}"
                                data-language="{{ $code }}" data-text-direction="ltr">
                                <span>{{ $label }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                <!--/ Language -->
                @endif


                <!-- Style Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                        <span class="d-none ms-2" id="nav-theme-text">{{ __('sidebar.toggle_theme') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                        <li>
                            <button type="button" class="dropdown-item align-items-center active"
                                data-bs-theme-value="light" aria-pressed="false">
                                <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>{{
                                    __('sidebar.light') }}</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                                aria-pressed="true">
                                <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                        data-icon="moon-stars"></i>{{ __('sidebar.dark') }}</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                                aria-pressed="false">
                                <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                        data-icon="device-desktop-analytics"></i>{{ __('sidebar.system') }}</span>
                            </button>
                        </li>
                    </ul>
                </li>
                <!-- / Style Switcher-->

                <!-- Quick links  -->
                {{-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h6 class="mb-0 me-auto">Shortcuts</h6>
                                <a href="javascript:void(0)"
                                    class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i
                                        class="icon-base ti tabler-plus icon-20px text-heading"></i></a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-calendar icon-26px text-heading"></i>
                                    </span>
                                    <a href="app-calendar.html" class="stretched-link">Calendar</a>
                                    <small>Appointments</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-file-dollar icon-26px text-heading"></i>
                                    </span>
                                    <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
                                    <small>Manage Accounts</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-user icon-26px text-heading"></i>
                                    </span>
                                    <a href="app-user-list.html" class="stretched-link">User App</a>
                                    <small>Manage Users</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-users icon-26px text-heading"></i>
                                    </span>
                                    <a href="app-access-roles.html" class="stretched-link">Role Management</a>
                                    <small>Permission</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i
                                            class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"></i>
                                    </span>
                                    <a href="index.html" class="stretched-link">Dashboard</a>
                                    <small>User Dashboard</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
                                    </span>
                                    <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
                                    <small>Account Settings</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-help-circle icon-26px text-heading"></i>
                                    </span>
                                    <a href="pages-faq.html" class="stretched-link">FAQs</a>
                                    <small>FAQs & Articles</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                        <i class="icon-base ti tabler-square icon-26px text-heading"></i>
                                    </span>
                                    <a href="modal-examples.html" class="stretched-link">Modals</a>
                                    <small>Useful Popups</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                <!-- Quick links -->

                <!-- Notification -->
                {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <span class="position-relative">
                            <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h6 class="mb-0 me-auto">Notification</h6>
                                <div class="d-flex align-items-center h6 mb-0">
                                    <span class="badge bg-label-primary me-2">8 New</span>
                                    <a href="javascript:void(0)" class="dropdown-notifications-all p-2 btn btn-icon"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i
                                            class="icon-base ti tabler-mail-opened text-heading"></i></a>
                                </div>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="small mb-1">Congratulation Lettie 🎉</h6>
                                            <small class="mb-1 d-block text-body">Won the monthly best seller gold
                                                badge</small>
                                            <small class="text-body-secondary">1h ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Charles Franklin</h6>
                                            <small class="mb-1 d-block text-body">Accepted your connection</small>
                                            <small class="text-body-secondary">12hr ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/2.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">New Message ✉️</h6>
                                            <small class="mb-1 d-block text-body">You have new message from
                                                Natalie</small>
                                            <small class="text-body-secondary">1h ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="icon-base ti tabler-shopping-cart"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Whoo! You have new order 🛒</h6>
                                            <small class="mb-1 d-block text-body">ACME Inc. made new order
                                                $1,154</small>
                                            <small class="text-body-secondary">1 day ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/9.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Application has been approved 🚀</h6>
                                            <small class="mb-1 d-block text-body">Your ABC project application has been
                                                approved.</small>
                                            <small class="text-body-secondary">2 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="icon-base ti tabler-chart-pie"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Monthly report is generated</h6>
                                            <small class="mb-1 d-block text-body">July monthly financial report is
                                                generated </small>
                                            <small class="text-body-secondary">3 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/5.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">Send connection request</h6>
                                            <small class="mb-1 d-block text-body">Peter sent you connection
                                                request</small>
                                            <small class="text-body-secondary">4 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/6.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">New message from Jane</h6>
                                            <small class="mb-1 d-block text-body">Your have new message from
                                                Jane</small>
                                            <small class="text-body-secondary">5 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                                        class="icon-base ti tabler-alert-triangle"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 small">CPU is running high</h6>
                                            <small class="mb-1 d-block text-body">CPU Utilization Percent is currently
                                                at 88.63%,</small>
                                            <small class="text-body-secondary">5 days ago</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="icon-base ti tabler-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="border-top">
                            <div class="d-grid p-4">
                                <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                    <small class="align-middle">View all notifications</small>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li> --}}
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="profile" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">

                            @php
                            use Illuminate\Support\Facades\File;

                            $user = Auth::user();
                            $imagePath = public_path('uploads/admin/' . $user->image);
                            @endphp

                            @auth
                            @if (!empty($user->image) && File::exists($imagePath))
                            <img src="{{ asset('public/uploads/admin/' . $user->image) }}" alt="{{ $user->name }}"
                                class="rounded-circle" />
                            @else
                            <!-- Optional: Show placeholder -->
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Default Avatar"
                                class="rounded-circle" />
                            @endif
                            @endauth

                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="{{ route('admin.profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">

                                            @php
                                            // use Illuminate\Support\Facades\File;

                                            $user = Auth::user();
                                            $imagePath = public_path('uploads/admin/' . $user->image);
                                            @endphp

                                            @auth
                                            @if (!empty($user->image) && File::exists($imagePath))
                                            <img src="{{ asset('public/uploads/admin/' . $user->image) }}"
                                                alt="{{ $user->name }}" class="rounded-circle" />
                                            @else
                                            <!-- Optional: Show placeholder -->
                                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Default Avatar"
                                                class="rounded-circle" />
                                            @endif
                                            @endauth

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
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">{{
                                    __('sidebar.my_profile') }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.password') }}">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span class="align-middle">{{
                                    __('sidebar.password') }}</span>
                            </a>
                        </li>
                        {{-- <li>
                            <a class="dropdown-item" href="pages-account-settings-billing.html">
                                <span class="d-flex align-items-center align-middle">
                                    <i class="flex-shrink-0 icon-base ti tabler-file-dollar me-3 icon-md"></i><span
                                        class="flex-grow-1 align-middle">Billing Plan</span>
                                    <span
                                        class="flex-shrink-0 badge bg-danger d-flex align-items-center justify-content-center">4</span>
                                </span>
                            </a>
                        </li> --}}
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        {{-- <li>
                            <a class="dropdown-item" href="pages-pricing.html">
                                <i class="icon-base ti tabler-currency-dollar me-3 icon-md"></i><span
                                    class="align-middle">Pricing</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="pages-faq.html">
                                <i class="icon-base ti tabler-question-mark me-3 icon-md"></i><span
                                    class="align-middle">FAQ</span>
                            </a>
                        </li> --}}
                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-danger btn-block d-flex align-items-center">
                                        <small class="align-middle">{{ __('sidebar.logout') }}</small>
                                        <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>

<div class="layout-page">
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
            <div class="container-xxl d-flex h-100">
                <ul class="menu-inner">
                    <!-- Dashboards -->
                    <li class="menu-item {{ request()->is('dashboard') ? 'active open' : '' }}">
                    <li class="menu-item {{ $isMainActive ? 'active open' : '' }}">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                            <div data-i18n="Main">{{ __('sidebar.main') }}</div>
                        </a>

                        <ul class="menu-sub">
                            @if (adminAccessRoute(config('role.work_board.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.workboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.workboard') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="WorkBoard">{{ __('sidebar.workBoard') }}</div>
                                </a>
                            </li>
                            @endif
                            @if (adminAccessRoute(config('role.partners.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.apis' ? 'active' : '' }}">
                                <a href="{{ route('admin.apis') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Merchant List">{{ __('sidebar.merchant_management') }}</div>
                                </a>
                            </li>
                            @endif
                            @if (adminAccessRoute(config('role.agents.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.agent.list' ? 'active' : '' }}">
                                <a href="{{ route('admin.agent.list') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Merchant List">{{ __('sidebar.agent_management') }} </div>
                                </a>
                            </li>
                            @endif

                            {{-- <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Dashboards">Dashboard</div>
                                </a>
                            </li> --}}

                            {{-- @if (adminAccessRoute(config('role.parent_group.access.view'))) --}}
                            {{-- <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.parant' ? 'active' : '' }}">
                                <a href="{{ route('admin.parant') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Partner Group">Partner Group</div>
                                </a>
                            </li> --}}
                            {{-- @endif --}}

                            @if (adminAccessRoute(config('role.manage_staff.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
                                <a href="{{ route('admin.users') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Users">{{ __('sidebar.user_management') }}</div>
                                </a>
                            </li>
                            @endif
                            {{-- <li
                                class="menu-item {{ Route::currentRouteName() == 'admin.deposit.manual.index' ? 'active' : '' }}">
                                <a href="{{ route('admin.deposit.manual.index') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Manual Gateway">Manual Gateway</div>
                                </a>
                            </li> --}}

                            <ul class="menu-sub">
                                <!-- <li class="menu-item {{ Request::routeIs('admin.accounts.add') ? 'active' : '' }}">
                                <a href="{{ route('admin.accounts.add') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </li> -->
                                <!-- <li class="menu-item {{ Request::routeIs('admin.accounts') ? 'active' : '' }}">
                                <a href="{{ route('admin.accounts') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Accounts">All Accounts</div>
                                </a>
                            </li> -->
                                {{-- <li class="menu-item {{ Request::routeIs('admin.balance.logs') ? 'active' : '' }}">
                                    <a href="{{ route('admin.balance.logs') }}" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="Account Balance">Account Balance</div>
                                    </a>
                                </li> --}}

                                <li class="menu-item {{ Request::routeIs('admin.ewallet.accounts') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ewallet.accounts') }}" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="E-Wallet Test">E-Wallet Test </div>
                                    </a>
                                </li>

                                <li
                                    class="menu-item {{ Request::routeIs('admin.ewallet.accounts.details') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ewallet.accounts.details') }}" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="E-Wallet Test"> Test </div>
                                    </a>
                                </li>
                            </ul>
                    </li>


                </ul>
                </li>


                <!-- Layouts -->
                <li class="menu-item {{ $isAccountsActive ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                        <div data-i18n="Accounts">{{ __('sidebar.accounts') }}</div>
                    </a>

                    <ul class="menu-sub">
                        {{-- <li class="menu-item {{ Request::routeIs('admin.accounts.add') ? 'active' : '' }}">
                            <a href="{{ route('admin.accounts.add') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Add Accounts">Add Accounts</div>
                            </a>
                        </li> --}}
                        @if (adminAccessRoute(config('role.telegram_group.access.view')))
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.groups' ? 'active' : '' }}">
                            <a href="{{ route('admin.groups') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="TelegramGroup">{{ __('sidebar.telegramGroup') }}</div>
                            </a>
                        </li>
                        @endif
                        {{-- <li class="menu-item {{ Request::routeIs('admin.accounts') ? 'active' : '' }}">
                            <a href="{{ route('admin.accounts') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="All Accounts">All Accounts</div>
                            </a>
                        </li> --}}
                        {{-- @if (adminAccessRoute(config('role.account_balance_logs.access.view'))) --}}
                        {{-- <li class="menu-item {{ Request::routeIs('admin.balance.logs') ? 'active' : '' }}">
                            <a href="{{ route('admin.balance.logs') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Account Balance">Account Balance</div>
                            </a>
                        </li> --}}
                        {{-- @endif --}}
                        @if (adminAccessRoute(config('role.account_management.access.view')))
                        <li class="menu-item {{ Request::routeIs('admin.accounts.management') ? 'active' : '' }}">
                            <a href="{{ route('admin.accounts.management') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Account Management">{{ __('sidebar.account_management') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.e_wallet_accounts_test.access.view')))
                        <li class="menu-item {{ Request::routeIs('admin.ewallet.accounts') ? 'active' : '' }}">
                            <a href="{{ route('admin.ewallet.accounts') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="E-Wallet Test">{{ __('sidebar.e_wallet_test') }} </div>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>


                <!-- Apps -->
                <li class="menu-item {{ $isPartnerActive ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Partner">{{ __('sidebar.partner') }}</div>
                    </a>

                    <ul class="menu-sub">

                        {{-- <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.apis.balance.add.get' ? 'active' : '' }}">
                            <a href="{{ route('admin.apis.balance.add') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Manage Commision">Manage Commision</div>
                            </a>
                        </li> --}}
                        @if (adminAccessRoute(config('role.commission_category.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.commission.categories.index' ? 'active' : '' }}">
                            <a href="{{ route('admin.commission.categories.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Commision Category">{{ __('sidebar.commission_category') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partners.access.edit')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.apis.balance.add.get' ? 'active' : '' }}">
                            <a href="{{ route('admin.apis.balance.add') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Add Balance/Adjustment">{{ __('sidebar.add_balance_adjustment') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.ewallet_transfer_balance.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : '' }}">
                            <a href="{{ route('admin.transfer.balance') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Transfer Balance">{{ __('sidebar.transfer_balance') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.settlements.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.settlements', 'admin.settlements.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.settlements') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-calendar"></i>
                                <div data-i18n="Partner Settelment">{{ __('sidebar.partner_settlement') }}</div>
                            </a>
                        </li>
                        @endif


                        @if (adminAccessRoute(config('role.commissions.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.api.commissions', 'admin.api.post.commissions']) ? 'active' : '' }}">
                            <a href="{{ route('admin.api.commissions') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Commission">{{ __('sidebar.partner_commission') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.adjustments.access.view')))
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.adjustments' ? 'active' : '' }}">
                            <a href="{{ route('admin.adjustments') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Monthly Adjustments ">{{ __('sidebar.monthly_adjustments') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_balance.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.partner.balance', 'admin.partner.balance.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.partner.balance') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Adjustments">{{ __('sidebar.adjustments') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.api_logs.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.transections.apilogs' ? 'active' : '' }}">
                            <a href="{{ route('admin.transections.apilogs') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="API Logs ">{{ __('sidebar.api_logs') }}</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.transections.functionlogs' ? 'active' : '' }}">
                            <a href="{{ route('admin.transections.functionlogs') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="API Logs ">Function Logs </div>
                            </a>
                        </li>
                        @endif
                        {{-- <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : '' }}">
                            <a href="{{ route('admin.transfer.balance') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Transfer Balance">Transfer Balance</div>
                            </a>
                        </li> --}}
                    </ul>
                </li>



                {{-- Transaction --}}
                <li class="menu-item {{ $isTransactionActive ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Transactions">{{ __('sidebar.transactions') }}</div>
                    </a>

                    <ul class="menu-sub">
                        @if (adminAccessRoute(config('role.payment_log.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.log', 'admin.payment.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.log') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Deposit Log">{{ __('sidebar.deposit_log') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.deposit_last_hour_report.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.log2']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.log2') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Deposit log2">{{ __('sidebar.deposit_log') }} (Last Hour)</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.payout_manage.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payout-log', 'admin.payout-log.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payout-log') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawl Log">{{ __('sidebar.withdrawal_log') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.api_payment_log.access.view')))
                        @if (auth()->user()->username == 'dev')
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.apiLog', 'admin.payment.apisearch']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.apiLog') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Api Deposit Log">{{ __('sidebar.api_deposit_log') }}</div>
                            </a>
                        </li>


                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.apiLogunclaimed', 'admin.payment.apiLogunclaimed.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.apiLogunclaimed') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Unclaimed Payment">{{ __('sidebar.unclaimed_payment') }}</div>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (adminAccessRoute(config('role.deposit_report.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.report', 'admin.payment.report.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Deposit Report">{{ __('sidebar.deposit_report') }}</div>
                            </a>
                        </li>

                        {{-- <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.report.daily', 'admin.payment.report.daily.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.report.daily') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Daily Deposit Report">Daily Deposit Report</div>
                            </a>
                        </li> --}}
                        @endif
                        @if (adminAccessRoute(config('role.all_reports.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payment.report.all', 'admin.payment.report.all.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.report.all') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="All Report">{{ __('sidebar.all_report') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.withdrawal_reports.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payout-report', 'admin.payout-report.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payout-report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawal Report">{{ __('sidebar.withdrawal_report') }}</div>
                            </a>
                        </li>


                        {{-- <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.payout.report.daily', 'admin.payout.report.daily.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.payout.report.daily') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Daily Withdrawal Report">Daily Withdrawal Report</div>
                            </a>
                        </li> --}}
                        @endif
                    </ul>
                </li>


                {{-- rehan reports --}}
                <li class="menu-item {{ $isReportsActive ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Reports">{{ __('sidebar.reports') }}</div>
                    </a>
                    <ul class="menu-sub">
                        @if (adminAccessRoute(config('role.live_e_wallet_balance_report.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.live_ewallet_balance' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.live_ewallet_balance') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Live E-Wallet Balance">{{ __('sidebar.live_ewallet_balance') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.daily_e_wallet_summary.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.daily_ewallet_summary' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.daily_ewallet_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily E-Wallet Summary">{{ __('sidebar.daily_ewallet_summary') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.daily_transaction_summary.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.daily_transection_summary' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.daily_transection_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily Transection Summary">
                                    {{ __('sidebar.daily_transection_summary') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.merchant_charges_summary_report.access.view')))
                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['admin.reports.merchant_charges_summary', 'admin.reports.merchant_charges_summary.search']) ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.merchant_charges_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Merchant Charges Summary">
                                    {{ __('sidebar.merchant_charges_summary') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_account_summary.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_summary' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.partner_account_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Summary">
                                    {{ __('sidebar.partner_account_summary') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_account_balance_summary_creation.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_balance_summary' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.partner_account_balance_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Creations">
                                    {{ __('sidebar.partner_account_balance_summary_creation') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_account_balance_summary_completions.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.partner_account_balance_summary_completions' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.partner_account_balance_summary_completions') }}"
                                class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Completions">
                                    {{ __('sidebar.partner_account_balance_summary_completions') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.revenue_center_report.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.revenue_center' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.revenue_center') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Revenue Center"> {{ __('sidebar.revenue_center') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_balance_log.access.view')))
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.reports.logs' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.logs') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance Logs">{{ __('sidebar.partner_balance_log') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.partner_balance_reports.access.view')))
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.reports.cal' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.cal') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R1">{{ __('sidebar.partner_balance_report1') }}
                                </div>
                            </a>
                        </li>
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.reports.cal2' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.cal2') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R2">{{ __('sidebar.partner_balance_report2') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.master_report.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.reports.master_report' ? 'active' : '' }}">
                            <a href="{{ route('admin.reports.master_report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Master Report">{{ __('sidebar.master_report') }}
                                </div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.gateway_performance_report.access.view')))
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.payment.payment_gateway_report' ? 'active' : '' }}">
                            <a href="{{ route('admin.payment.payment_gateway_report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Gateway Performance Report">
                                    {{ __('sidebar.gateway_performance_report') }}</div>
                            </a>
                        </li>
                        @endif
                        @if (adminAccessRoute(config('role.payment_type.access.view')))
                        <li class="menu-item {{ Route::currentRouteName() == 'admin.type' ? 'active' : '' }}">
                            <a href="{{ route('admin.type') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Payment Type">{{ __('sidebar.payment_type') }}</div>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @if (adminAccessRoute(config('role.merchant_reports.access.view')))
                <li class="menu-item {{ $isMerchantReportsActive ? 'active open' : '' }}">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Merchant Reports">{{ __('sidebar.merchant_reports') }}</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_date' ? 'active' : '' }}">
                            <a href="{{ route('admin.merchant_reports.by_date') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Date">{{ __('sidebar.summary_by_date') }}</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_name' ? 'active' : '' }}">
                            <a href="{{ route('admin.merchant_reports.by_name') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Name">{{ __('sidebar.summary_by_name') }}</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'admin.merchant_reports.by_month' ? 'active' : '' }}">
                            <a href="{{ route('admin.merchant_reports.by_month') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Year">{{ __('sidebar.summary_by_year') }}</div>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif
                </ul>
            </div>
        </aside>
        <!-- / Menu -->

        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <!-- Pricing Plans -->
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                        </li>
                    </ul>
                </div>
                @endif
                {{-- ======= --}}
                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif



                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                {{ $slot }}
            </div>
        </div>
