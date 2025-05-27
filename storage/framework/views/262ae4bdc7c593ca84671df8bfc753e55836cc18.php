<?php
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
'admin.apis',
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
//'admin.groups',
'admin.parant',
'admin.workboard',
'admin.users',
'admin.deposit.manual.index',
]);

?>



<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
            <a href="index.html" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <span class="text-primary">

                        <img src="<?php echo e(asset('assets/uploads/logo/logo.png')); ?>" height="50" viewBox="0 0 128 128"
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

                
                <!--/ Language -->

                <!-- Style Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                        <li>
                            <button type="button" class="dropdown-item align-items-center active"
                                data-bs-theme-value="light" aria-pressed="false">
                                <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                                aria-pressed="true">
                                <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                        data-icon="moon-stars"></i>Dark</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                                aria-pressed="false">
                                <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                        data-icon="device-desktop-analytics"></i>System</span>
                            </button>
                        </li>
                    </ul>
                </li>
                <!-- / Style Switcher-->

                <!-- Quick links  -->
                
                <!-- Quick links -->

                <!-- Notification -->
                
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="profile" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">

                            <?php
                            use Illuminate\Support\Facades\File;

                            $user = Auth::user();
                            $imagePath = public_path('uploads/admin/' . $user->image);
                            ?>

                            <?php if(auth()->guard()->check()): ?>
                            <?php if(!empty($user->image) && File::exists($imagePath)): ?>
                            <img src="<?php echo e(asset('public/uploads/admin/' . $user->image)); ?>" alt="<?php echo e($user->name); ?>"
                                class="rounded-circle" />
                            <?php else: ?>
                            <!-- Optional: Show placeholder -->
                            <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt="Default Avatar"
                                class="rounded-circle" />
                            <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="<?php echo e(route('admin.profile')); ?>">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">

                                            <?php
                                            // use Illuminate\Support\Facades\File;

                                            $user = Auth::user();
                                            $imagePath = public_path('uploads/admin/' . $user->image);
                                            ?>

                                            <?php if(auth()->guard()->check()): ?>
                                            <?php if(!empty($user->image) && File::exists($imagePath)): ?>
                                            <img src="<?php echo e(asset('public/uploads/admin/' . $user->image)); ?>"
                                                alt="<?php echo e($user->name); ?>" class="rounded-circle" />
                                            <?php else: ?>
                                            <!-- Optional: Show placeholder -->
                                            <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt="Default Avatar"
                                                class="rounded-circle" />
                                            <?php endif; ?>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?php echo e(auth()->user()->username); ?></h6>
                                        <small class="text-body-secondary"><?php echo e(auth()->user()->email); ?></small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.profile')); ?>">
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My
                                    Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.password')); ?>">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span
                                    class="align-middle">Password</span>
                            </a>
                        </li>
                        
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        
                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                                    <?php echo csrf_field(); ?>
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
                    <li class="menu-item <?php echo e(request()->is('dashboard') ? 'active open' : ''); ?>">
                    <li class="menu-item <?php echo e($isMainActive ? 'active open' : ''); ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon icon-base ti tabler-layout-grid-add"></i>
                            <div data-i18n="Main">Main</div>
                        </a>

                        <ul class="menu-sub">
                            

                            <?php if(adminAccessRoute(config('role.parent_group.access.view'))): ?>
                            
                            <?php endif; ?>
                            <?php if(adminAccessRoute(config('role.work_board.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.workboard' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.workboard')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="WorkBoard">WorkBoard</div>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(adminAccessRoute(config('role.manage_staff.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.users' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.users')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Users">All Users</div>
                                </a>
                            </li>
                            <?php endif; ?>
                            

                            <ul class="menu-sub">
                                <!-- <li class="menu-item <?php echo e(Request::routeIs('admin.accounts.add') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.accounts.add')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Add Accounts">Add Accounts</div>
                                </a>
                            </li> -->
                                <!-- <li class="menu-item <?php echo e(Request::routeIs('admin.accounts') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.accounts')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Accounts">All Accounts</div>
                                </a>
                            </li> -->
                                <li class="menu-item <?php echo e(Request::routeIs('admin.balance.logs') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('admin.balance.logs')); ?>" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="Account Balance">Account Balance</div>
                                    </a>
                                </li>

                                <li class="menu-item <?php echo e(Request::routeIs('admin.ewallet.accounts') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('admin.ewallet.accounts')); ?>" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="E-Wallet Test">E-Wallet Test </div>
                                    </a>
                                </li>

                                <li
                                    class="menu-item <?php echo e(Request::routeIs('admin.ewallet.accounts.details') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('admin.ewallet.accounts.details')); ?>" class="menu-link">
                                        <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                        <div data-i18n="E-Wallet Test"> Test </div>
                                    </a>
                                </li>
                            </ul>
                    </li>


                </ul>
                </li>


                <!-- Layouts -->
                <li class="menu-item <?php echo e($isAccountsActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                        <div data-i18n="Accounts">Accounts</div>
                    </a>

                    <ul class="menu-sub">
                        
                        <?php if(adminAccessRoute(config('role.telegram_group.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.groups' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.groups')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="TelegramGroup">TelegramGroup</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(adminAccessRoute(config('role.account_balance_logs.access.view'))): ?>
                        <li class="menu-item <?php echo e(Request::routeIs('admin.balance.logs') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.balance.logs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Account Balance">Account Balance</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.account_management.access.view'))): ?>
                        <li class="menu-item <?php echo e(Request::routeIs('admin.accounts.management') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.accounts.management')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Account Management">Account Management</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.e_wallet_accounts_test.access.view'))): ?>
                        <li class="menu-item <?php echo e(Request::routeIs('admin.ewallet.accounts') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.ewallet.accounts')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="E-Wallet Test">E-Wallet Test </div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>


                <!-- Apps -->
                <li class="menu-item <?php echo e($isPartnerActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Partner">Partner</div>
                    </a>

                    <ul class="menu-sub">

                        
                        <?php if(adminAccessRoute(config('role.commission_category.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.commission.categories.index' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.commission.categories.index')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Commision Category">Commission Category</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partners.access.edit'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.apis.balance.add.get' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.apis.balance.add')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Add Balance/Adjustment">Add Balance/Adjustment</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.ewallet_transfer_balance.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.transfer.balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Transfer Balance">Transfer Balance</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.settlements.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.settlements', 'admin.settlements.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.settlements')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-calendar"></i>
                                <div data-i18n="Partner Settelment">Partner Settlement</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partnersbalance.access.add'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.apis' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.apis')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Merchant List">Merchant List </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partnersbalance.access.add'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.apis' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.agent.list')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Merchant List">Agent List </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.commissions.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.api.commissions', 'admin.api.post.commissions']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.api.commissions')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Commission">Partner Commission</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.adjustments.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.adjustments' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.adjustments')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Monthly Adjustments ">Monthly Adjustments </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.partner.balance', 'admin.partner.balance.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.partner.balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Adjustments">Adjustments</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.api_logs.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.transections.apilogs' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.transections.apilogs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="API Logs ">API Logs </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                    </ul>
                </li>



                
                <li class="menu-item <?php echo e($isTransactionActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Transactions">Transactions</div>
                    </a>

                    <ul class="menu-sub">
                        <?php if(adminAccessRoute(config('role.payment_log.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.log', 'admin.payment.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.log')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Deposit Log">Deposit Log</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.payout_manage.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payout-log', 'admin.payout-log.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payout-log')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawl Log">Withdrawal Log</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.api_payment_log.access.view'))): ?>
                       <?php if(auth()->user()->username=="dev"): ?>

                       
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.apiLog', 'admin.payment.apisearch']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.apiLog')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Api Deposit Log">Api Deposit Log</div>
                            </a>
                        </li>
                        

                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.apiLogunclaimed', 'admin.payment.apiLogunclaimed.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.apiLogunclaimed')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Unclaimed Payment">Unclaimed Payment</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.deposit_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.report', 'admin.payment.report.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Deposit Report">Deposit Report</div>
                            </a>
                        </li>

                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.report.daily', 'admin.payment.report.daily.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.report.daily')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Daily Deposit Report">Daily Deposit Report</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.all_reports.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.report.all', 'admin.payment.report.all.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.report.all')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="All Report">All Report</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.withdrawal_reports.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payout-report', 'admin.payout-report.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payout-report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawal Report">Withdrawal Report</div>
                            </a>
                        </li>


                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payout.report.daily', 'admin.payout.report.daily.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payout.report.daily')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Daily Withdrawal Report">Daily Withdrawal Report</div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>


                
                <li class="menu-item <?php echo e($isReportsActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Reports">Reports</div>
                    </a>
                    <ul class="menu-sub">
                        <?php if(adminAccessRoute(config('role.live_e_wallet_balance_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.live_ewallet_balance' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.live_ewallet_balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Live E-Wallet Balance">Live E-Wallet Balance</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.daily_e_wallet_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.daily_ewallet_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.daily_ewallet_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily E-Wallet Summary">Daily E-Wallet Summary </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.daily_transaction_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.daily_transection_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.daily_transection_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily Transection Summary">Daily Transection Summary </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.merchant_charges_summary_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.reports.merchant_charges_summary', 'admin.reports.merchant_charges_summary.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.merchant_charges_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Merchant Charges Summary">Merchant Charges Summary</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Summary">Partner Account Summary </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_balance_summary_creation.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_balance_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_balance_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Creations">Partner Account Balance
                                    Summary Creations </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_balance_summary_completions.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_balance_summary_completions' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_balance_summary_completions')); ?>"
                                class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Completions">Partner Account
                                    Balance Summary Completions </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.revenue_center_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.revenue_center' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.revenue_center')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Revenue Center">Revenue Center </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance_log.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.logs' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.logs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance Logs">Partner Balance Logs </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance_reports.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.cal' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.cal')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R1">Partner Balance R1 </div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.cal2' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.cal2')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R2">Partner Balance R2 </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.master_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.master_report' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.master_report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Master Report">Master Report </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.gateway_performance_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.payment.payment_gateway_report' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.payment_gateway_report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Gateway Performance Report">Gateway Performance Report </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.payment_type.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.type' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.type')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Payment Type">Payment Type </div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if(adminAccessRoute(config('role.merchant_reports.access.view'))): ?>
                <li class="menu-item <?php echo e($isMerchantReportsActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Merchant Reports">Merchant Reports</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_date' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_date')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Date">Summary By Date</div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_name' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_name')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Name">Summary By Name </div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_month' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_month')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Year">Summary By Year </div>
                            </a>
                        </li>

                    </ul>
                </li>
                <?php endif; ?>
                </ul>
            </div>
        </aside>
        <!-- / Menu -->

        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <!-- Pricing Plans -->
                <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>



                <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>

                <?php echo e($slot); ?>

            </div>
        </div>
<?php /**PATH C:\xampp\htdocs\subecpaypast\resources\views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>