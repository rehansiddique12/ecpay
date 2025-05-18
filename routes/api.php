<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PayoutRecordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/paymentGateway', [PaymentController::class, 'paymentGateway']);
Route::post('/paymentGatewayInfo', [PaymentController::class, 'paymentGatewayInfo']);
Route::post('/uploadReceipt', [PaymentController::class, 'uploadReceipt']);
Route::post('/lastPaymentDetail', [PaymentController::class, 'lastPaymentDetail']);
Route::get('/payoutGateway', [PayoutRecordController::class, 'payoutGateway']);

Route::post('/addPayout', [PayoutRecordController::class, 'addPayout']);
Route::post('/lastPayoutDetail', [PayoutRecordController::class, 'lastPayoutDetail']);
Route::get('/allPayoutInfo', [PayoutRecordController::class, 'allPayoutInfo']);
Route::post('/addPayoutInfo', [PayoutRecordController::class, 'addPayoutInfo']);
Route::post('/rejectPayoutInfo', [PayoutRecordController::class, 'rejectPayoutInfo']);

Route::post('/verifyPayment', [PaymentLogController::class, 'verifyPayment']);


Route::post('/addPaymentInfo', [PaymentLogController::class, 'addPaymentInfo']);
