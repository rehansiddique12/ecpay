<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PayoutRecordController;
use App\Http\Controllers\Admin\TelegramGroupController;
use App\Http\Controllers\Admin\ManageRolePermissionController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\LoginController as PartnerLoginController;

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


    });
    Route::group(['middleware' => ['auth:partner']], function () {


        Route::get('/profile', [PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PartnerDashboardController::class, 'profileUpdate'])->name('profileUpdate');
        Route::get('/password', [PartnerDashboardController::class, 'password'])->name('password');
        Route::put('/password', [PartnerDashboardController::class, 'passwordUpdate'])->name('passwordUpdate');
        Route::post('/logout', [PartnerLoginController::class, 'logout'])->name('logout');
    });


});

