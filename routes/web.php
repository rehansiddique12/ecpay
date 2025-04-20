<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Partner\MerchantController;
use App\Http\Controllers\Admin\ManualGatewayController;
use App\Http\Controllers\Admin\PayoutRecordController;
use App\Http\Controllers\Admin\TelegramGroupController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ManageRolePermissionController;
use App\Http\Controllers\Partner\LoginController as PartnerLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
// rehan
use App\Http\Controllers\Admin\ReportsController;


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
        'message' => 'All caches cleared successfully!'
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

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [LoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth:admin', 'permission']], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/staff', [ManageRolePermissionController::class, 'staff'])->name('staff');
        Route::post('/staff', [ManageRolePermissionController::class, 'storeStaff'])->name('storeStaff');
        Route::put('/staff/{admin}', [ManageRolePermissionController::class, 'updateStaff'])->name('updateStaff');
        // Parant Routs
        Route::get('/parent', [ParentController::class, 'parant'])->name('parant');
        Route::get('/workboard', [PayoutRecordController::class, 'workboard'])->name('workboard');

        // rehan Reports:
        Route::get('reports/cal', [ReportsController::class,'cal'])->name('reports.cal');
        Route::get('reports/logs', [ReportsController::class,'logs'])->name('reports.logs');
        Route::get('reports/cal2', [ReportsController::class,'cal2'])->name('reports.cal2');
        Route::get('reports/master_report', [ReportsController::class,'master_report'])->name('reports.master_report');
        Route::get('reports/revenue_center', [ReportsController::class,'revenue_center'])->name('reports.revenue_center');
        Route::get('reports/live_ewallet_balance', [ReportsController::class,'live_ewallet_balance'])->name('reports.live_ewallet_balance');
        Route::get('reports/daily_ewallet_summary', [ReportsController::class,'daily_ewallet_summary'])->name('reports.daily_ewallet_summary');
        Route::get('reports/partner_account_summary', [ReportsController::class,'partner_account_summary'])->name('reports.partner_account_summary');
        Route::get('reports/merchant_charges_summary', [ReportsController::class,'merchant_charges_summary'])->name('reports.merchant_charges_summary');
        Route::get('reports/daily_transection_summary', [ReportsController::class,'daily_transection_summary'])->name('reports.daily_transection_summary');
        Route::get('payment_gateway_performance_report', [PaymentMethodController::class,'payment_gateway_report'])->name('payment.payment_gateway_report');
        Route::get('reports/merchant_charges_summary/search', [ReportsController::class,'merchant_charges_summary_search'])->name('reports.merchant_charges_summary.search');
        Route::get('reports/partner_account_balance_summary', [ReportsController::class,'partner_account_balance_summary'])->name('reports.partner_account_balance_summary');
        Route::get('reports/partner_account_balance_summary_completions', [ReportsController::class,'partner_account_balance_summary_completions'])->name('reports.partner_account_balance_summary_completions');



        /* ===== Merchant Ticket ====*/



        Route::get('/apis', [PayoutRecordController::class,'apis'])->name('apis');
        Route::post('/apis/add', [PayoutRecordController::class,'apisAdd'])->name('apis.add');
        Route::post('/apis/add-by-parent', [PayoutRecordController::class,'apisAddByParent'])->name('apis.addByParent');
        Route::delete('/apis/delete/{id}', [PayoutRecordController::class,'apisDelete'])->name('apis.delete');
        Route::get('/apis/login/{id}', [PayoutRecordController::class,'apisLgoin'])->name('apis.login');
        Route::get('/apis/reset/{id}', [PayoutRecordController::class,'apisReset'])->name('apis.reset');
        Route::get('/apis/commission/{id}', [PayoutRecordController::class,'apisCommission'])->name('apis.commission');
        Route::get('/api/commissions/detail/{id}', [PayoutRecordController::class,'apiCommissionsDetail'])->name('api.commissions.detail');
        Route::get('/api/commissions/calculate/{id}', [PayoutRecordController::class,'apiCommissionsCalculate'])->name('api.commissions.calculate');
        Route::put('/apis/update/{id}', [PayoutRecordController::class,'updateApi'])->name('apis.update');
        Route::post('/apis/balance/add', [PayoutRecordController::class,'apis/balance/add'])->name('apis.balance.add');
        Route::post('/apis/commission/add', [PayoutRecordController::class,'apisCommissionAdd'])->name('apis.commission.add');

        Route::get('/apis/balance/add', [PayoutRecordController::class,'apisBalanceAddGet'])->name('apis.balance.add.get');
        Route::get('/groups', [TelegramGroupController::class,'groups'])->name('groups');
        Route::post('/groups/add', [TelegramGroupController::class,'groupsAdd'])->name('groups.add');
        Route::put('/groups/update/{id}', [TelegramGroupController::class,'updateGroup'])->name('groups.update');
        Route::delete('/groups/delete/{id}', [TelegramGroupController::class,'groupsDelete'])->name('groups.delete');


        Route::get('/settlements', [PayoutRecordController::class,'settlements'])->name('settlements');
        Route::post('/settlements/Add', [PayoutRecordController::class,'storeSettlement'])->name('settlements.add');
        Route::get('settlements/search', [PayoutRecordController::class,'settlementSearch'])->name('settlements.search');
        Route::get('/settlements/reject/{id}', [PayoutRecordController::class,'rejectSettlement'])->name('settlements.reject');
        Route::get('/settlements/approve/{id}', [PayoutRecordController::class,'approveSettlement'])->name('settlements.approve');

        // Acconts:
        Route::get('/accounts', [PayoutRecordController::class,'allAccounts'])->name('accounts');
        Route::get('/accounts/edit/{id}', [PayoutRecordController::class,'editAccount'])->name('accounts.edit');
        Route::post('/update-status/{id}', [PayoutRecordController::class,'updateStatus'])->name('update.status');
        Route::get('/accounts/charges/{id}', [PayoutRecordController::class,'accountCharges'])->name('accounts.charges');
        Route::post('/accounts/balance/add', [PayoutRecordController::class,'accountBalanceAdd'])->name('account.balance.add');
        Route::delete('/merchant/delete/{account}', [PayoutRecordController::class,'merchantDelete'])->name('merchant.delete');
        Route::post('/accounts/balance/edit', [PayoutRecordController::class,'accountBalanceEdit'])->name('account.balance.edit');

        //Add Accounts
        Route::get('/accounts/add', [PayoutRecordController::class,'addAccount'])->name('accounts.add');
        Route::post('/accounts/create', [PayoutRecordController::class,'createAccount'])->name('accounts.create');
        Route::put('/accounts/update/{id}', [PayoutRecordController::class,'updateAccount'])->name('accounts.update');
        Route::post('/accounts/charges/add',[PayoutRecordController::class,'accountChargesAdd'])->name('accounts.charges.add');

        Route::get('/merchant', [PayoutRecordController::class,'merchant'])->name('merchant');
        Route::post('/merchant/add', [PayoutRecordController::class,'merchantAdd'])->name('merchant.add');

        Route::get('/balance/logs', [PayoutRecordController::class,'balanceLogs'])->name('balance.logs');
        Route::get('balance/logs/search', [PayoutRecordController::class,'balanceLogsSearch'])->name('balance.logs.search');


        Route::get('/transfer/balance', [PayoutRecordController::class,'transferBalance'])->name('transfer.balance');
        Route::post('/transfer/balance/add', [PayoutRecordController::class,'transferBalanceAdd'])->name('transfer.balance.add');


        Route::get('/profile', [AdminDashboardController::class,'profile'])->name('profile');
        Route::put('/profile', [AdminDashboardController::class,'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [AdminDashboardController::class,'password'])->name('password');
        Route::put('/password', [AdminDashboardController::class,'passwordUpdate'])->name('passwordUpdate');

        Route::get('payment/log', [PaymentLogController::class,'index'])->name('payment.log');
        Route::get('payment/search', [PaymentLogController::class,'search'])->name('payment.search');
        Route::put('payment/update_e_wallet', [PaymentLogController::class,'update_e_wallet'])->name('payment.update_e_wallet');
        Route::post('/accounts/run/callback/deposit', [PaymentLogController::class,'runCallback'])->name('run.deposit.callback');

        Route::get('/user/edit/{id}', [UsersController::class,'userEdit'])->name('user-edit');
        Route::put('payment/action/{id}', [PaymentLogController::class,'action'])->name('payment.action');

        Route::get('/payout-log', [PayoutRecordController::class,'index'])->name('payout-log');
        Route::get('/payout-log/search', [PayoutRecordController::class,'search'])->name('payout-log.search');
        Route::put('payout/update_e_wallet', [PayoutRecordController::class,'update_e_wallet'])->name('payout.update_e_wallet');
        Route::post('/accounts/run/callback', [PayoutRecordController::class,'runCallback'])->name('run.callback');
        Route::get('/payout-report/get-notification', [PayoutRecordController::class,'getNotification'])->name('payout-report.getnotification');
        Route::get('/payout-request', [PayoutRecordController::class,'request'])->name('payout-request');
        Route::put('/payout-action/{id}', [PayoutRecordController::class,'action'])->name('payout-action');

        Route::get('payment/apiLog', [PaymentLogController::class,'apiLog'])->name('payment.apiLog');
        Route::get('payment/apisearch', [PaymentLogController::class,'apisearch'])->name('payment.apisearch');
        Route::get('payment/apiLogUnclaimed', [PaymentLogController::class,'apiLogUnclaimed'])->name('payment.apiLogunclaimed');
        Route::get('payment/apiLogUnclaimed/search', [PaymentLogController::class,'apiLogUnclaimedsearch'])->name('payment.apiLogunclaimed.search');

        Route::get('payment/report', [PaymentLogController::Class,'report'])->name('payment.report');
        Route::get('payment/report/search', [PaymentLogController::class,'reportSearch'])->name('payment.report.search');
        Route::get('payment/report/daily', [PaymentLogController::class,'dailyReport'])->name('payment.report.daily');
        Route::get('payment/report/daily/search', [PaymentLogController::class,'dailyReportSearch'])->name('payment.report.daily.search');
        Route::get('payment/report/all', [PaymentLogController::class,'allReport'])->name('payment.report.all');
        Route::get('payment/report/all/search', [PaymentLogController::class,'allReportSearch'])->name('payment.report.all.search');
        Route::get('payment/report/detail/{date}/{gateway}/{status}', [PaymentLogController::class,'reportDetail'])->name('payment.report.detail');
        Route::get('payout/report/detail/{date}/{gateway}/{status}', [PayoutRecordController::class,'reportDetail'])->name('payout.report.detail');
        Route::get('/payout-report', [PayoutRecordController::class,'report'])->name('payout-report');
        Route::get('/payout-report/search', [PayoutRecordController::class,'reportSearch'])->name('payout-report.search');
        Route::get('payout/report/daily', [PayoutRecordController::class,'dailyReport'])->name('payout.report.daily');
        Route::get('payout/report/daily/search', [PayoutRecordController::class,'dailyReportSearch'])->name('payout.report.daily.search');

         // Manual Methods
         Route::get('payment-methods/manual', [ManualGatewayController::class,'index'])->name('deposit.manual.index');
         Route::get('payment-methods/manual/new', [ManualGatewayController::class,'create'])->name('deposit.manual.create');
         Route::post('payment-methods/manual/new', [ManualGatewayController::class,'store'])->name('deposit.manual.store');
         Route::get('payment-methods/manual/edit/{id}', [ManualGatewayController::class,'edit'])->name('deposit.manual.edit');
         Route::put('payment-methods/manual/update/{id}', [ManualGatewayController::class,'update'])->name('deposit.manual.update');
         Route::post('payment-methods/deactivate', [PaymentMethodController::class,'deactivate'])->name('payment.methods.deactivate');
         Route::get('payment-methods/deactivate', [PaymentMethodController::class,'deactivate'])->name('payment.methods.deactivate');

         Route::get('/e-wallet/accounts', [PayoutRecordController::class,'eWalletAccounts'])->name('ewallet.accounts');
         Route::get('/e_wallet_accounts/{id}/toggle-status', [PayoutRecordController::class,'toggleStatus'])->name('e_wallet_accounts.toggle_status');
         Route::delete('/e-wallet/admin/delete/{account}', [PayoutRecordController::class,'adminAccountDelete'])->name('ewallet.accounts.delete');
         Route::post('/accounts/deposit/test', [PayoutRecordController::class,'depositTest'])->name('deposit.test');
         Route::post('/e-wallet/admin/add', [PayoutRecordController::class,'eWalletAccountsAdd'])->name('ewallet.accounts.add');
         Route::post('/accounts/deposit/testp', [PayoutRecordController::class,'depositTestp'])->name('deposit.testp');
         Route::post('/accounts/withdrawal/test', [PayoutRecordController::class,'withdrawalTest'])->name('withdrawal.test');
         Route::post('/accounts/withdrawal/testp', [PayoutRecordController::class,'withdrawalTestp'])->name('withdrawal.testp');



    });

});
//partnerRoutes
Route::group(['prefix' => 'partner', 'as' => 'partner.'], function () {
    Route::middleware(['guest:partner'])->group(function () {
        Route::get('/', [PartnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [PartnerLoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth:partner', 'permission_partner']], function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/twoFA', [PartnerDashboardController::class, 'twoFA'])->name('twoFA');
        Route::post('/twoFA', [PartnerDashboardController::class, 'updateTwoFA'])->name('twoFA.update');
        Route::get('/twoFA/disable', [PartnerDashboardController::class, 'disableTwoFA'])->name('twoFA.disable');



        Route::get('merchant/report_by_date', [MerchantController::class,'report_by_date'])->name('merchant_reports.by_date');
        Route::get('merchant-reports/export', [MerchantController::class,'export_by_date'])->name('merchant_reports.export_by_date');

        Route::get('merchant/report_by_name', [MerchantController::class,'report_by_name'])->name('merchant_reports.by_name');
         Route::get('merchant-reports/export_name', [MerchantController::class,'export_by_name'])->name('merchant_reports.export_by_name');



    });
    Route::group(['middleware' => ['auth:partner']], function () {


        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');
    });


});

