<?php

use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ManageRolePermissionController;
use App\Http\Controllers\Admin\ManualGatewayController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PayoutRecordController;
use App\Http\Controllers\Partner\PayoutRecordController as PartnerPayoutRecordController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\TelegramGroupController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\LoginController as PartnerLoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
// rehan
use Illuminate\Support\Facades\Route;

/*```php
// No code was selected, so I'll provide a general improvement suggestion.

// Consider adding route names for the following routes:
Route::get('/clear-cache', function () {
    // Clear various caches
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json([
        'message' => 'All caches cleared successfully!'
    ]);
})->name('clear-cache');

// Also, consider adding route names for the following routes:
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// For the admin routes, consider adding a middleware to check if the user is an admin.
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin', 'admin']], function () {
    // ...
});

// For the partner routes, consider adding a middleware to check if the user is a partner.
Route::group(['prefix' => 'partner', 'as' => 'partner.', 'middleware' => ['auth:partner', 'partner']], function () {
    // ...
});
```
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/clear-cache', function () {

    // dd(config('broadcasting.connections.pusher.options'));
    // Clear various caches
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json([
        'message' => 'All caches cleared successfully!',
    ]);
    exit;
});

Route::get('/', function () {
    return view('welcome')->name('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/403', [AdminDashboardController::class, 'forbidden'])->name('403');

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('loginfrom');
        Route::post('/', [LoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth:admin']], function () {
        // Route::resource('roles',RoleController::class);
        // Route::resource('permissions', PermissionController::class);
        // Route::post('roles/{role}/permissions', [PermissionController::class, 'assignPermissionsToRole'])->name('roles.permissions.assign');
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/staff', [ManageRolePermissionController::class, 'staff'])->name('staff');
        Route::post('/staff', [ManageRolePermissionController::class, 'storeStaff'])->name('storeStaff');
        Route::put('/staff/{admin}', [ManageRolePermissionController::class, 'updateStaff'])->name('updateStaff');

        // Parant Routs
        Route::get('/parent', [ParentController::class, 'parant'])->name('parant');
        Route::get('/workboard', [PayoutRecordController::class, 'workboard'])->name('workboard');
        Route::get('transections/apilogs', [PayoutRecordController::class, 'apilogs'])->name('transections.apilogs');
        Route::get('/get-api-balance/{id}', function ($id) {
            $api = \App\Models\Api::find($id);
            return response()->json(['balance' => $api ? $api->balance : 0]);
        });


        // accounts details
        Route::get('/categories', [CategoryController::class, 'index'])->name('ewallet.accounts.details');
        Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('category.delete');

        Route::get('/account    /groups', [CategoryController::class, 'index'])->name('ewallet.accounts.groups');
        Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
        // rehan Reports:
        Route::get('reports/cal', [ReportsController::class, 'cal'])->name('reports.cal');
        Route::get('reports/logs', [ReportsController::class, 'logs'])->name('reports.logs');
        Route::get('reports/cal2', [ReportsController::class, 'cal2'])->name('reports.cal2');
        Route::get('reports/master_report', [ReportsController::class, 'master_report'])->name('reports.master_report');
        Route::get('reports/revenue_center', [ReportsController::class, 'revenue_center'])->name('reports.revenue_center');
        Route::get('reports/live_ewallet_balance', [ReportsController::class, 'live_ewallet_balance'])->name('reports.live_ewallet_balance');
        Route::get('reports/daily_ewallet_summary', [ReportsController::class, 'daily_ewallet_summary'])->name('reports.daily_ewallet_summary');
        Route::get('reports/partner_account_summary', [ReportsController::class, 'partner_account_summary'])->name('reports.partner_account_summary');
        Route::get('reports/merchant_charges_summary', [ReportsController::class, 'merchant_charges_summary'])->name('reports.merchant_charges_summary');
        Route::get('reports/daily_transection_summary', [ReportsController::class, 'daily_transection_summary'])->name('reports.daily_transection_summary');
        Route::get('payment_gateway_performance_report', [PaymentMethodController::class, 'payment_gateway_report'])->name('payment.payment_gateway_report');
        Route::get('reports/merchant_charges_summary/search', [ReportsController::class, 'merchant_charges_summary_search'])->name('reports.merchant_charges_summary.search');
        Route::get('reports/partner_account_balance_summary', [ReportsController::class, 'partner_account_balance_summary'])->name('reports.partner_account_balance_summary');
        Route::get('reports/partner_account_balance_summary_completions', [ReportsController::class, 'partner_account_balance_summary_completions'])->name('reports.partner_account_balance_summary_completions');
        Route::post('/apis/inline-update', [PayoutRecordController::class, 'inlineUpdate'])->name('apis.inlineUpdate');

        /* ===== AdminMerchant Ticket ==== */
        Route::get('merchant/report_by_date', [MerchantController::class, 'report_by_date'])->name('merchant_reports.by_date');
        Route::get('/admin/merchant-reports/export/{from_date?}', [MerchantController::class, 'export_by_date'])->name('merchant_reports.export_by_date');

        Route::get('merchant/report_by_name', [MerchantController::class, 'report_by_name'])->name('merchant_reports.by_name');
        Route::get('/admin/merchant-reports/exportt/{from_date?}', [MerchantController::class, 'export_by_name'])->name('merchant_reports.export_by_name');

        Route::get('merchant/report_by_month', [MerchantController::class, 'report_by_month'])->name('merchant_reports.by_month');
        Route::get('/admin/merchant-reports/exports/{from_date?}', [MerchantController::class, 'export_by_month'])->name('merchant_reports.export_by_month');

        // Partner Commission
        Route::get('/api/commissions', [PayoutRecordController::class,'apiCommissions'])->name('api.commissions');
        Route::post('/api/commissions', [PayoutRecordController::class,'apiCommissions'])->name('api.post.commissions');
        Route::get('/admin/commissions/export', [PayoutRecordController::class,'exportCommissions'])->name('commissions.export');
        Route::get('/api/export-profile/{id}', [PayoutRecordController::class,'exportprofile'])->name('api.profile.export');
        Route::get('/api/commissions', [PayoutRecordController::class, 'apiCommissions'])->name('api.commissions');
        Route::post('/api/commissions', [PayoutRecordController::class, 'apiCommissions'])->name('api.post.commissions');
        Route::get('/admin/commissions/export', [PayoutRecordController::class, 'exportCommissions'])->name('commissions.export');
        Route::get('/api/export-profile/{id}', [PayoutRecordController::class, 'exportprofile'])->name('api.profile.export');
        Route::post('/apis/{id}/generate-password', [PayoutRecordController::class, 'generatePassword'])->name('apis.generatePassword');
        Route::get('/adjustments', [PayoutRecordController::class, 'adjustments'])->name('adjustments');
        Route::get('adjustments/search', [PayoutRecordController::class, 'adjustmentSearch'])->name('adjustments.search');
        Route::get('/adjustments/approve/{id}', [PayoutRecordController::class, 'approveAdjustment'])->name('adjustments.approve');

        Route::get('/partner/balance', [PayoutRecordController::class, 'partnerBalance'])->name('partner.balance');
        Route::get('partner/balance/search', [PayoutRecordController::class, 'partnerBalanceSearch'])->name('partner.balance.search');
        Route::get('transections/apilogs', [PayoutRecordController::class, 'apilogs'])->name('transections.apilogs');

        // rehan Payment type route:
        Route::get('/type', [PaymentTypeController::class, 'type'])->name('type');
        Route::post('/type/add', [PaymentTypeController::class, 'typeAdd'])->name('type.add');
        Route::put('/type/update/{id}', [PaymentTypeController::class, 'updatetype'])->name('type.update');

        Route::get('/apis', [PayoutRecordController::class, 'apis'])->name('apis');
        Route::post('/apis/add', [PayoutRecordController::class, 'apisAdd'])->name('apis.add');
        Route::post('/apis/add-by-parent', [PayoutRecordController::class, 'apisAddByParent'])->name('apis.addByParent');
        Route::delete('/apis/delete/{id}', [PayoutRecordController::class, 'apisDelete'])->name('apis.delete');
        Route::get('/apis/login/{id}', [PayoutRecordController::class, 'apisLgoin'])->name('apis.login');
        Route::get('/apis/reset/{id}', [PayoutRecordController::class, 'apisReset'])->name('apis.reset');
        Route::get('/apis/commission/{id}', [PayoutRecordController::class, 'apisCommission'])->name('apis.commission');
        Route::get('/api/commissions/detail/{id}', [PayoutRecordController::class, 'apiCommissionsDetail'])->name('api.commissions.detail');
        Route::get('/api/commissions/calculate/{id}', [PayoutRecordController::class, 'apiCommissionsCalculate'])->name('api.commissions.calculate');
        Route::put('/apis/update/{id}', [PayoutRecordController::class, 'updateApi'])->name('apis.update');
        Route::post('/apis/balance/add', [PayoutRecordController::class, 'apisbalanceadd'])->name('apis.balance.add');
        Route::post('/apis/commission/add', [PayoutRecordController::class, 'apisCommissionAdd'])->name('apis.commission.add');

        Route::get('/apis/balance/add', [PayoutRecordController::class, 'apisBalanceAddGet'])->name('apis.balance.add.get');
        Route::get('/groups', [TelegramGroupController::class, 'groups'])->name('groups');
        Route::post('/groups/add', [TelegramGroupController::class, 'groupsAdd'])->name('groups.add');
        Route::put('/groups/update/{id}', [TelegramGroupController::class, 'updateGroup'])->name('groups.update');
        Route::delete('/groups/delete/{id}', [TelegramGroupController::class, 'groupsDelete'])->name('groups.delete');

        Route::get('/settlements', [PayoutRecordController::class, 'settlements'])->name('settlements');
        Route::post('/settlements/Add', [PayoutRecordController::class, 'storeSettlement'])->name('settlements.add');
        Route::get('settlements/search', [PayoutRecordController::class, 'settlementSearch'])->name('settlements.search');
        Route::get('/settlements/reject/{id}', [PayoutRecordController::class, 'rejectSettlement'])->name('settlements.reject');
        Route::get('/settlements/approve/{id}', [PayoutRecordController::class, 'approveSettlement'])->name('settlements.approve');

        // Acconts:
        Route::get('/accounts', [PayoutRecordController::class, 'allAccounts'])->name('accounts');
        Route::get('/accounts/edit/{id}', [PayoutRecordController::class, 'editAccount'])->name('accounts.edit');
        Route::post('/update-status/{id}', [PayoutRecordController::class, 'updateStatus'])->name('update.status');
        Route::get('/accounts/charges/{id}', [PayoutRecordController::class, 'accountCharges'])->name('accounts.charges');
        Route::post('/accounts/balance/add', [PayoutRecordController::class, 'accountBalanceAdd'])->name('account.balance.add');
        Route::delete('/merchant/delete/{account}', [PayoutRecordController::class, 'merchantDelete'])->name('merchant.delete');
        Route::post('/accounts/balance/edit', [PayoutRecordController::class, 'accountBalanceEdit'])->name('account.balance.edit');

        // Add Accounts
        Route::get('/accounts/add', [PayoutRecordController::class, 'addAccount'])->name('accounts.add');
        Route::post('/accounts/create', [PayoutRecordController::class, 'createAccount'])->name('accounts.create');
        Route::put('/accounts/update/{id}', [PayoutRecordController::class, 'updateAccount'])->name('accounts.update');
        Route::post('/accounts/charges/add', [PayoutRecordController::class, 'accountChargesAdd'])->name('accounts.charges.add');

        Route::get('/merchant', [PayoutRecordController::class, 'merchant'])->name('merchant');
        Route::post('/merchant/add', [PayoutRecordController::class, 'merchantAdd'])->name('merchant.add');

        Route::get('/balance/logs', [PayoutRecordController::class, 'balanceLogs'])->name('balance.logs');
        Route::get('balance/logs/search', [PayoutRecordController::class, 'balanceLogsSearch'])->name('balance.logs.search');

        Route::get('/transfer/balance', [PayoutRecordController::class, 'transferBalance'])->name('transfer.balance');
        Route::post('/transfer/balance/add', [PayoutRecordController::class, 'transferBalanceAdd'])->name('transfer.balance.add');

        Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [AdminDashboardController::class, 'password'])->name('password');
        Route::put('/password', [AdminDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');

        // Rehan User Management

        // user management Routes
        Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/users/search', [UsersController::class, 'search'])->name('users.search');
        Route::get('/user/send-email/{id}', [UsersController::class, 'sendEmail'])->name('send-email');
        Route::post('/user/login', [UsersController::class, 'userLogin'])->name('userLogin');
        Route::post('/users-active', [UsersController::class, 'activeMultiple'])->name('user-multiple-active');
        Route::post('/users-inactive', [UsersController::class, 'inactiveMultiple'])->name('user-multiple-inactive');
        Route::get('/user/transaction/{id}', [UsersController::class, 'transaction'])->name('user.transaction');
        Route::get('/user/fundLog/{id}', [UsersController::class, 'funds'])->name('user.fundLog');
        Route::get('/user/payoutLog/{id}', [UsersController::class, 'payoutLog'])->name('user.withdrawal');
        Route::get('user/{user}/kyc', [UsersController::class, 'userKycHistory'])->name('user.userKycHistory');
        Route::get('/bet-history/{user_id?}', [ManageBetController::class, 'betList'])->name('historyBet');
        Route::post('/user/update/{id}', [UsersController::class, 'userUpdate'])->name('user-update');
        Route::post('/user/password/{id}', [UsersController::class, 'passwordUpdate'])->name('userPasswordUpdate');
        Route::post('/user/balance-update/{id}', [UsersController::class, 'userBalanceUpdate'])->name('user-balance-update');
        Route::post('/user/add', [UsersController::class,'userAdd'])->name('user.add');
        // end user management


        // usre location Route
        Route::get('location', [UsersController::class, 'location'])->name('location');
        Route::post('users/location/add', [UsersController::class, 'addUserLocation'])->name('users.location.add');
        // Route::post('/user/update-location', [UsersController::class,'updateUserLocation'])->name('user.update-location');
        // Route::get('users/location/{id}/edit', [UsersController::class, 'editUserLocation'])->name('users.location.edit');
        Route::delete('users/location/{id}', [UsersController::class, 'deleteUserLocation'])->name('users.location.delete');
        Route::put('location/update/{id}', [UsersController::class, 'updateUserLocationDetails'])->name('location.update');

        // Roles and premations Route
        Route::get('roles_and_permission', [UsersController::class, 'roles_and_permission'])->name('roles_and_permission');

        Route::get('/profile', [AdminDashboardController::class,'profile'])->name('profile')->middleware('permission:profile');
        Route::put('/profile', [AdminDashboardController::class,'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [AdminDashboardController::class,'password'])->name('password');
        Route::put('/password', [AdminDashboardController::class,'passwordUpdate'])->name('passwordUpdate');
        // Roles Catgory Routs:
        // Roles Catgory Routs:
        Route::post('roles/copy', [UsersController::class, 'copyRole'])->name('roles.copy');
        Route::delete('roles/delete', [UsersController::class, 'deleteRole'])->name('roles.delete');
        Route::put('rolescategory/{id}', [UsersController::class, 'updateRole'])->name('roles.update');
        Route::get('/rolescategory', [UsersController::class, 'rolesCategory'])->name('rolescategory');
        Route::post('/rolescategory', [UsersController::class, 'addRole'])->name('roles.add');
        Route::get('roles/list', [UsersController::class, 'getRoles'])->name('roles.list');

        Route::get('payment/log', [PaymentLogController::class, 'index'])->name('payment.log');
        Route::get('payment/search', [PaymentLogController::class, 'search'])->name('payment.search');
        Route::put('payment/update_e_wallet', [PaymentLogController::class, 'update_e_wallet'])->name('payment.update_e_wallet');
        Route::post('/accounts/run/callback/deposit', [PaymentLogController::class, 'runCallback'])->name('run.deposit.callback');

        Route::get('/user/edit/{id}', [UsersController::class, 'userEdit'])->name('user-edit');
        Route::put('payment/action/{id}', [PaymentLogController::class, 'action'])->name('payment.action');

        Route::get('/payout-log', [PayoutRecordController::class, 'index'])->name('payout-log');
        Route::get('/payout-log/search', [PayoutRecordController::class, 'search'])->name('payout-log.search');
        Route::put('payout/update_e_wallet', [PayoutRecordController::class, 'update_e_wallet'])->name('payout.update_e_wallet');
        Route::post('/accounts/run/callback', [PayoutRecordController::class, 'runCallback'])->name('run.callback');
        Route::get('/payout-report/get-notification', [PayoutRecordController::class, 'getNotification'])->name('payout-report.getnotification');
        Route::get('/payout-request', [PayoutRecordController::class, 'request'])->name('payout-request');
        Route::put('/payout-action/{id}', [PayoutRecordController::class, 'action'])->name('payout-action');

        Route::get('payment/apiLog', [PaymentLogController::class, 'apiLog'])->name('payment.apiLog');
        Route::get('payment/apisearch', [PaymentLogController::class, 'apisearch'])->name('payment.apisearch');
        Route::get('payment/apiLogUnclaimed', [PaymentLogController::class, 'apiLogUnclaimed'])->name('payment.apiLogunclaimed');
        Route::get('payment/apiLogUnclaimed/search', [PaymentLogController::class, 'apiLogUnclaimedsearch'])->name('payment.apiLogunclaimed.search');

        Route::get('payment/report', [PaymentLogController::class, 'report'])->name('payment.report');
        Route::get('payment/report/search', [PaymentLogController::class, 'reportSearch'])->name('payment.report.search');
        Route::get('payment/report/daily', [PaymentLogController::class, 'dailyReport'])->name('payment.report.daily');
        Route::get('payment/report/daily/search', [PaymentLogController::class, 'dailyReportSearch'])->name('payment.report.daily.search');
        Route::get('payment/report/all', [PaymentLogController::class, 'allReport'])->name('payment.report.all');
        Route::get('payment/report/all/search', [PaymentLogController::class, 'allReportSearch'])->name('payment.report.all.search');
        Route::get('payment/report/detail/{date}/{gateway}/{status}', [PaymentLogController::class, 'reportDetail'])->name('payment.report.detail');
        Route::get('payout/report/detail/{date}/{gateway}/{status}', [PayoutRecordController::class, 'reportDetail'])->name('payout.report.detail');
        Route::get('/payout-report', [PayoutRecordController::class, 'report'])->name('payout-report');
        Route::get('/payout-report/search', [PayoutRecordController::class, 'reportSearch'])->name('payout-report.search');
        Route::get('payout/report/daily', [PayoutRecordController::class, 'dailyReport'])->name('payout.report.daily');
        Route::get('payout/report/daily/search', [PayoutRecordController::class, 'dailyReportSearch'])->name('payout.report.daily.search');

        // Manual Methods
        Route::get('payment-methods/manual', [ManualGatewayController::class, 'index'])->name('deposit.manual.index');
        Route::get('payment-methods/manual/new', [ManualGatewayController::class, 'create'])->name('deposit.manual.create');
        Route::post('payment-methods/manual/new', [ManualGatewayController::class, 'store'])->name('deposit.manual.store');
        Route::get('payment-methods/manual/edit/{id}', [ManualGatewayController::class, 'edit'])->name('deposit.manual.edit');
        Route::put('payment-methods/manual/update/{id}', [ManualGatewayController::class, 'update'])->name('deposit.manual.update');
        Route::post('payment-methods/deactivate', [PaymentMethodController::class, 'deactivate'])->name('payment.methods.deactivate');
        Route::get('payment-methods/deactivate', [PaymentMethodController::class, 'deactivate'])->name('payment.methods.deactivate');

        Route::get('payment-methods/manual/accounts', [AccountManagementController::class, 'index'])->name('deposit.accounts.index');
        Route::get('payment-methods/manual/new/accounts', [AccountManagementController::class, 'create'])->name('deposit.accounts.create');
        Route::post('payment-methods/manual/new/accounts', [AccountManagementController::class, 'store'])->name('deposit.accounts.store');
        Route::get('payment-methods/manual/edit/{id}/accounts', [AccountManagementController::class, 'edit'])->name('deposit.maccounts.edit');
        Route::put('payment-methods/manual/update/{id}/accounts', [AccountManagementController::class, 'update'])->name('deposit.accounts.update');
        Route::post('payment-methods/deactivate/accounts', [AccountManagementController::class, 'deactivate'])->name('accounts.payment.methods.deactivate');
        Route::get('payment-methods/deactivate/accounts', [AccountManagementController::class, 'deactivate'])->name('.accountsl.payment.methods.deactivate');

        Route::get('/e-wallet/accounts', [PayoutRecordController::class, 'eWalletAccounts'])->name('ewallet.accounts');

        Route::get('/e_wallet_accounts/{id}/toggle-status', [PayoutRecordController::class, 'toggleStatus'])->name('e_wallet_accounts.toggle_status');
        Route::delete('/e-wallet/admin/delete/{account}', [PayoutRecordController::class, 'adminAccountDelete'])->name('ewallet.accounts.delete');
        Route::post('/accounts/deposit/test', [PayoutRecordController::class, 'depositTest'])->name('deposit.test');
        Route::post('/e-wallet/admin/add', [PayoutRecordController::class, 'eWalletAccountsAdd'])->name('ewallet.accounts.add');
        Route::post('/accounts/deposit/testp', [PayoutRecordController::class, 'depositTestp'])->name('deposit.testp');
        Route::post('/accounts/withdrawal/test', [PayoutRecordController::class, 'withdrawalTest'])->name('withdrawal.test');
        Route::post('/accounts/withdrawal/testp', [PayoutRecordController::class, 'withdrawalTestp'])->name('withdrawal.testp');

        Route::get('merchant-profile/{id}', [MerchantController::class, 'profile'])->name('merchant.profile');
        Route::get('merchant-logs/{id}', [MerchantController::class, 'mechantlogs'])->name('merchant.logs');
        Route::post('/activity-logs', [MerchantController::class, 'fetchActivityLogs'])->name('fetchActivityLogs');






    });

    // User Location Routes
    // Route::get('users/location', [UsersController::class, 'location'])->name('users.location');


});
// partnerRoutes
Route::group(['prefix' => 'partner', 'as' => 'partner.'], function () {
    Route::middleware(['guest:partner'])->group(function () {
        Route::get('/', [PartnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [PartnerLoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth:partner']], function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/twoFA', [PartnerDashboardController::class, 'twoFA'])->name('twoFA');
        Route::post('/twoFA', [PartnerDashboardController::class, 'updateTwoFA'])->name('twoFA.update');
        Route::get('/twoFA/disable', [PartnerDashboardController::class, 'disableTwoFA'])->name('twoFA.disable');
        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');

        Route::get('/{username}/url', [PartnerPayoutRecordController::class, 'methods'])->name('methods.get');
        Route::get('/{username}/deposit', [PartnerPayoutRecordController::class, 'depositFund'])->name('depositFund');
        Route::post('partner/add-fund/open', [PartnerPayoutRecordController::class, 'addFundRequestOpen'])->name('addFund.request.open');



        Route::get('/{username}/withdrawal', [PartnerPayoutRecordController::class, 'payoutMoneyTransection'])->name('payout.money.transection');
        Route::post('/withdraw/transection', [PayoutRecordController::class, 'payoutMoneyRequestTransection'])->name('payout.moneyRequest.transection');

        // Route::get('merchant/report_by_date', [MerchantController::class,'report_by_date'])->name('merchant_reports.by_date');
        // Route::get('merchant-reports/export', [MerchantController::class,'export_by_date'])->name('merchant_reports.export_by_date');

        // Route::get('merchant/report_by_name', [MerchantController::class,'report_by_name'])->name('merchant_reports.by_name');
        // Route::get('merchant-reports/export_name', [MerchantController::class,'export_by_name'])->name('merchant_reports.export_by_name');

        // Route::get('merchant/report_by_month', [MerchantController::class, 'report_by_month'])->name('merchant_reports.by_month');
        // Route::get('merchant-reports/export_month', [MerchantController::class, 'export_by_month'])->name('merchant_reports.export_by_month');
        Route::get('iframe/{username}/{ewallet}/{acc}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class, 'processTransection'])->name('iframe.open');
        Route::get('iframe2/{username}/{ewallet}/{acc}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class,'processTransection2'])->name('iframe.open');
        Route::get('iframe3/{username}/{ewallet}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class,'processTransection3'])->name('iframe.direct');
        Route::get('process/update-fund-order-status/iframe/{id}', [PartnerPayoutRecordController::class,'update_order_fund_status_iframe'])->name('update_fund_order_status.iframe');
        Route::get('process/payment/{id}', [PartnerPayoutRecordController::class,'processNextPayment'])->name('iframe.payment');
        Route::post('process/payment2', [PartnerPayoutRecordController::class,'processNextPayment2'])->name('iframe.payment2');
        Route::post('process/payment3', [PartnerPayoutRecordController::class,'processNextPayment3'])->name('iframe.payment3');
        Route::post('process/iframe/getaccount', [PartnerPayoutRecordController::class,'getaccount'])->name('iframe.getaccount');

        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');
    });

});
