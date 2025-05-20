@php
$isAccountsActive =
Request::routeIs('admin.accounts.add') ||
Request::routeIs('admin.accounts') ||
Request::routeIs('admin.balance.logs');

$isMainActive = in_array(Route::currentRouteName(), [
'partner.apis',
'partner.dashboard',
'partner.settlements',
'partner.partner.balance',
'partner.partner.balance.search',
'partner.api.commissions',
'partner.settlements.search',
'partner.partner.methods.get',
'partner.settlement.report.daily',
'partner.reports.log_completions',
'partner.payment.payment_gateway_report',
]);

$isReportsActive = in_array(Route::currentRouteName(), [
'partner.payment.report.all',

'partner.reports.partner_account_summary',
'partner.reports.partner_account_balance_summary',
]);
$isTransactionActive = in_array(Route::currentRouteName(), [
'partner.reports.logs',
'partner.payout-report',
'partner.payout-report.search',
'partner.payment.report',
'partner.payment.report.search',
'partner.payout.report.daily',
'partner.payout.report.daily.search',
'partner.payment.report.daily',
'partner.payment.report.daily.search',
]);
$isMerchantReportsActive = in_array(Route::currentRouteName(), [
'partner.merchant_reports.by_date',
'partner.merchant_reports.by_name',
'partner.merchant_reports.by_month',
]);

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



                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="profile" data-bs-toggle="dropdown">
                        {{-- <div class="avatar avatar-online">
                            <img src="{{ asset('public/uploads/admin/' . Auth::user()->image) }}"
                                alt="{{ Auth::user()->name }}" class="rounded-circle" />
                        </div> --}}
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
                                            {{-- <img src="{{ asset('public/uploads/admin/' . Auth::user()->image) }}"
                                                alt="{{ Auth::user()->name }}" class="rounded-circle" /> --}}
                                            @php

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
                            <a class="dropdown-item" href="{{ route('partner.profile') }}">
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('partner.password') }}">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span
                                    class="align-middle">Password</span>
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
                                <form method="POST" action="{{ route('partner.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-danger btn-block d-flex align-items-center">
                                        <small class="align-middle">Logout</small>
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
                            <div data-i18n="Main">Main</div>
                        </a>

                        <ul class="menu-sub">
                            @if(partnerAccessRoute(config('rolep.dashboard.access.view')))
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.dashboard' ? 'active' : '' }}">
                                <a href="{{ route('partner.dashboard') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Dashboards">Dashboard</div>
                                </a>
                            </li>
                            @endif

                            @if(partnerAccessRoute(config('rolep.manage_staff.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'partner.staff' ? 'active' : '' }}">
                                <a href="{{ route('partner.staff') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Staff">Staff</div>
                                </a>
                            </li>
                            @endif


                            @if(partnerAccessRoute(config('rolep.manage_staff.access.view')))
                            <li class="menu-item {{ Route::currentRouteName() == 'partner.apis' ? 'active' : '' }}">
                                <a href="{{ route('partner.apis') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Commissions Summary">Commissions Summary</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.api.commissions' ? 'active' : '' }}">
                                <a href="{{ route('partner.api.commissions') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Commission History">Commission Report</div>
                                </a>
                            </li>
                            @endif
                            @if(Auth::guard('partner')->user()->type=="Admin")
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.settlements' ? 'active' : '' }}">
                                <a href="{{ route('partner.settlements') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Settlements">Settlements</div>
                                </a>
                            </li>

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.settlement.report.daily' ? 'active' : '' }}">
                                <a href="{{ route('partner.settlement.report.daily') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Daily Settlement Report">Daily Settlement Report</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ in_array(Route::currentRouteName(), ['partner.partner.balance', 'partner.partner.balance.search']) ? 'active' : '' }}">
                                <a href="{{ route('partner.partner.balance') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Adjustments">Adjustments</div>
                                </a>
                            </li>
                            @endif

                            {{-- <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.reports.partner_account_balance_summary' ? 'active' : '' }}">
                                <a href="{{ route('partner.reports.partner_account_balance_summary') }}"
                                    class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Account Balance">Account Balance </div>
                                </a>
                            </li> --}}

                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.reports.log_completions' ? 'active' : '' }}">
                                <a href="{{ route('partner.reports.log_completions') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Transactions Completions Logs">Transactions Completions Logs</div>
                                </a>
                            </li>
                            <li
                                class="menu-item {{ Route::currentRouteName() == 'partner.payment.payment_gateway_report' ? 'active' : '' }}">
                                <a href="{{ route('partner.payment.payment_gateway_report') }}" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                    <div data-i18n="Gateway Performance Report">Gateway Performance Report</div>
                                </a>
                            </li>







                    </li>
                </ul>
                </li>


                <li class="menu-item {{ $isReportsActive ? 'active open' : '' }}">

                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                        <div data-i18n="Reports">Reports</div>
                    </a>
                    <ul class="menu-sub">

                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['partner.payment.report.all', 'partner.payment.report.all.search']) ? 'active' : '' }}">
                            <a href="{{ route('partner.payment.report.all') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="All Reports">All Reports</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.reports.partner_account_balance_summary' ? 'active' : '' }}">
                            <a href="{{ route('partner.reports.partner_account_balance_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Account Balance">Account Balance</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.reports.partner_account_summary' ? 'active' : '' }}">
                            <a href="{{ route('partner.reports.partner_account_summary') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Account Summary">Account Summary</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item {{ $isTransactionActive ? 'active open' : '' }}">

                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                        <div data-i18n="Transactions">Transaction</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ Route::currentRouteName() == 'partner.reports.logs' ? 'active' : '' }}">
                            <a href="{{ route('partner.reports.logs') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Transaction Logs">Transaction Logs</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['partner.payment.report', 'partner.payment.report.search']) ? 'active' : '' }}">
                            <a href="{{ route('partner.payment.report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Deposit Report">Deposit Report</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['partner.payment.report.daily', 'partner.payment.report.daily.search']) ? 'active' : '' }}">
                            <a href="{{ route('partner.payment.report.daily') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Daily Deposit Report">Daily Deposit Report</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.payout-request' ? 'active' : '' }}">
                            <a href="{{ route('partner.payout-request') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Withdrawal Request">Withdrawal Request</div>
                            </a>
                        </li>

                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['partner.payout-report', 'partner.payout-report.search']) ? 'active' : '' }}">
                            <a href="{{ route('partner.payout-report') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Withdrawal Report">Withdrawal Report</div>
                            </a>
                        </li>


                        <li
                            class="menu-item {{ in_array(Route::currentRouteName(), ['partner.payout.report.daily', 'partner.payout.report.daily.search']) ? 'active' : '' }}">
                            <a href="{{ route('partner.payout.report.daily') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                                <div data-i18n="Daily Withdrawal Report">Daily Withdrawal Report</div>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="menu-item {{ $isMerchantReportsActive ? 'active open' : '' }}">

                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                        <div data-i18n="Merchant Reports">Merchant Reports</div>
                    </a>
                    <ul class="menu-sub">


                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_date' ? 'active' : '' }}">
                            <a href="{{ route('partner.merchant_reports.by_date') }}" class="menu-link">
                                <div data-i18n=" ">Summary By_Date</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_name' ? 'active' : '' }}">
                            <a href="{{ route('partner.merchant_reports.by_name') }}" class="menu-link">
                                <div data-i18n="Summary By_Name">Summary By_Name</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Route::currentRouteName() == 'partner.merchant_reports.by_month' ? 'active' : '' }}">
                            <a href="{{ route('partner.merchant_reports.by_month') }}" class="menu-link">
                                <div data-i18n="Summary By_Year">Summary By_Year</div>
                            </a>
                        </li>

                    </ul>
                </li>



            </div>
        </aside>
        <!-- / Menu -->

        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <!-- Pricing Plans -->
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
