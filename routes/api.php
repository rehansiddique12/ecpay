<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Webhook\WebhookController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\Admin\PayoutRecordController;
use App\Http\Controllers\Admin\TelegramGroupController;
use App\Http\Controllers\Partner\PayoutRecordController as PartnerPayoutRecordController;

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


Route::post('/direct/webhook/{source}/{acc}/{type}', [PaymentLogController::class, 'directwebhook']);
Route::post('/sms/webhook/{source}/{acc}/{type}', [WebhookController::class, 'webhook']);

Route::get('/paymentGateway', [PaymentController::class, 'paymentGateway']);


Route::post('/lastPaymentDetail', [PaymentController::class, 'lastPaymentDetail']);
Route::get('/payoutGateway', [PayoutRecordController::class, 'payoutGateway']);


Route::post('/lastPayoutDetail', [PayoutRecordController::class, 'lastPayoutDetail']);
Route::get('/allPayoutInfo', [PayoutRecordController::class, 'allPayoutInfo']);






Route::post('/addPaymentInfo', [PaymentLogController::class, 'addPaymentInfo']);
Route::post('/updatePayment', [PaymentLogController::class, 'updatePayment']);

Route::post('/payment/callback', [PaymentLogController::class, 'payment_callback']);
Route::post('/payout/callback', [PayoutRecordController::class, 'payout_callback']);




Route::post('/partner/telegram/webhook', [TelegramGroupController::class, 'telegramwebhook']);

Route::middleware('api_logs_middleware')->group(function () {
    Route::post('/verifyPayment', [PaymentLogController::class, 'verifyPayment']);
    Route::post('/paymentGatewayInfo', [PaymentController::class, 'paymentGatewayInfo']);
    Route::post('/uploadReceipt', [PaymentController::class, 'uploadReceipt']);
    Route::post('/rejectPayoutInfo', [PayoutRecordController::class, 'rejectPayoutInfo']);
    Route::post('/addPayout', [PayoutRecordController::class, 'addPayout']);
    Route::post('/addPayoutInfo', [PayoutRecordController::class, 'addPayoutInfo']);
    Route::post('/checkBalance', [PaymentController::class, 'checkBalance']);


    Route::post('/updateAccountBalance', [PayoutRecordController::class, 'updateAccountBalance']);

    

    Route::match(['get','post'],'/bkashcallback', [PartnerPayoutRecordController::class, 'bkashcallback']);  
});