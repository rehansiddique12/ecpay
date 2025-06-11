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
'ms' => 'Malaysian',
'cn' =>'Chinese'
];

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

                <!-- Language -->
                <?php if(adminAccessRoute(config('role.language.access.view'))): ?>
                <li class="nav-item dropdown-language dropdown">
                     <a class="nav-link dropdown-toggle btn btn-text-secondary rounded-pill" href="#"
                        data-bs-toggle="dropdown">
                        <?php echo e($languages[$currentLocale] ?? 'Select Language'); ?>

                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('lang.switch', ['locale' => $code])); ?>"
                                data-language="<?php echo e($code); ?>" data-text-direction="ltr">
                                <span><?php echo e($label); ?></span>
                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
                <!--/ Language -->
                <?php endif; ?>


                <!-- Style Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                        id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                        <span class="d-none ms-2" id="nav-theme-text"><?php echo e(__('sidebar.toggle_theme')); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                        <li>
                            <button type="button" class="dropdown-item align-items-center active"
                                data-bs-theme-value="light" aria-pressed="false">
                                <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i><?php echo e(__('sidebar.light')); ?></span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                                aria-pressed="true">
                                <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                        data-icon="moon-stars"></i><?php echo e(__('sidebar.dark')); ?></span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                                aria-pressed="false">
                                <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                        data-icon="device-desktop-analytics"></i><?php echo e(__('sidebar.system')); ?></span>
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
                                <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle"><?php echo e(__('sidebar.my_profile')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.password')); ?>">
                                <i class="icon-base ti tabler-settings me-3 icon-md"></i><span class="align-middle"><?php echo e(__('sidebar.password')); ?></span>
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
                                        <small class="align-middle"><?php echo e(__('sidebar.logout')); ?></small>
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
                            <div data-i18n="Main"><?php echo e(__('sidebar.main')); ?></div>
                        </a>

                        <ul class="menu-sub">
                            <?php if(adminAccessRoute(config('role.work_board.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.workboard' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.workboard')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="WorkBoard"><?php echo e(__('sidebar.workBoard')); ?></div>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(adminAccessRoute(config('role.partners.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.apis' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.apis')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Merchant List"><?php echo e(__('sidebar.merchant_management')); ?></div>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(adminAccessRoute(config('role.agents.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.agent.list' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.agent.list')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="Merchant List"><?php echo e(__('sidebar.agent_management')); ?> </div>
                                </a>
                            </li>
                            <?php endif; ?>

                            

                            
                            
                            

                            <?php if(adminAccessRoute(config('role.manage_staff.access.view'))): ?>
                            <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.users' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('admin.users')); ?>" class="menu-link">
                                    <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                    <div data-i18n="All Users"><?php echo e(__('sidebar.user_management')); ?></div>
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
                        <div data-i18n="Accounts"><?php echo e(__('sidebar.accounts')); ?></div>
                    </a>

                    <ul class="menu-sub">
                        
                        <?php if(adminAccessRoute(config('role.telegram_group.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.groups' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.groups')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="TelegramGroup"><?php echo e(__('sidebar.telegramGroup')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        
                        
                        
                        <?php if(adminAccessRoute(config('role.account_management.access.view'))): ?>
                        <li class="menu-item <?php echo e(Request::routeIs('admin.accounts.management') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.accounts.management')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Account Management"><?php echo e(__('sidebar.account_management')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.e_wallet_accounts_test.access.view'))): ?>
                        <li class="menu-item <?php echo e(Request::routeIs('admin.ewallet.accounts') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.ewallet.accounts')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="E-Wallet Test"><?php echo e(__('sidebar.e_wallet_test')); ?> </div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>


                <!-- Apps -->
                <li class="menu-item <?php echo e($isPartnerActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Partner"><?php echo e(__('sidebar.partner')); ?></div>
                    </a>

                    <ul class="menu-sub">

                        
                        <?php if(adminAccessRoute(config('role.commission_category.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.commission.categories.index' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.commission.categories.index')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Commision Category"><?php echo e(__('sidebar.commission_category')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partners.access.edit'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.apis.balance.add.get' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.apis.balance.add')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Add Balance/Adjustment"><?php echo e(__('sidebar.add_balance_adjustment')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.ewallet_transfer_balance.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.transfer.balance' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.transfer.balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Transfer Balance"><?php echo e(__('sidebar.transfer_balance')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.settlements.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.settlements', 'admin.settlements.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.settlements')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-calendar"></i>
                                <div data-i18n="Partner Settelment"><?php echo e(__('sidebar.partner_settlement')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>


                        <?php if(adminAccessRoute(config('role.commissions.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.api.commissions', 'admin.api.post.commissions']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.api.commissions')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Commission"><?php echo e(__('sidebar.partner_commission')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.adjustments.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.adjustments' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.adjustments')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Monthly Adjustments "><?php echo e(__('sidebar.monthly_adjustments')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.partner.balance', 'admin.partner.balance.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.partner.balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Adjustments"><?php echo e(__('sidebar.adjustments')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.api_logs.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.transections.apilogs' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.transections.apilogs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="API Logs "><?php echo e(__('sidebar.api_logs')); ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.transections.functionlogs' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.transections.functionlogs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="API Logs ">Function Logs </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                    </ul>
                </li>



                
                <li class="menu-item <?php echo e($isTransactionActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Transactions"><?php echo e(__('sidebar.transactions')); ?></div>
                    </a>

                    <ul class="menu-sub">
                        <?php if(adminAccessRoute(config('role.payment_log.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.log', 'admin.payment.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.log')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Deposit Log"><?php echo e(__('sidebar.deposit_log')); ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.log', 'admin.payment.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.log2')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                                <div data-i18n="Deposit log2"><?php echo e(__('sidebar.deposit_log')); ?> (Last Hour)</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.payout_manage.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payout-log', 'admin.payout-log.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payout-log')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawl Log"><?php echo e(__('sidebar.withdrawal_log')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.api_payment_log.access.view'))): ?>
                        <?php if(auth()->user()->username == 'dev'): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.apiLog', 'admin.payment.apisearch']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.apiLog')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Api Deposit Log"><?php echo e(__('sidebar.api_deposit_log')); ?></div>
                            </a>
                        </li>


                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.apiLogunclaimed', 'admin.payment.apiLogunclaimed.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.apiLogunclaimed')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Unclaimed Payment"><?php echo e(__('sidebar.unclaimed_payment')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.deposit_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.report', 'admin.payment.report.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Deposit Report"><?php echo e(__('sidebar.deposit_report')); ?></div>
                            </a>
                        </li>

                        
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.all_reports.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payment.report.all', 'admin.payment.report.all.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.report.all')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="All Report"><?php echo e(__('sidebar.all_report')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.withdrawal_reports.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.payout-report', 'admin.payout-report.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payout-report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-messages"></i>
                                <div data-i18n="Withdrawal Report"><?php echo e(__('sidebar.withdrawal_report')); ?></div>
                            </a>
                        </li>


                        
                        <?php endif; ?>
                    </ul>
                </li>


                
                <li class="menu-item <?php echo e($isReportsActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Reports"><?php echo e(__('sidebar.reports')); ?></div>
                    </a>
                    <ul class="menu-sub">
                        <?php if(adminAccessRoute(config('role.live_e_wallet_balance_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.live_ewallet_balance' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.live_ewallet_balance')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Live E-Wallet Balance"><?php echo e(__('sidebar.live_ewallet_balance')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.daily_e_wallet_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.daily_ewallet_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.daily_ewallet_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily E-Wallet Summary"><?php echo e(__('sidebar.daily_ewallet_summary')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.daily_transaction_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.daily_transection_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.daily_transection_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Daily Transection Summary">
                                    <?php echo e(__('sidebar.daily_transection_summary')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.merchant_charges_summary_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(in_array(Route::currentRouteName(), ['admin.reports.merchant_charges_summary', 'admin.reports.merchant_charges_summary.search']) ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.merchant_charges_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Merchant Charges Summary">
                                    <?php echo e(__('sidebar.merchant_charges_summary')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_summary.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Summary">
                                    <?php echo e(__('sidebar.partner_account_summary')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_balance_summary_creation.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_balance_summary' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_balance_summary')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Creations">
                                    <?php echo e(__('sidebar.partner_account_balance_summary_creation')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_account_balance_summary_completions.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.partner_account_balance_summary_completions' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.partner_account_balance_summary_completions')); ?>"
                                class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Account Balance Summary Completions">
                                    <?php echo e(__('sidebar.partner_account_balance_summary_completions')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.revenue_center_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.revenue_center' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.revenue_center')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Revenue Center"> <?php echo e(__('sidebar.revenue_center')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance_log.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.logs' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.logs')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance Logs"><?php echo e(__('sidebar.partner_balance_log')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.partner_balance_reports.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.cal' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.cal')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R1"><?php echo e(__('sidebar.partner_balance_report1')); ?>

                                </div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.cal2' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.cal2')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Partner Balance R2"><?php echo e(__('sidebar.partner_balance_report2')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.master_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.reports.master_report' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.reports.master_report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Master Report"><?php echo e(__('sidebar.master_report')); ?>

                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.gateway_performance_report.access.view'))): ?>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.payment.payment_gateway_report' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.payment.payment_gateway_report')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Gateway Performance Report">
                                    <?php echo e(__('sidebar.gateway_performance_report')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(adminAccessRoute(config('role.payment_type.access.view'))): ?>
                        <li class="menu-item <?php echo e(Route::currentRouteName() == 'admin.type' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.type')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Payment Type"><?php echo e(__('sidebar.payment_type')); ?></div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if(adminAccessRoute(config('role.merchant_reports.access.view'))): ?>
                <li class="menu-item <?php echo e($isMerchantReportsActive ? 'active open' : ''); ?>">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Merchant Reports"><?php echo e(__('sidebar.merchant_reports')); ?></div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_date' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_date')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Date"><?php echo e(__('sidebar.summary_by_date')); ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_name' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_name')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Name"><?php echo e(__('sidebar.summary_by_name')); ?></div>
                            </a>
                        </li>
                        <li
                            class="menu-item <?php echo e(Route::currentRouteName() == 'admin.merchant_reports.by_month' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.merchant_reports.by_month')); ?>" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-menu-2"></i>
                                <div data-i18n="Summary By Year"><?php echo e(__('sidebar.summary_by_year')); ?></div>
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