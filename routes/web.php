<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Webhook\WebhookController;
use App\Http\Controllers\CCategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\TrackingController;
use App\Http\Controllers\Admin\DevFunctionsController;
use App\Http\Controllers\Admin\PayoutRecordController;
use App\Http\Controllers\Admin\ManualGatewayController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\TelegramGroupController;
use App\Http\Controllers\Admin\MerchantAccountController;
use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\Admin\ManageRolePermissionController;
use App\Http\Controllers\Partner\LoginController as PartnerLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Partner\ReportsController as PartnerReportsController;
use App\Http\Controllers\Partner\MerchantController as PartnerMerchantController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\PaymentLogController as PartnerPaymentLogController;
// rehan
use App\Http\Controllers\Partner\PayoutRecordController as PartnerPayoutRecordController;
use App\Http\Controllers\Partner\SummaryReportController as PartnerSummaryReportController;
use App\Http\Controllers\Partner\ManageRolePermissionController as PartnerManageRolePermissionController;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\AuditLog;
use App\Models\CsTracker;

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

    //Route::get('/approve-payout-transaction/{id}/{status}', [DevFunctionsController::class, 'payoutAction']);
    Route::get('/create_transaction_log', [DevFunctionsController::class, 'create_transaction_log']);


    Route::group(['middleware' => ['auth:admin', 'check.admin.status', 'permission']], function () {

        Route::get('/twoFA', [AdminDashboardController::class, 'twoFA'])->name('twoFA');
        Route::post('/twoFA', [AdminDashboardController::class, 'updateTwoFA'])->name('twoFA.update');
        
        // Route::resource('roles',RoleController::class);
        // Route::resource('permissions', PermissionController::class);
        // Route::post('roles/{role}/permissions', [PermissionController::class, 'assignPermissionsToRole'])->name('roles.permissions.assign');
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/staff', [ManageRolePermissionController::class, 'staff'])->name('staff');



        // Parant Routs
        Route::get('/parent', [ParentController::class, 'parant'])->name('parant');
        Route::get('/workboard', [PayoutRecordController::class, 'workboard'])->name('workboard');
        Route::get('/fetchrecords', [PayoutRecordController::class, 'fetchrecords'])->name('fetchrecords');
        Route::post('/hide-transaction', [PayoutRecordController::class, 'hideTransaction'])->name('hideTransaction');
        Route::post('/adjust-transaction', [PayoutRecordController::class, 'adjustTransaction'])->name('adjust.transaction');
        Route::post('/update/payment', [PayoutRecordController::class, 'updatePayment'])->name('update.payment');
        Route::post('/update/payout', [PayoutRecordController::class, 'updatePayout'])->name('update.payout');
        Route::post('/manual-process-copy', [PayoutRecordController::class, 'manualProcess'])->name('manual-process');
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('audit_logs.index');
        Route::get('/CsTrakcer', [TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/CsTrakcer/filter', [TrackingController::class, 'filter'])->name('tracking.filter');



        Route::post('/update-adjusted-by', function (Request $request) {
            $txnId = $request->txnId;
            $adjustedBy = $request->adjusted_by;

            // Fetch the payout record first
            $payout = Payout::where('partner_transection_id', $txnId)->first();

            if ($payout) {
                // Update the fields
                $payout->update([
                    'adjusted_by' => $adjustedBy,
                    'check_by' => $adjustedBy
                ]);

                // Log into audit log
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'module' => 'Workboard WITHDRAWAL PENDING LIST',
                    'module_id' => $payout->id,
                    'description' => "Pending Withdrawl Payout ID {$payout->id} checked by user."
                ]);

                // Update CsTracker - set 'to' time without changing 'from'
                CsTracker::where('action', 'like', '%Payout ID: ' . $payout->id)
                        ->whereNull('to')
                        ->update([
                            'to' => now(),
                            'user_id' => auth()->id(),
                            'action' => auth()->user()->name . ' checked the Pending List (Payout ID: ' . $payout->id . ')'
                        ]);

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Payout not found.'], 404);
        });



        Route::get('/get-api-balance/{id}', function ($id) {
            $api = \App\Models\Api::find($id);
            return response()->json(['balance' => $api ? $api->balance : 0]);
        });



        // WebHook Route
        Route::get('/webhook', [WebhookController::class, 'index'])->name('webhook');

        // accounts details
        Route::get('/accounts-management', [CategoryController::class, 'index'])->name('ewallet.accounts.details');
        Route::get('/accounts-management/add-account', [CategoryController::class, 'addAccount'])->name('account_management.add_account');
        // Route::get('/accounts-management/edit-account/{id}', [CategoryController::class, 'editAccount'])->name('account_management.edit_account');
        Route::get('/get-accounts/{category_id}', [CategoryController::class, 'getAccountsByCategory'])->name('get.e_wallet_accounts');

        Route::get('/accounts-management/on-off', [CategoryController::class, 'onOffAccount'])->name('account_management.on_off_account');
        Route::post('/wallet/update-account-type', [CategoryController::class, 'updateAccountType'])->name('wallet.updateAccountType');
        Route::post('/wallet/update-gateway-deposit', [CategoryController::class, 'updateGatewayDeposit'])->name('wallet.updateGatewayDeposit');
        Route::post('/wallet/update-gateway-withdrawal', [CategoryController::class, 'updateGatewayWithdrawal'])->name('wallet.updateGatewayWithdrawal');
        Route::get('/accounts-management/add-category', [CategoryController::class, 'addCategory'])->name('account_management.add_category');


        Route::get('/accounts-management/add-gateways', [CategoryController::class, 'gateway'])->name('account_management.gateway');




        Route::post('/categories', [CategoryController::class, 'store'])->name('accounts.management');
        Route::post('/category/{id}/status', [CategoryController::class, 'changeStatus'])->name('category.status');
        Route::delete('/categories/delete', [CategoryController::class, 'destroy'])->name('category.delete');
        Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('category.update');

        Route::get('/account/groups', [CategoryController::class, 'index'])->name('ewallet.accounts.groups');
        Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');


        // rehan Reports:
        Route::get('reports/cal', [ReportsController::class, 'cal'])->name('reports.cal');
        Route::get('reports/logs', [ReportsController::class, 'logs'])->name('reports.logs');
        Route::get('reports/cal2', [ReportsController::class, 'cal2'])->name('reports.cal2');
        Route::get('reports/master_report', [ReportsController::class, 'master_report'])->name('reports.master_report');
        Route::get('reports/commission-breakdown', [ReportsController::class, 'commissionBreakdown'])->name('reports.commission_breakdown');
        Route::get('reports/revenue_center', [ReportsController::class, 'revenue_center'])->name('reports.revenue_center');
        Route::get('reports/live_ewallet_balance', [ReportsController::class, 'live_ewallet_balance'])->name('reports.live_ewallet_balance');
        Route::get('reports/daily_ewallet_summary', [ReportsController::class, 'daily_ewallet_summary'])->name('reports.daily_ewallet_summary');
        Route::get('reports/partner_account_summary', [ReportsController::class, 'partner_account_summary'])->name('reports.partner_account_summary');
        Route::get('reports/merchant_charges_summary', [ReportsController::class, 'merchant_charges_summary'])->name('reports.merchant_charges_summary');
        Route::get('reports/daily_transection_summary', [ReportsController::class, 'daily_transection_summary'])->name('reports.daily_transection_summary');
        Route::post('/payout/retry', [PayoutRecordController::class, 'retry'])->name('payout.retry');
        Route::get('payment_gateway_performance_report', [PaymentMethodController::class, 'payment_gateway_report'])->name('payment.payment_gateway_report');
        Route::get('payment_gateway_performance_report_detail/{id?}/{from_date?}/{to_date?}', [PaymentMethodController::class, 'payment_gateway_report_detail'])->name('payment.payment_gateway_report_detail');
        Route::get('reports/merchant_charges_summary/search', [ReportsController::class, 'merchant_charges_summary_search'])->name('reports.merchant_charges_summary.search');
        Route::get('reports/partner_account_balance_summary', [ReportsController::class, 'partner_account_balance_summary'])->name('reports.partner_account_balance_summary');
        Route::get('fix-partner-balance-summary-balance', [ReportsController::class, 'fix_partner_summary_closing_balance'])->name('dev_partner_summary_fix_balance');

        // AA
        // Route::get('reports/sms/logs', [ReportsController::class, 'smsLogs'])->name('admin.sms.logs');
        Route::get('reports/sms_logs', [ReportsController::class, 'smsLogs'])->name('sms.logs');
        Route::get('reports/partner_account_balance_summaryv2', [ReportsController::class, 'partner_account_balance_summaryv2'])->name('reports.partner_account_balance_summaryv2');
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
        // Route::get('partner/balance/export/{from_date?}/{to_date?}/{partner?}/{search_by_name?}/{adjustment?}', [PayoutRecordController::class,'export_for_blance2'])->name('blance_export');
        Route::get('partner/balance/export', [PayoutRecordController::class,'export_for_blance2'])->name('blance_export');
        Route::get('transections/apilogs', [PayoutRecordController::class, 'apilogs'])->name('transections.apilogs');
        Route::get('transections/functionlogs', [PayoutRecordController::class, 'functionlogs'])->name('transections.functionlogs');

        // rehan Payment type route:
        Route::get('/type', [PaymentTypeController::class, 'type'])->name('type');
        Route::post('/type/add', [PaymentTypeController::class, 'typeAdd'])->name('type.add');
        Route::put('/type/update/{id}', [PaymentTypeController::class, 'updatetype'])->name('type.update');

        // Route::get('/get-api-log/{url?}', [PayoutRecordController::class, 'getApiLog'])->where('url', '.*');
        // Route::get('/get-api-log2/{url?}', [PayoutRecordController::class, 'getApiLog2'])->where('url', '.*');

        Route::match(['get', 'post'], '/get-api-log/{url?}', [PayoutRecordController::class, 'getApiLog'])->where('url', '.*');
        Route::match(['get', 'post'], '/get-api-log2/{url?}', [PayoutRecordController::class, 'getUnifiedApiLog'])->where('url', '.*');

        Route::get('/apis', [PayoutRecordController::class, 'apis'])->name('apis');
        Route::get('/agent/list', [PayoutRecordController::class, 'agentlist'])->name('agent.list');
        Route::post('/apis/toggle-status', [PayoutRecordController::class, 'toggleStatusApi'])->name('apis.toggleStatus');
        Route::patch('/notifications/{notification}/mark-as-read', [PayoutRecordController::class, 'markAsRead']);

        Route::post('/apis/add', [PayoutRecordController::class, 'apisAdd'])->name('apis.add');
        Route::post('/agent/add', [PayoutRecordController::class, 'agentAdd'])->name('agent.add');
        Route::post('/apis/add-by-parent', [PayoutRecordController::class, 'apisAddByParent'])->name('apis.addByParent');
        Route::delete('/apis/delete/{id}', [PayoutRecordController::class, 'apisDelete'])->name('apis.delete');
        Route::get('/apis/login/{id}', [PayoutRecordController::class, 'apisLgoin'])->name('apis.login');
        Route::get('/apis/reset/{id}', [PayoutRecordController::class, 'apisReset'])->name('apis.reset');
        Route::get('/apis/commission/{id}', [PayoutRecordController::class, 'apisCommission'])->name('apis.commission');
        Route::get('/apis/commissions/detail/{id}', [PayoutRecordController::class, 'apiCommissionsDetail'])->name('api.commissions.detail');

        Route::get('/apis/commissions/calculate/{id}', [PayoutRecordController::class, 'apiCommissionsCalculate'])->name('api.commissions.calculate');
        Route::put('/apis/update/{id}', [PayoutRecordController::class, 'updateApi'])->name('apis.update');
        Route::put('/apis/agent/update/{id}', [PayoutRecordController::class, 'agentupdateApi'])->name('apis.agent.update');
        Route::post('/apis/balance/add', [PayoutRecordController::class, 'apisbalanceadd'])->name('apis.balance.add');
        Route::post('/apis/commission/add', [PayoutRecordController::class, 'apisCommissionAdd'])->name('apis.commission.add');
        // partner commission
        Route::get('/partner/commission/{id}', [PayoutRecordController::class, 'partnerCommission'])->name('partner.commision.form');
        Route::post('/add-partner/commission', [PayoutRecordController::class, 'addpartnerCommission'])->name('add.partner.commission');

        Route::delete('/partner/commission/{id}', [PayoutRecordController::class, 'commissionDelete'])->name('partner.commission.delete');
        Route::get('/partner/commissionedit/{id}', [PayoutRecordController::class, 'partnerCommissionedit'])->name('partner.commisionedit.form');
        Route::post('/edit-partner/commission', [PayoutRecordController::class, 'editpartnerCommission'])->name('edit.partner.commission');



        Route::get('/apis/balance/add', [PayoutRecordController::class, 'apisBalanceAddGet'])->name('apis.balance.add.get');
        Route::get('/groups', [TelegramGroupController::class, 'groups'])->name('groups');
        Route::post('/groups/add', [TelegramGroupController::class, 'groupsAdd'])->name('groups.add');
        Route::put('/groups/update/{id}', [TelegramGroupController::class, 'updateGroup'])->name('groups.update');
        Route::delete('/groups/delete/{id}', [TelegramGroupController::class, 'groupsDelete'])->name('groups.delete');
        Route::post('/groups/toggle-status/{id}', [TelegramGroupController::class, 'toggleStatus'])->name('groups.toggleStatus');




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

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/store', [SettingController::class, 'store'])->name('settings.store');
    Route::post('/settings/update/{id}', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/delete/{id}', [SettingController::class, 'destroy'])->name('settings.delete');
    Route::get('blacklist', [\App\Http\Controllers\Admin\BlacklistController::class, 'index'])->name('blacklist.index');
    Route::delete('blacklist/{id}', [\App\Http\Controllers\Admin\BlacklistController::class, 'destroy'])->name('blacklist.destroy');

        // Add Accounts
        Route::get('/accounts/add', [PayoutRecordController::class, 'addAccount'])->name('accounts.add');
        Route::post('/accounts/create', [PayoutRecordController::class, 'createAccount'])->name('accounts.create');
        Route::post('/accounts/update/{id}', [PayoutRecordController::class, 'updateAccount'])->name('accounts.update');
        Route::post('/accounts/{id}/status', [PayoutRecordController::class, 'changeStatus'])->name('accounts.status');
        Route::post('/accounts/charges/add', [PayoutRecordController::class, 'accountChargesAdd'])->name('accounts.charges.add');
        Route::get('/accounts/group', [PayoutRecordController::class, 'accountGroupList'])->name('accounts.group.add');

        // Accunt groups
        Route::post('/accounts/addpairs', [PayoutRecordController::class, 'addAccountPairs'])->name('accounts.addpairs');
        Route::post('/admin/accounts/update-group', [PayoutRecordController::class, 'updateAccountGroup'])->name('accounts.updateGroup');
        Route::post('/updateaccount-status', [PayoutRecordController::class, 'updateaccountStatus'])->name('update.accstatus');

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
        Route::post('/staff', [UsersController::class, 'storeStaff'])->name('storeStaff');
        Route::put('/staff/{admin}', [UsersController::class, 'updateStaff'])->name('updateStaff');

        Route::get('/users/search', [UsersController::class, 'search'])->name('users.search');
        Route::get('/user/send-email/{id}', [UsersController::class, 'sendEmail'])->name('send-email');
        Route::post('/user/login', [UsersController::class, 'userLogin'])->name('userLogin');
        Route::post('/users-active', [UsersController::class, 'activeMultiple'])->name('user-multiple-active');
        Route::post('/users-inactive', [UsersController::class, 'inactiveMultiple'])->name('user-multiple-inactive');
        Route::get('/user/transaction/{id}', [UsersController::class, 'transaction'])->name('user.transaction');
        Route::get('/user/fundLog/{id}', [UsersController::class, 'funds'])->name('user.fundLog');
        Route::get('/user/payoutLog/{id}', [UsersController::class, 'payoutLog'])->name('user.withdrawal');
        Route::get('user/{user}/kyc', [UsersController::class, 'userKycHistory'])->name('user.userKycHistory');
        Route::post('/user/update/{id}', [UsersController::class, 'userUpdate'])->name('user-update');
        Route::post('/user/password/{id}', [UsersController::class, 'passwordUpdate'])->name('userPasswordUpdate');
        Route::post('/user/balance-update/{id}', [UsersController::class, 'userBalanceUpdate'])->name('user-balance-update');
        Route::post('/user/add', [UsersController::class,'userAdd'])->name('user.add');
        // end user management


        // usre location Route
        Route::get('location', [UsersController::class, 'location'])->name('location');
        Route::post('users/location/add', [UsersController::class, 'addUserLocation'])->name('users.location.add');
        Route::delete('users/location', [UsersController::class, 'deleteUserLocation'])->name('users.location.delete');
        Route::put('location/update/{id}', [UsersController::class, 'updateUserLocationDetails'])->name('location.update');
        Route::post('/location/toggle-status', [UsersController::class, 'toggleLocationStatus'])->name('location.toggleStatus');
        Route::post('/toggle-status/{id}', [UsersController::class, 'toggleStaffStatus'])->name('toggleStaffStatus');

        // Roles and premations Route
        Route::get('roles_and_permission', [UsersController::class, 'roles_and_permission'])->name('roles_and_permission');
        Route::post('update_role_permissions/{id}', [UsersController::class, 'updatePermissions'])->name('update_role_permissions');


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
        Route::get('payment/log2', [PaymentLogController::class, 'log2'])->name('payment.log2');
        Route::get('payment/search', [PaymentLogController::class, 'search'])->name('payment.search');
        Route::put('payment/update_e_wallet', [PaymentLogController::class, 'update_e_wallet'])->name('payment.update_e_wallet');
        Route::post('/accounts/run/callback/deposit', [PaymentLogController::class, 'runCallback'])->name('run.deposit.callback');

        Route::get('/user/edit/{id}', [UsersController::class, 'userEdit'])->name('user-edit');
        Route::post('/user/update/{id}', [UsersController::class, 'userUpdate'])->name('user-update');
        Route::put('payment/action/{id}', [PaymentLogController::class, 'action'])->name('payment.action');

        Route::get('/payout-log', [PayoutRecordController::class, 'index'])->name('payout-log');
        Route::get('/payout-log/search', [PayoutRecordController::class, 'search'])->name('payout-log.search');
        Route::put('payout/update_e_wallet', [PayoutRecordController::class, 'update_e_wallet'])->name('payout.update_e_wallet');
        Route::post('/accounts/run/callback', [PayoutRecordController::class, 'runCallback'])->name('run.callback');
        Route::get('/payout-report/get-notification', [PayoutRecordController::class, 'getNotification'])->name('payout-report.getnotification');
        Route::get('/payout-request', [PayoutRecordController::class, 'request'])->name('payout-request');
        Route::put('/payout-action/{id}', [PayoutRecordController::class, 'action'])->name('payout-action');

        Route::get('makeatest/{id?}', [PaymentLogController::class, 'makeatest'])->name('makeatest');

        Route::get('payment/apiLog', [PaymentLogController::class, 'apiLog'])->name('payment.apiLog');
        Route::get('payment/apisearch', [PaymentLogController::class, 'apisearch'])->name('payment.apisearch');
        Route::get('payment/apiLogUnclaimed', [PaymentLogController::class, 'apiLogUnclaimed'])->name('payment.apiLogunclaimed');
        Route::get('payment/apiLogUnclaimed/search', [PaymentLogController::class, 'apiLogUnclaimedsearch'])->name('payment.apiLogunclaimed.search');

        Route::get('payment/report', [PaymentLogController::class, 'report'])->name('payment.report');
        Route::get('reports/export/{from_date?}', [PaymentLogController::class, 'export_by_logs'])->name('merchant_reports.export_by_logs');
        Route::get('payment/report/search', [PaymentLogController::class, 'reportSearch'])->name('payment.report.search');
        Route::get('payment/report/daily', [PaymentLogController::class, 'dailyReport'])->name('payment.report.daily');
        Route::get('payment/report/daily/search', [PaymentLogController::class, 'dailyReportSearch'])->name('payment.report.daily.search');
        Route::get('payment/report/all', [PaymentLogController::class, 'allReport'])->name('payment.report.all');
        Route::get('payment/report/all/search', [PaymentLogController::class, 'allReportSearch'])->name('payment.report.all.search');
        Route::get('payment/report/detail/{date}/{gateway}/{status}', [PaymentLogController::class, 'reportDetail'])->name('payment.report.detail');
        Route::get('payout/report/detail/{date}/{gateway}/{status}', [PayoutRecordController::class, 'reportDetail'])->name('payout.report.detail');
        Route::get('/payout-report', [PayoutRecordController::class, 'report'])->name('payout-report');
        Route::get('withdrawl_export', [PayoutRecordController::class, 'export_Withdrawl'])->name('export_Withdrawl');
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
        Route::post('payment-methods/manual/update/{id}/accounts', [AccountManagementController::class, 'update'])->name('deposit.accounts.update');
        Route::post('payment-methods/deactivate/accounts/{id?}', [AccountManagementController::class, 'deactivate'])->name('accounts.payment.methods.deactivate');
        Route::get('payment-methods/deactivate/accounts', [AccountManagementController::class, 'deactivate'])->name('.accountsl.payment.methods.deactivate');

        Route::get('/e-wallet/accounts', [PayoutRecordController::class, 'eWalletAccounts'])->name('ewallet.accounts');

        Route::get('/e_wallet_accounts/{id}/toggle-status', [PayoutRecordController::class, 'toggleStatus'])->name('e_wallet_accounts.toggle_status');
        Route::delete('/e-wallet/delete/{account}', [PayoutRecordController::class, 'adminAccountDelete'])->name('ewallet.accounts.delete');
        Route::post('/accounts/deposit/test', [PayoutRecordController::class, 'depositTest'])->name('deposit.test');
        Route::post('/e-wallet/admin/add', [PayoutRecordController::class, 'eWalletAccountsAdd'])->name('ewallet.accounts.add');
        Route::post('/accounts/deposit/testp', [PayoutRecordController::class, 'depositTestp'])->name('deposit.testp');
        Route::post('/accounts/withdrawal/test', [PayoutRecordController::class, 'withdrawalTest'])->name('withdrawal.test');
        Route::post('/accounts/withdrawal/testp', [PayoutRecordController::class, 'withdrawalTestp'])->name('withdrawal.testp');
        Route::post('/accounts-management', [PayoutRecordController::class, 'eWalletAccountsAdd'])->name('accounts.management');


        Route::get('agent-profile/{id}', [MerchantController::class, 'agent_profile'])->name('agent.profile');
        Route::get('merchant-profile/{id}', [MerchantController::class, 'profile'])->name('merchant.profile');
        Route::get('agent-logs/{id}', [MerchantController::class, 'agent_logs'])->name('agent.logs');
        Route::get('merchant-logs/{id}', [MerchantController::class, 'mechantlogs'])->name('merchant.logs');
        Route::post('/activity-logs', [MerchantController::class, 'fetchActivityLogs'])->name('fetchActivityLogs');


        Route::get('/account-management/account-group', [AccountManagementController::class, 'accountGroup'])->name('account_management.account_group');
        Route::post('/ewallet-account/toggle-status', [CategoryController::class, 'toggleStatus'])->name('ewallet-account.toggleStatus');
        Route::post('/ewallet-account/send-gateway-notice', [CategoryController::class, 'sendGatewayNotice'])->name('gateway.send_notice');


        Route::get('/merchant_accounts', [MerchantAccountController::class, 'apis'])->name('merchant_accounts');
        Route::post('/merchant_accounts/add', [MerchantAccountController::class, 'apisAdd'])->name('merchant_accounts.add');
        Route::delete('/merchant_accounts/delete/{id}', [MerchantAccountController::class, 'apisDelete'])->name('merchant_accounts.delete');
        Route::put('/merchant_accounts/update/{id}', [MerchantAccountController::class, 'updateApi'])->name('merchant_accounts.update');

            Route::prefix('commission/categories')->name('commission.categories.')->group(function () {
                Route::get('/', [CCategoryController::class, 'index'])->name('index');
                Route::post('/', [CCategoryController::class, 'store'])->name('store');
                Route::put('/', [CCategoryController::class, 'update'])->name('update');
                Route::delete('/', [CCategoryController::class, 'destroy'])->name('destroy');
            });



    });

    // User Location Routes
    // Route::get('users/location', [UsersController::class, 'location'])->name('users.location');


});
// partnerRoutes
Route::group(['prefix' => 'partner', 'as' => 'partner.'], function () {
    Route::get('/403', [PartnerLoginController::class, 'forbidden'])->name('403');
    Route::middleware(['guest:partner'])->group(function () {
        Route::get('/', [PartnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [PartnerLoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth:partner' , 'permission_partner']], function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/twoFA', [PartnerDashboardController::class, 'twoFA'])->name('twoFA');
        Route::post('/twoFA', [PartnerDashboardController::class, 'updateTwoFA'])->name('twoFA.update');

        // Route::get('/twoFA/disable', [PartnerDashboardController::class, 'disableTwoFA'])->name('twoFA.disable');

        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');
        //      ///////////////////////////////////////----------------------------------------

        //      ///////////////////////////////////////----------------------------------------
        Route::get('/apis', [PartnerPayoutRecordController::class, 'apis'])->name('apis');
        Route::get('/api/commissions', [PartnerPayoutRecordController::class, 'apiCommissions'])->name('api.commissions');

        Route::get('/settlements', [PartnerPayoutRecordController::class, 'settlements'])->name('settlements');
        Route::post('/settlements/Add', [PartnerPayoutRecordController::class, 'storeSettlement'])->name('settlements.add');
        Route::get('settlements/search', [PartnerPayoutRecordController::class, 'settlementSearch'])->name('settlements.search');

        Route::get('payment/report/all', [PartnerPaymentLogController::class, 'allReport'])->name('payment.report.all');
        Route::get('payment/report/all/search', [PartnerPaymentLogController::class, 'allReportSearch'])->name('payment.report.all.search');
        Route::get('payment/report/detail/{date}/{gateway}/{status}', [ PartnerPaymentLogController::class, 'reportDetail'])->name('payment.report.detail');
        Route::get('payout/report/detail/{date}/{gateway}/{status}', [PartnerPayoutRecordController::class, 'reportDetail'])->name('payout.report.detail');

        Route::get('settlement/report/daily', [PartnerPayoutRecordController::class, 'dailyReportSettlement'])->name('settlement.report.daily');
        Route::get('settlement/report/daily/search', [PartnerPayoutRecordController::class, 'dailyReportSearchSettlement'])->name('settlement.report.daily.search');
        Route::post('settlement/report/detail', [PartnerPayoutRecordController::class, 'reportDetailSettlement'])->name('settlement.report.detail');


        Route::get('/partner/balance', [PartnerPayoutRecordController::class, 'partnerBalance'])->name('partner.balance');
        Route::get('/reports/export/{from_date?}', [PartnerPaymentLogController::class, 'export_by_blance'])->name('merchant_reports.export_by_blance');
        Route::get('partner/balance/export', [PartnerPayoutRecordController::class,'export_for_blance2'])->name('blance_export_for_partner');
        Route::get('partner/balance/search', [PartnerPayoutRecordController::class, 'partnerBalanceSearch'])->name('partner.balance.search');

        Route::get('reports/partner_account_summary', [PartnerReportsController::class, 'partner_account_summary'])->name('reports.partner_account_summary');
        Route::get('reports/partner_account_balance_summary', [PartnerReportsController::class, 'partner_account_balance_summary'])->name('reports.partner_account_balance_summary');
        Route::get('reports/logs', [PartnerReportsController::class, 'logs'])->name('reports.logs');
        Route::get('reports/export_log', [PartnerReportsController::class, 'export_excel_record'])->name('report.export_excel_record');
        Route::get('merchant/report_by_date', [PartnerMerchantController::class,'report_by_date'])->name('merchant_reports.by_date');
        Route::get('merchant-reports/export', [PartnerMerchantController::class,'export_by_date'])->name('merchant_reports.export_by_date');

        Route::get('reports/completions/logs', [PartnerReportsController::class, 'log_completions'])->name('reports.log_completions');
        Route::get('reports/export_log_completions', [PartnerReportsController::class, 'export_excel_record_completions'])->name('report.export_excel_record_completions');
        Route::get('payment_gateway_performance_report', [PartnerSummaryReportController::class, 'payment_gateway_report'])->name('payment.payment_gateway_report');

        Route::get('merchant/report_by_name', [PartnerMerchantController::class,'report_by_name'])->name('merchant_reports.by_name');
        Route::get('merchant-reports/export_name', [PartnerMerchantController::class,'export_by_name'])->name('merchant_reports.export_by_name');

        Route::get('merchant/report_by_month', [PartnerMerchantController::class, 'report_by_month'])->name('merchant_reports.by_month');
        Route::get('merchant-reports/export_month', [PartnerMerchantController::class, 'export_by_month'])->name('merchant_reports.export_by_month');



        // Route::get('merchant/report_by_month', [MerchantController::class, 'report_by_month'])->name('merchant_reports.by_month');
        // Route::get('merchant-reports/export_month', [MerchantController::class, 'export_by_month'])->name('merchant_reports.export_by_month');


        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');
        Route::post('/partner/profile/update-timezone', [PartnerDashboardController::class, 'updateTimezone'])->name('update_timezone');

        Route::get('payment/report', [PartnerPaymentLogController::class,'report'])->name('payment.report');
        Route::get('payment/report/search', [PartnerPaymentLogController::class,'reportSearch'])->name('payment.report.search');

        Route::get('payment/report/daily', [PartnerPaymentLogController::class,'dailyReport'])->name('payment.report.daily');
        Route::get('payment/report/daily/search', [PartnerPaymentLogController::class,'dailyReportSearch'])->name('payment.report.daily.search');
        Route::get('payment/report/detail/{date}/{gateway}/{status}', [PartnerPaymentLogController::class,'reportDetail'])->name('payment.report.detail');
        Route::get('/payout-request', [PartnerPayoutRecordController::class,'request'])->name('payout-request');

        Route::put('/payout-action/{id}', [Partne\r::class, 'action'])->name('payout-action');

        Route::get('/payout-log/search', [PartnerPayoutRecordController::class,'search'])->name('payout-log.search');
        Route::get('/payout-report', [PartnerPayoutRecordController::class,'report'])->name('payout-report');
        Route::get('/payout-report/search', [PartnerPayoutRecordController::class,'reportSearch'])->name('payout-report.search');
        Route::get('payout/report/daily', [PartnerPayoutRecordController::class,'dailyReport'])->name('payout.report.daily');
        Route::get('payout/report/daily/search', [PartnerPayoutRecordController::class,'dailyReportSearch'])->name('payout.report.daily.search');
        Route::get('payout/report/detail/{date}/{gateway}/{status}', [PartnerPayoutRecordController::class,'reportDetail'])->name('payout.report.detail');


        Route::get('/{username}/url', [PartnerPayoutRecordController::class, 'methods'])->name('methods.get');
        Route::get('/{username}/deposit', [PartnerPayoutRecordController::class, 'depositFund'])->name('depositFund');
        Route::post('/add-fund/open', [PartnerPayoutRecordController::class, 'addFundRequestOpen'])->name('addFund.request.open');
        Route::get('/process/payment', [PartnerPayoutRecordController::class, 'processMyPayment'])->name('addFund.processPayment.open');
        Route::get('/update-fund-order-status/check', [PartnerPayoutRecordController::class, 'update_order_fund_status'])->name('update_fund_order_status.open');


        // Route::get('/{username}/withdrawal', [PartnerPayoutRecordController::class, 'payoutMoneyTransection'])->name('payout.money.transection');
        // Route::post('/withdraw/transection', [PartnerPayoutRecordController::class, 'payoutMoneyRequestTransection'])->name('payout.moneyRequest.transection');

        //Partner Staff Module

        Route::get('/staff', [PartnerManageRolePermissionController::class, 'staff'])->name('staff');
        Route::post('/staff', [PartnerManageRolePermissionController::class, 'storeStaff'])->name('storeStaff');
        Route::put('/staff/{id}', [PartnerManageRolePermissionController::class, 'updateStaff'])->name('updateStaff');
        Route::delete('/apis/delete', [PartnerManageRolePermissionController::class, 'apisDelete'])->name('apis.delete');
        Route::post('/toggle-status/{id}', )->name('toggleStaffStatus');
        Route::get('/apis/reset/{id}', [PartnerManageRolePermissionController::class, 'apisReset'])->name('apis.reset');
    });




});


Route::middleware(['function_track_middleware'])->group(function () {

    Route::get('iframe/{username}/{ewallet}/{acc}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class, 'processTransection'])->name('iframe.open');
    // Route::get('iframe2/{username}/{ewallet}/{acc}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class,'processTransection2'])->name('iframe.open');

    //temp
    Route::get('iframe2/{username}/{ewallet}/{acc}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class,'processTransection4'])->name('iframe.open4');
    Route::post('process/payment4', [PartnerPayoutRecordController::class,'processNextPayment4'])->name('iframe.payment4');

    Route::get('iframe3/{username}/{ewallet}/{amount}/{transection_id?}/{sign?}/{member_id?}', [PartnerPayoutRecordController::class,'processTransection3'])->name('iframe.direct');
    Route::get('process/payment/{id}', [PartnerPayoutRecordController::class,'processNextPayment'])->name('iframe.payment');

    // Route::post('process/payment2', [PartnerPayoutRecordController::class,'processNextPayment2'])->name('iframe.payment2');

    Route::post('process/payment3', [PartnerPayoutRecordController::class,'processNextPayment3'])->name('iframe.payment3');
    Route::post('partner/verify/txn', [PartnerPayoutRecordController::class,'verifytxn'])->name('partner.verify.txn');


});



Route::get('process/update-fund-order-status/iframe/{id}', [PartnerPayoutRecordController::class,'update_order_fund_status_iframe'])->name('update_fund_order_status.iframe');


Route::post('process/iframe/getaccount', [PartnerPayoutRecordController::class,'getaccount'])->name('iframe.getaccount');
Route::post('process/iframe/createpayment', [PartnerPayoutRecordController::class,'createpayment'])->name('iframe.createpayment');


    Route::get('partner/{username}/url', [PartnerPayoutRecordController::class, 'methods'])->name('partner.methods.get');
    Route::get('partner/{username}/deposit', [PartnerPayoutRecordController::class, 'depositFund'])->name('partner.depositFund');
    Route::post('partner/add-fund/open', [PartnerPayoutRecordController::class, 'addFundRequestOpen'])->name('partner.addFund.request.open');
    Route::get('partner/process/payment', [PartnerPayoutRecordController::class, 'processMyPayment'])->name('partner.addFund.processPayment.open');



Route::get('partner/update-fund-order-status/check', [PartnerPayoutRecordController::class,'update_order_fund_status'])->name('partner.update_fund_order_status.open');



    Route::get('partner/{username}/withdrawal', [PartnerPayoutRecordController::class, 'payoutMoneyTransection'])->name('payout.money.transection');
    Route::post('partner/withdraw/transection', [PartnerPayoutRecordController::class, 'payoutMoneyRequestTransection'])->name('partner.payout.moneyRequest.transection');
    Route::get('partner/withdraw/preview/transection', [PartnerPayoutRecordController::class,'payoutPreviewTransection'])->name('partner.payout.preview.transection');
    Route::post('partner/withdraw/preview/transection', [PartnerPayoutRecordController::class, 'payoutRequestSubmitTransection'])->name('partner.payout.submit.transection');


    Route::get('/admin/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ms' , 'cn'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('lang.switch');


