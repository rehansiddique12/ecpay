<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Log;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\Commission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TelegramGroup;


use App\Models\EWalletAccount;
use App\Models\PendingPayment;
use App\Models\ParentCommission;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use App\Models\DailyPartnerSummary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\DailyPartnerSummaryLog;
use Illuminate\Support\Facades\Log as LaravelLog;

class TelegramGroupController extends Controller
{
    private $messages = [
    'en' => [
        'checkbalance' => "Hello %s, Your Current Balance is: %s.",
        'lang_selected' => "? English Language Selected",
        'lang_invalid' => "? Your request format is not correct!\n\n? Use the following commands:\n/lang en => To Select English \n/lang ch => To Select Chinese",
        'transaction_completed' => "Your transaction has been marked as completed, and the callback has also been sent.\n\n*Merchant Order:* `%s`\n*Order Id:* `%s`\n*Transaction ID:* `%s`\n*Amount:* `%s`\n*Status:* `Complete`",
        'transaction_pending' => "Transaction in pending state. Please wait a moment while our customer service check on this transaction.",
        'transaction_not_found' => "The entered ticket number does not match our records. Kindly check your ticket number.",
        'image_error' => "Image Processing Error! Try Again. Please Attach clear image and add caption /checkorder XXX123XXX  for further checking.",
        'service_error' => "Service Error! Try Again. Please send image and add caption /checkorder XXX123XXX  for further checking.",
        'invalid_command' => "Your request format is not correct!\n\nUse the following commands:\n/checkbalance – to check your balance\n/checkorder XXX123XXX – to check your order status (Please attach Transaction image to check)",
        'order_processing' => "Processing your order. Please wait...",
        'order_verified' => "Order verified successfully!",
        'order_not_found' => "Order not found. Please check your order number.",
        'image_processing' => "Processing your image. Please wait...",
        'image_verification' => "Image verification in progress...",
        'transaction_already_verified' => "This transaction has already been verified at %s",
        'transaction_claimed' => "We are sorry. This transaction has already been claimed at %s by other merchant",
        'transaction_pending_cs' => "Transaction in pending state. Please wait a moment while our customer service check on this transaction.",
        'transaction_duplicate' => "?? *Duplicate Transaction Alert* ??\n\n*Merchant Order:* `%s`\n*Transaction ID:* `%s`\n*Amount:* `%s`\n*Phone:* `%s`\n*Remark:* Transaction already verified with another order.\n*Status:* `Complete`",
        'transaction_not_found_support' => "?? *Transaction Not Found* ??\n\n*Merchant Order:* `%s`\n*Order Id:* `%s`\n*Transaction ID:* `%s`\n*Amount:* `%s`\n*Remark:* Transaction processed and callback sent.\n*Status:* `Not Found`",
        'transaction_completion_details' => "This transaction has already been claimed by other merchants %s",
        'request_pending' => "Your request has been sent and is in a pending state. Please contact the administrator!",
        'transaction_completed_again' => " The transaction has been completed and callback sent.",
        'transaction_rejected' => "Your transaction status is rejected and callback sent.",
        'transaction_pending_customer' => "The transaction is in pending state. Please hold on while we transfer your request to our customer service.",
        'extracted_info' => "?? *Extracted Information:*\n\n",
        'transaction_completed_callback' => "The transaction has been completed and callback sent.",
        'transaction_rejected_callback' => "The transaction has been rejected and callback sent.",
        'transaction_pending_callback' => "The transaction is in pending state. Please hold on while we transfer your request to our customer service.",
        'ticket_not_found' => "The entered ticket number does not match our records. Kindly check your ticket number.",
        'invalid_request_format' => "Your request format is not correct!\n\nUse the following commands:\n/checkbalance – to check your balance\n/checkorder XXX123XXX – to check your order status (Please attach Transaction image to check)",
        'empty_message' => "",
        'phone_number_empty' => "",
        'image_processing_error_retry' => "Image Processing Error! Try Again. Please Attach clear image and add caption /checkorder XXX123XXX  for further checking.",
        'transaction_pending_customer_service' => "The transaction is in pending state. Please hold on while we transfer your request to our customer service.",
        'support_message' => "?? *Support Message* ??\n\n*Merchant Order:* `%s`\n*Order Id:* `%s`\n*Transaction ID:* `%s`\n*Amount:* `%s`\n*Phone:* `%s`\n*Remark:* %s\n*Status:* `%s`",
        'transaction_processed' => "Your transaction has been processed successfully.",
        'transaction_verification' => "Transaction verification in progress...",
        'transaction_details' => "*Transaction Details:*\n*Order ID:* `%s`\n*Amount:* `%s`\n*Status:* `%s`",
        'payment_processing' => "Payment processing in progress...",
        'payment_completed' => "Payment completed successfully.",
        'payment_failed' => "Payment processing failed. Please try again.",
        'invalid_image_format' => "Invalid image format. Please send a clear image.",
        'processing_error' => "An error occurred while processing your request. Please try again.",
        'verification_success' => "Verification completed successfully.",
        'verification_failed' => "Verification failed. Please try again.",
        'callback_sent' => "Callback has been sent successfully.",
        'callback_failed' => "Failed to send callback. Please try again.",
        'transaction_rejected_with_reason' => "The transaction has been rejected and callback sent.\nReason: %s",
        'transaction_already_completed_in_order' => "This transaction has been completed in Order %s.",
        'account_not_belong' => "Account does not belong to us. Please attach image with correct information.",
    ],
    'ch' => [
        'checkbalance' => "你好 %s, 您的当前余额是: %s.",
        'lang_selected' => "✓ 已选择中文",
        'lang_invalid' => "❌ 您的请求格式不正确！\n\n✓ 使用以下命令：\n/lang en => 选择英文 \n/lang ch => 选择中文",
        'transaction_completed' => "交易已完成，回调已发送。\n\n*商户订单:* `%s`\n*订单号:* `%s`\n*交易号:* `%s`\n*金额:* `%s`\n*状态:* `完成`",
        'transaction_pending' => "交易正在处理中，请稍候客服检查。",
        'transaction_not_found' => "输入的订单号与记录不匹配，请检查您的订单号。",
        'image_error' => "图片处理错误！请重试。请附上清晰的图片并添加说明 /checkorder XXX123XXX 以便进一步检查。",
        'service_error' => "服务错误！请重试。请发送图片并添加说明 /checkorder XXX123XXX 以便进一步检查。",
        'invalid_command' => "您的请求格式不正确！\n\n使用以下命令：\n/checkbalance – 查询余额\n/checkorder XXX123XXX – 查询订单状态（请附上交易图片以便查询）",
        'order_processing' => "正在处理您的订单，请稍候...",
        'order_verified' => "订单验证成功！",
        'order_not_found' => "未找到订单，请检查您的订单号。",
        'image_processing' => "正在处理您的图片，请稍候...",
        'image_verification' => "图片验证进行中...",
        'transaction_already_verified' => "此交易已在 %s 验证",
        'transaction_claimed' => "抱歉，此交易已被其他商户在 %s 认领",
        'transaction_pending_cs' => "交易正在处理中，请稍候客服检查。",
        'transaction_duplicate' => "⚠️ *重复交易提醒* ⚠️\n\n*商户订单:* `%s`\n*交易号:* `%s`\n*金额:* `%s`\n*电话:* `%s`\n*备注:* 交易已被其他订单验证。\n*状态:* `完成`",
        'transaction_not_found_support' => "⚠️ *未找到交易* ⚠️\n\n*商户订单:* `%s`\n*订单号:* `%s`\n*交易号:* `%s`\n*金额:* `%s`\n*备注:* 交易已处理并发送回调。\n*状态:* `未找到`",
        'transaction_completion_details' => "抱歉，此交易已在 %s 认领",
        'request_pending' => "您的请求已发送并处于待处理状态，请联系管理员！",
        'transaction_completed_again' => "交易已完成，回调已再次发送。",
        'transaction_rejected' => "您的交易状态已被拒绝，回调已发送。",
        'transaction_pending_customer' => "交易正在处理中，请稍候我们将您的请求转给客服。",
        'extracted_info' => "📝 *提取的信息:*\n\n",
        'transaction_completed_callback' => "交易已完成，回调已发送。",
        'transaction_rejected_callback' => "交易已拒绝，回调已发送。",
        'transaction_pending_callback' => "交易正在处理中，请稍候我们将您的请求转给客服。",
        'ticket_not_found' => "输入的票据号与记录不匹配，请检查您的票据号。",
        'invalid_request_format' => "您的请求格式不正确！\n\n使用以下命令：\n/checkbalance – 查询余额\n/checkorder XXX123XXX – 查询订单状态（请附上交易图片以便查询）",
        'empty_message' => "",
        'phone_number_empty' => "",
        'image_processing_error_retry' => "图片处理错误！请重试。请附上清晰的图片并添加说明 /checkorder XXX123XXX 以便进一步检查。",
        'transaction_pending_customer_service' => "交易正在处理中，请稍候我们将您的请求转给客服。",
        'support_message' => "📞 *支持消息* 📞\n\n*商户订单:* `%s`\n*订单号:* `%s`\n*交易号:* `%s`\n*金额:* `%s`\n*电话:* `%s`\n*备注:* %s\n*状态:* `%s`",
        'transaction_processed' => "交易已处理成功。",
        'transaction_verification' => "交易验证进行中...",
        'transaction_details' => "*交易详情:*\n*订单号:* `%s`\n*金额:* `%s`\n*状态:* `%s`",
        'payment_processing' => "支付处理中...",
        'payment_completed' => "支付已完成。",
        'payment_failed' => "支付处理失败，请重试。",
        'invalid_image_format' => "无效的图片格式，请发送清晰的图片。",
        'processing_error' => "处理您的请求时出错，请重试。",
        'verification_success' => "验证成功完成。",
        'verification_failed' => "验证失败，请重试。",
        'callback_sent' => "回调已成功发送。",
        'callback_failed' => "发送回调失败，请重试。",
        'transaction_rejected_with_reason' => "The transaction has been rejected and callback sent.\nReason: %s",
        'transaction_already_completed_in_order' => "此交易已在订单 %s 中完成。",
        'account_not_belong' => "账户不属于我们。请附上正确的信息图片。",
    ]
    ];



    const TELEGRAM_BOT_TOKEN = '7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4';
    const PENDING_MESSAGE = 'Your request has been sent and is in a pending state. Please contact the administrator!';
    const TRANSACTION_STATUS_MESSAGES = [
    1 => 'The transaction has been completed and callback sent.',
    3 => 'The transaction has been rejected and callback sent.',
    'Complete' => 'The transaction has been completed and callback sent.',
    'Reject' => 'The transaction has been rejected and callback sent.',
    ];

    public function groups(Request $request)
    {
    $records = TelegramGroup::paginate('20');
    $partners = Api::where('type', 'Admin')->pluck('name', 'id');
    $pageTitle = $title = __('accounts.manage_telegram_groups');

    return view('admin.group.api', compact('records', 'pageTitle', 'title', 'partners'));
    }

    public function toggleStatus($id)
    {
    $group = TelegramGroup::findOrFail($id);
    $group->status = !$group->status;
    $group->save();

    return response()->json([
        'success' => true,
        'status' => $group->status ? 'Active' : 'Inactive'
    ]);
    }

    public function groupsAdd(Request $request)
    {
    $validated = $request->validate($this->validationRules());

    TelegramGroup::create($validated);

    return back()->with('success', 'Added Successfully');
    }

    public function updateGroup(Request $request, $id)
    {
    $validated = $request->validate($this->validationRules());

    $group = TelegramGroup::findOrFail($id);
    $group->update($validated);

    return back()->with('success', 'Group Updated Successfully');
    }

    public function groupsDelete($id)
    {
    TelegramGroup::findOrFail($id)->delete();
    return redirect()->route('admin.groups')->with('success', 'Group deleted successfully.');
    }


    public function telegramwebhook(Request $request){
    LaravelLog::info('Telegram function loaded');

    try {
        $data = file_get_contents('php://input');
        $array = json_decode($data, true);
        
        // Add message deduplication check
        if(isset($array['message']['message_id'])) {
            $messageId = $array['message']['message_id'];
            $cacheKey = 'telegram_message_' . $messageId;
            
            // Check if we've already processed this message
            if (Cache::has($cacheKey)) {
                LaravelLog::info("Duplicate message received: " . $messageId);
                return response()->json(['status' => 'success'], 200);
            }
            
            // Mark message as processed
            Cache::put($cacheKey, true, now()->addHours(24));
        }
        
        if(isset($array['message'])){
            $TG_message = $array['message'];
        }elseif(isset($array['edited_message'])){
            $TG_message = $array['edited_message'];
        }

        if(isset($TG_message)){
            $sender_chat = $TG_message['chat'];
            $api = TelegramGroup::where('group_username',$sender_chat)->first();
            if(!$api){
                $title = "";
                if(isset($sender_chat['title'])){
                    $title = $sender_chat['title'];
                }elseif(isset($sender_chat['first_name']) && isset($sender_chat['last_name'])){
                    $title = $sender_chat['first_name'] . " " . $sender_chat['last_name'];
                }
                $api = new TelegramGroup;
                $api->group_name = $title;
                $api->group_username = $sender_chat['id'];
                $api->status = 0;
                $api->save();
            }

            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // Add timeout and retry logic for Telegram API calls
            $maxRetries = 3;
            $timeout = 10; // seconds
            $retryDelay = 1; // seconds

            $sendMessage = function($params) use ($url, $maxRetries, $timeout, $retryDelay) {
                $attempt = 0;
                while ($attempt < $maxRetries) {
                    try {
                        $response = Http::timeout($timeout)->post($url, $params);
                        if ($response->successful()) {
                            return $response;
                        }
                        LaravelLog::warning("Telegram API call failed (attempt " . ($attempt + 1) . "): " . $response->body());
                    } catch (\Exception $e) {
                        LaravelLog::error("Telegram API exception (attempt " . ($attempt + 1) . "): " . $e->getMessage());
                    }
                    $attempt++;
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                    }
                }
                throw new \Exception("Failed to send message after $maxRetries attempts");
            };

            // Use the new sendMessage function for all Telegram API calls
            if(empty($api->api_id) || $api->api_id==0 || $api->status==0){
                $message = $this->messages[$api->lang]['request_pending'];
                try {
                    $sendMessage([
                        'chat_id' => $sender_chat['id'],
                        'text' => $message,
                        'reply_to_message_id' => $TG_message['message_id'],
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Exception $e) {
                    LaravelLog::error("Failed to send pending message: " . $e->getMessage());
                }
            } else {
                if(isset($TG_message['text'])){
                    $sender_message = $TG_message['text'];
                }elseif(isset($TG_message['caption'])){
                    $sender_message = $TG_message['caption'];
                }

                $api_key = Api::where('id', $api->api_id)->first();
                $lowercaseText = strtolower($sender_message);
                if($lowercaseText=="checkbalance" || $lowercaseText=="/checkbalance"){
                    if (!$api_key) {
                        $message = $this->messages[$api->lang]['request_pending'];
                    } else {
                        $message = sprintf($this->messages[$api->lang]['checkbalance'], $api_key->name, $api_key->balance);
                    }
                    try {
                        $sendMessage([
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    } catch (\Exception $e) {
                        LaravelLog::error("Failed to send balance message: " . $e->getMessage());
                    }
                }elseif(strpos($lowercaseText, "/lang") === 0){
                    $parts = explode(" ", $sender_message);
                    if(count($parts) >= 2) {
                        $language = trim($parts[1]);
                        if($language=="en" || $language=="ch"){
                            $api->lang = $language;
                            $api->save();
                            
                            $message = $this->messages[$language]['lang_selected'];
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }else{
                            $message = $this->messages[$api->lang]['lang_invalid'];
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }
                    }else{
                        $message = $this->messages[$api->lang]['lang_invalid'];
                        $response = Http::post($url, [
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                }
                elseif(strpos($lowercaseText, "/checkorder") === 0){
                    
                    $parts = explode(" ", $sender_message);
                    $extractedText = '';
                    
                    if(count($parts) >= 2) {
                        $orderNumber = trim($parts[1]);
                    
                    
                        $deposit = Payment::where('partner_transection_id',$orderNumber)->where('api_id',$api->api_id)->with('gateway')->latest()->first();
                        if($deposit){
                            if($deposit->status=="Complete"){
                                $message = $this->messages[$api->lang]['transaction_completed_again'];
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }elseif($deposit->status=="Reject"){
                                $message = $this->messages[$api->lang]['transaction_rejected'];
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }else{
                                // $message = "The transaction is in pending state. Please hold on while we transfer your request to our customer service.";
                                
                                if (isset($TG_message['photo'])) {
                                    $image_processed = 0;
                                    try {
                                        $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                        $photo = end($TG_message['photo']);
                                        $file_id = $photo['file_id'];
                                        LaravelLog::info("Got file_id: $file_id");
                            
                                        // Get file info from Telegram
                                        $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                        LaravelLog::info("Requesting file info from: $getFileUrl");
                                        $fileData = Http::get($getFileUrl)->json();
                                        LaravelLog::info("File info response: " . json_encode($fileData));
                            
                                        if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                            $file_path = $fileData['result']['file_path'];
                                            $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                            LaravelLog::info("Downloading image from: $fileUrl");
                            
                                            // Use cURL to fetch the image data because allow_url_fopen is disabled
                                            
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                            $imageContent = curl_exec($ch);
                                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                            curl_close($ch);
                            
                                            if ($imageContent && $httpCode === 200) {
                                                
                                                $tempPath = 'ocr_' . time() . '.jpg';
                                                $tempImagePath = storage_path('app/public/ocr_images/' . $tempPath);
                                                file_put_contents($tempImagePath, $imageContent);
                                                $imageUrl = url('storage/app/public/ocr_images/' . $tempPath);
                                                LaravelLog::info("Image saved temporarily at: $tempImagePath");
                                                LaravelLog::info("Image saved temporarily at: $imageUrl");
                                                
                                                $ocrtext = "";

                                                $response = Http::withHeaders([
                                                    'Content-Type' => 'application/json',
                                                ])->post('http://89.46.62.251/ocr/api/applyocr', [
                                                    'imageurl' => $imageUrl,
                                                ]);
                                                
                                                
                                                
                                                LaravelLog::info("OCR API Raw Response: " . $response);
                                                
                                                
                                                if ($response->successful()) {
                                                    $ocr_response = $response->json();
                                                    
                                                    if(isset($ocr_response['ocr_text'])){
                                                    $ocrtext = $ocr_response['ocr_text'];  
                                                    }

                                                } else {
                                                    
                                                    $message = 'Unexpected error occurred.';
                                                    $response = Http::post($url, [
                                                        'chat_id' => $sender_chat['id'],
                                                        'text' => $message,
                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                        'parse_mode' => 'Markdown',
                                                    ]);
                                                }
                                                
                                                LaravelLog::info("OCR API Raw Response: " . $ocrtext);
                                                
                                                
                                                
                                                
                                            
                            
                                                try {
                                                    
                                                    $extractedText = $ocrtext;
                                                    
                                                    if (isset($extractedText)) {
                                                        
                                                        LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                        
                                                        
                                                        if (isset($extractedText)) {
                                                            
                                                                
                                                                
                                                                // Initialize the gateway_name variable with a default value
                                                                $gateway_name = $deposit->gateway->name ?? '';
                                                                if (!empty($deposit->gateway) && !empty($deposit->gateway->name)) {
                                                                    $gateway_name = $deposit->gateway->name;
                                                                }
                                                            
                                                            
                                                                $extracted_text_values = $this->extractTransactionDetails($extractedText);

                                                                // For Rocket, use a simpler and more direct approach to get Transaction ID
                                                                // Remove any previous Transaction ID extraction that might be causing issues
                                                                $txnId = $extracted_text_values['txn'];
                                                                $amount = $extracted_text_values['amount'];
                                                                $phone_number = $extracted_text_values['ewallet'];
                                                                
                                                                
                                                                
                                                                
                                                                // Format the message with extracted information
                                                                $message = "?? *Extracted Information:*\n\n";
                                                                $message .= "\n*E-Wallet:* " . $extracted_text_values['ewallet'] . "\n";
                                                                $message .= "\n*TXN:* " . $extracted_text_values['txn'] . "\n";
                                                                $message .= "\n*Amount:* " . $extracted_text_values['amount'] . "\n";    
                                                                
                                                                // Add the fixed extracted text at the bottom
                                                                $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                
                                                                LaravelLog::info("Final message being sent 3: " . $message);

                                                                if(empty($extracted_text_values['ewallet']) && empty($extracted_text_values['txn']) && empty($extracted_text_values['amount'])){
                                                                    $message = sprintf($this->messages[$api->lang]['image_processing_error_retry'], 
                                                                        $deposit->partner_transection_id,
                                                                        $deposit->id
                                                                    );
                                                                    $response = Http::post($url, [
                                                                        'chat_id' => $sender_chat['id'],
                                                                        'text' => $message,
                                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                                        'parse_mode' => 'Markdown',
                                                                    ]);
                                                                }else{
                                                                
                                                                    LaravelLog::info("if if: ".$txnId);
                                                            
                                                                    DB::beginTransaction();
                                                                    $payment = PendingPayment::where('txn_id', $txnId)->where('status', 0)->lockForUpdate()->first();
                                                                    if($payment){
                                                                        LaravelLog::info("if if: ");
                                                                        if($payment){

                                                                            $check_payment_txn = Payment::where('txn_id', $payment->txn_id)->first();
                                                                            if ($check_payment_txn) {
                                                                                DB::rollBack();

                                                                                $message = "By This Txn no, Payment Already Completed.";

                                                                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                    'chat_id' => $TG_message['chat']['id'],
                                                                                    'text' => $message,
                                                                                    'parse_mode' => 'Markdown',
                                                                                    'reply_to_message_id' => $TG_message['message_id']
                                                                                ]);

                                                                                $image_processed=1;
                                                                                return response()->json(['status' => 'success'], 200);
                                                                            }

                                                                            if(isset($phone_number)) {
                                                                                $another_phone_number = $payment->e_wallet_phone_number;
                                                                                $cleaned = str_replace(['-', ' '], '', $phone_number);
                                                                                $matched = "no";
                                                                                if (ctype_digit($cleaned)) {

                                                                                    $data = [
                                                                                        'type' => 'all_digits',
                                                                                        'value' => $cleaned
                                                                                    ];

                                                                                    if ($cleaned === $another_phone_number) {
                                                                                        $matched = "yes";
                                                                                        $e_wallet_phone_number = $cleaned;
                                                                                    } else {
                                                                                        $matched = "no";
                                                                                    }
                                                                                }elseif (preg_match('/\*{2,}|x{2,}|X{2,}/', $cleaned)) {
                                                                                    preg_match('/^(\d+)/', $cleaned, $startMatch);
                                                                                    preg_match('/(\d+)$/', $cleaned, $endMatch);

                                                                                    $startDigits = $startMatch[1] ?? '';
                                                                                    $endDigits   = $endMatch[1] ?? '';

                                                                                    $data =  [
                                                                                        'type' => 'masked',
                                                                                        'start_digits' => $startDigits,
                                                                                        'start_count'  => strlen($startDigits),
                                                                                        'end_digits'   => $endDigits,
                                                                                        'end_count'    => strlen($endDigits),
                                                                                        'original'     => $phone_number
                                                                                    ];

                                                                                    if (
                                                                                        str_starts_with($another_phone_number, $startDigits) &&
                                                                                        str_ends_with($another_phone_number, $endDigits)
                                                                                    ) {
                                                                                        $matched = "yes";
                                                                                        $e_wallet_phone_number = $another_phone_number;
                                                                                        
                                                                                    } else {
                                                                                        $matched = "no";
                                                                                    }
                                                                                }

                                                                                if($matched=="no"){
                                                                                    $message = "⚠️ *E-Wallet Mismatch* ⚠️\n\n";
                                                                                    $message .= "Our E-Wallet: `" . $another_phone_number . "`\n";
                                                                                    $message .= "User E-wallet: `" . $phone_number . "`\n\n";
                                                                                    $message .= " *Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                    $message .= " *Transaction ID:* `".$txnId."`\n";
                                                                                    $message .= " *Amount:* `".(isset($deposit->amount) ? $deposit->amount : "Not found")."`\n";
                                                                                    $message .= " *Status:* `Pending`\n";
                                                                                    $message .= " *Payment Platform:* `".$deposit->gateway->name."`\n";
                                                                                    $message .= "User E-Wellet no. Does not Match with our E-Wallet no.";


                                                                                    $support_chat_id = "-4786890063";
                                                                                    $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                                    $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";

                                                                                    $response = Http::post($url_support, [
                                                                                        'chat_id' => $support_chat_id,
                                                                                        'text' => $message,
                                                                                        'parse_mode' => 'Markdown',
                                                                                    ]);


                                                                                    $message = sprintf($this->messages[$api->lang]['account_not_belong'], 
                                                                                        $deposit->partner_transection_id,
                                                                                        $deposit->id
                                                                                    );
                                                                                    $response = Http::post($url, [
                                                                                        'chat_id' => $sender_chat['id'],
                                                                                        'text' => $message,
                                                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                                                        'parse_mode' => 'Markdown',
                                                                                    ]);

                                                                                    $image_processed=1;
                                                                                    return response()->json(['status' => 'success'], 200);
                                                                                }
                                                                                
                                                                                
                                                                                
                                                                            }


                                                                            // Add amount validation
                                                                            if(isset($amount) && $amount > 0) {
                                                                                $expectedAmount = $deposit->amount;
                                                                                $extractedAmount = (float)$amount;
                                                                                
                                                                                // Check if amounts don't match
                                                                                if(abs($extractedAmount - $expectedAmount) > 0.01) {
                                                                                    // Save the new TRX ID to the deposit/order
                                                                                    $deposit->txn_id = $txnId; // Save the new TRX ID
                                                                                    $deposit->save();

                                                                                    $message = "⚠️ *Amount Mismatch* ⚠️\n\n";
                                                                                    $message .= "Expected Amount: `" . $expectedAmount . "`\n";
                                                                                    $message .= "Image Amount: `" . $extractedAmount . "`\n\n";
                                                                                    $message .= " *Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                    $message .= " *Transaction ID:* `".$txnId."`\n";
                                                                                    $message .= " *Status:* `Pending`\n";
                                                                                    $message .= " *Payment Platform:* `".$deposit->gateway->name."`\n";
                                                                                    $message .= "New TRX number submitted and callback sent with correct amount.";

                                                                                    $support_chat_id = "-4786890063";
                                                                                    $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                                    $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";

                                                                                    $response = Http::post($url_support, [
                                                                                        'chat_id' => $support_chat_id,
                                                                                        'text' => $message,
                                                                                        'parse_mode' => 'Markdown',
                                                                                    ]);

                                                                                    $image_processed=1;
                                                                                }
                                                                            }


                                                                            
                                                                        
                                                                            $partner_api_key = $api_key;
                                                                            $source = $partner_api_key->website;
                                                                            $api_id = $partner_api_key->id;
                                                                        
                                                                        
                                                                            $sum = Payment::whereYear('created_at', now()->year)
                                                                                ->whereMonth('created_at', now()->month)
                                                                                ->where('api_id', $api_id)
                                                                                ->where('status', 'Complete')
                                                                                ->sum('amount');

                                                                            $account = EWalletAccount::where('e_wallet_name', $deposit->gateway->code)
                                                                                ->where('account_no', $e_wallet_phone_number)
                                                                                ->first();
                                                                            if (!$account) {
                                                                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                    'chat_id' => $TG_message['chat']['id'],
                                                                                    'text' => 'E-Wallet Account Issue, Contact With Administrator',
                                                                                    'parse_mode' => 'Markdown',
                                                                                    'reply_to_message_id' => $TG_message['message_id']
                                                                                ]);

                                                                                $image_processed=1;
                                                                                return response()->json(['status' => 'success'], 200);
                                                                            }


                                                                            $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                                                                            if ($commissions) {
                                                                                $charge = $commissions->deposit_percentage * $deposit->amount / 100;
                                                                            } else {
                                                                                $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                                                                                if ($commissions) {
                                                                                    $charge = $commissions->deposit_percentage * $deposit->amount / 100;
                                                                                }
                                                                            }
                                                    
                                                                            $charge = str_replace(',', '', $charge);
                                                                            $charge = (float)$charge;
                                                                            $charge = round($charge, 2);
                                                                            
                                                                            $amount = str_replace(',', '', $amount);
                                                                            $amount = (float)$amount;
                                                                            $amount = round($amount, 2);
                                                                        
                                                                            if($amount>0){
                                                                                $final_amo = getAmount($amount - $charge);
                                                                                    
                                                                                if($amount==$payment->amount){
                                                                                    $order = Payment::where('id', $deposit->id)->with(['gateway', 'user'])->lockForUpdate()->first();
                                                                                    
                                                                                    $message_to_show = "*Transection Completed*";
                                                                                }
                                                                                else
                                                                                {   
                                                                                        $message_to_show = "*Transection of Differant Amount Completed*";
                                                                                        $partner_transection_id = "createdByAdmin_" . time();
                                                                                        
                                                                                        $order = new Payment();
                                                                                        $order->user_id = 0;
                                                                                        $order->gateway_id = $deposit->gateway_id;
                                                                                        $order->amount = $payment->amount;
                                                                                        $order->partner_transection_id = $partner_transection_id;
                                                                                        $order->member_id = $deposit->member_id;
                                                                                        $order->charge = $charge;
                                                                                        $order->sender = $deposit->account_no;
                                                                                        $order->transaction = strRandom();
                                                                                        $order->try = 0;
                                                                                        $order->status = "Pending";
                                                                                        $order->api_id = $api_id;
                                                                                        $order->e_wallet_phone_number = $deposit->e_wallet_phone_number;
                                                                                        $order->request_source = "Telegram";
                                                                                        $order->save();     


                                                                                        $parentIds = ParentCommission::where('user_id', $partner_api_key->id)
                                                                                            ->pluck('parent_id')
                                                                                            ->unique()
                                                                                            ->values();
                                                                                        foreach($parentIds as  $parentId){

                                                                                            $parent_charge = 0;

                                                                                            $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                                                                                            if ($parent_commission) {
                                                                                                $parent_charge = $parent_commission->deposit_percentage * $deposit->amount / 100;
                                                                                            } else {
                                                                                                $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                                                                                                if ($parent_commission) {
                                                                                                    $parent_charge = $parent_commission->deposit_percentage * $deposit->amount / 100;
                                                                                                }
                                                                                            }

                                                                                            if($parent_charge>0){
                                                                                                $PartnerCommission = new PartnerCommission();
                                                                                                $PartnerCommission->api_id = $partner_api_key->id;
                                                                                                $PartnerCommission->from_id = $parentId;
                                                                                                $PartnerCommission->type = 1;
                                                                                                $PartnerCommission->amount = $deposit->amount;
                                                                                                $PartnerCommission->charges = $charge;
                                                                                                $PartnerCommission->total_amount = $deposit->amount - $charge;
                                                                                                $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                                                                                                $profit_p = $parent_commission->deposit_percentage;
                                                                                                $profit = $profit_p * $deposit->amount / 100;
                                                                                                $PartnerCommission->profit = $profit;
                                                                                                $PartnerCommission->profit_p = $profit_p;
                                                                                                $PartnerCommission->transaction_id = $deposit->id;
                                                                                                $PartnerCommission->status = 0;
                                                                                                $PartnerCommission->save();
                                                                                            }




                                                                                        }
                                                                                    
                                                                                    
                                                                                        
                                                                                        
                                                                                        
                                                                                        
                                                                                        
                                                                                }


                                                                                if($order){
                                                                                    $order = Payment::where('id', $deposit->id)->with(['gateway', 'user'])->lockForUpdate()->first();
                                                                                    $commit = 0;
                                                                                    
                                                                                    
                                                                                    
                                                                                    
                                                                                    if ($source != env('APP_WEBSITE')) {
                                                                                        $api_balance_row = Api::where('api_key', $api_id)->where('type', 'Admin')->lockForUpdate()->first();
                                                                                        $net_amount = $payment->amount - $charge;
                                                                                        
                                                                                        if ($api_balance_row) {
                                                                                            $api_balance_row->balance += $net_amount;
                                                                                            $api_balance_row->save();

                                                                                            $Log = new Log();
                                                                                            $Log->date_time = $payment->updated_at;
                                                                                            $Log->final_amount = $net_amount;
                                                                                            $Log->balance = $api_balance_row->balance;
                                                                                            $Log->transection_type = 1;
                                                                                            $Log->transection_id = $order->id;
                                                                                            $Log->partner_id = $api_balance_row->id;
                                                                                            $Log->source = 'TelegramVerify';
                                                                                            $Log->save();
                                                                                        } else {
                                                                                            LaravelLog::error('API balance row not found for api_id: ' . $api_id . '. Transaction ID: ' . $payment->id);
                                                                                            // Continue processing but log the error
                                                                                        }
                                                                                    } else {
                                                                                        $net_amount = $payment->amount - $charge; // Define net_amount for other cases too
                                                                                    }
                                                                
                                                                                    $order->status = 'Complete';
                                                                                    $order->trans_complete_date = Carbon::now();
                                                                                    $order->completed_source = 'Telegram';
                                                                                    $order->charge = $charge;

                                                                                    if(empty($order->sender) || $order->sender==0){
                                                                                        $order->sender = $payment->sender;
                                                                                    }
                                                                                    
                                                                                    $order->txn_id = $payment->txn_id;
                                                                                    $order->date_time = $payment->date_time;
                                                                                    $order->transaction_type = $payment->transaction_type;
                                                                                    $order->ip_address = $payment->ip_address;
                                                                                    $order->e_wallet_type = $payment->e_wallet_type;
                                                                                    $order->mac_address = $payment->mac_address;
                                                                                    $order->fee = $payment->fee;
                                                                                    $order->commission = $payment->commission;
                                                                                    $order->e_wallet_charges = $payment->e_wallet_charges;
                                                                                    $order->payment_received_at = $payment->created_at;



                                                                                    $order->save();

                                                                                    $payment->status = 1;
                                                                                    $payment->save();
                                                                                    $payment=null;
                                                                                    // $payment->delete();
                                                                                    
                                                                
                                                                                    DB::commit();
                                                                                    $commit = 1;
                                                                
                                                                                    $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $api_id)->whereDate('created_at', '>=', $order->created_at)->get();
                                                                                    foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                                                                        $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                                                                                        $amount_to_update = round($amount_to_update, 2);
                                                                                        // $amount_to_update = floor($amount_to_update * 100) / 100;
                                                                                        $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                                                                        $DailyPartnerSummary_record->save();

                                                                                        $summary_log = new DailyPartnerSummaryLog();
                                                                                        if ($partner_api_key) {
                                                                                            $summary_log->partner_id = $partner_api_key->id;
                                                                                            $summary_log->partner_balance = $partner_api_key->balance;
                                                                                        } else {
                                                                                            LaravelLog::error('Partner API key not found for api_id: ' . $api_id);
                                                                                            $summary_log->partner_id = $api_id;
                                                                                            $summary_log->partner_balance = 0;
                                                                                        }
                                                                                        $summary_log->payment_id = $order->id;
                                                                                        $summary_log->total_amount = $net_amount;
                                                                                        $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                                                                        $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                                                                        $summary_log->source = 'Telegram';
                                                                                        $summary_log->save();
                                                                                    }
                                                                
                                                                
                                                                                    
                                                                                    
                                                                
                                                                                    $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                                                                                    foreach ($PartnerCommissions as $PartnerCommission) {
                                                                                        $PartnerCommission->status = 1;
                                                                                        $PartnerCommission->save();
                                                                
                                                                                        DB::beginTransaction();
                                                                                        $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                                                                                        if($parent_api_key){
                                                                                            $parent_api_key->balance += $PartnerCommission->profit;
                                                                                            $parent_api_key->save();
                                                                    
                                                                                            $Log = new Log();
                                                                                            $Log->date_time = $PartnerCommission->created_at;
                                                                                            $Log->final_amount = $PartnerCommission->profit;
                                                                                            $Log->balance = $parent_api_key->balance;
                                                                                            $Log->transection_type = 5;
                                                                                            $Log->transection_id = $PartnerCommission->id;
                                                                                            $Log->partner_id = $PartnerCommission->from_id;
                                                                                            $Log->source = 'Telegram';
                                                                                            $Log->save();
                                                                                            DB::commit();
                                                                    
                                                                                            $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                                                                            foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                                                                                $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($PartnerCommission->profit);
                                                                                                $amount_to_update = round($amount_to_update, 2);
                                                                                                // $amount_to_update = floor($amount_to_update * 100) / 100;
                                                                                                $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                                                                                $DailyPartnerSummary_record->save();
                                                                    
                                                                                                $summary_log = new DailyPartnerSummaryLog();
                                                                                                $summary_log->partner_id = $parent_api_key->id;
                                                                                                $summary_log->partner_balance = $parent_api_key->balance;
                                                                                                $summary_log->payment_id = $PartnerCommission->id;
                                                                                                $summary_log->total_amount = $PartnerCommission->profit;
                                                                                                $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                                                                                $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                                                                                $summary_log->source = 'Telegram';
                                                                                                $summary_log->save();
                                                                                            }
                                                                                        }
                                                                                        
                                                                                    }
                                                                                }
                                                                                
                                                                                if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {
                                                            
                                                                                    $string_to_hash = json_encode(array(
                                                                                        "amount" => strval($this->convertStringToNumber($order->amount)),
                                                                                        "api_key" => $partner_api_key->api_key,
                                                                                        "e_wallet_name" => $order->e_wallet_name,
                                                                                        "id" => strval($order->id),
                                                                                        'transaction_type' => 'Deposit',
                                                                                        "user_account_no" => strval($order->sender),
                                                            
                                                                                    ));
                                                                                    $secretKey = $partner_api_key->secret_key;
                                                                                    $hash = hash("sha256", $string_to_hash);
                                                                                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                                                                                    $timestamp = time();
                                                                                    $combined = $hmac . $timestamp;
                                                                                    $sign = base64_encode($combined);
                                                            
                                                            
                                                                                    $array_data = [
                                                                                                'id' => $order->id,
                                                                                                'partner_transection_id' => $order->partner_transection_id,
                                                                                                'transaction_type' => 'Deposit',
                                                                                                'e_wallet_name' => $order->e_wallet_name,
                                                                                                'amount' => $this->convertStringToNumber($order->amount),
                                                                                                'user_account_no' => $order->sender,
                                                                                                'txn_id' => $order->txn_id,
                                                                                                'e_wallet_phone_number' => $order->e_wallet_phone_number,
                                                                                                'e_wallet_type' => $order->e_wallet_type,
                                                                                                'charges' => $this->convertStringToNumber($order->charge),
                                                                                                'status' => $order->status,
                                                                                                'completion_date' => Carbon::parse($order->date_time)->toDateString(),
                                                                                                'completion_time' => Carbon::parse($order->date_time)->toTimeString(),
                                                                                                'created_at' => $order->created_at,
                                                                                                'updated_at' => $order->updated_at,
                                                                                                'sign' => $sign,
                                                                                    ];
                                                            
                                                                                    if(!empty($order->member_id)){
                                                                                        $array_data['member_id'] = $order->member_id;
                                                                                    }
                                                            
                                                            
                                                                                    $requestData = [
                                                                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                                                                        'request_url' => $partner_api_key->api_endpoint_deposit,
                                                                                        'request_payload' => json_encode($array_data),
                                                                                        'request_headers' => json_encode([
                                                                                            'Content-Type' => 'application/json',
                                                                                            'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                                                                                        ]),
                                                                                        'created_at' => now(),
                                                                                        'updated_at' => now(),
                                                                                    ];
                                                            
                                                                                    $logId = DB::table('api_logs')->insertGetId($requestData);
                                                                                    try {
                                                                                        $csrfToken = Str::random(40);
                                                                                        $response = Http::withHeaders([
                                                                                            'Content-Type' => 'application/json',
                                                                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                                                                        ])
                                                                                            ->post($partner_api_key->api_endpoint_deposit, $array_data);
                                                            
                                                                                        if ($response) {
                                                                                            $responseData = [
                                                                                                'response_code' => $response->status(),
                                                                                                'response_payload' => $response->body(),
                                                                                                'response_headers' => json_encode($response->headers()),
                                                                                            ];
                                                            
                                                                                            DB::table('api_logs')->where('id', $logId)->update($responseData);
                                                                                        }
                                                                                    } catch (\Exception $e) {
                                                                                        //
                                                                                    }
                                                                                }
                                                                                
                                                                                $support_chat_id = "-4786890063";
                                                                                $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                                $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                                $message_support = "";
                                                                                $message_support .= "?? ".$message_to_show." ??\n\n";
                                                                                $message_support .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                $message_support .= "*Order Id:* `".$deposit->id."`\n";
                                                                                $message_support .= "*Transaction ID:* `".$txnId."`\n";
                                                                                $message_support .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                                $message_support .= "*Remark:* Transaction processed and callback sent.\n";
                                                                                $message_support .= "*Status:* `Complete`\n";
                                                                                
                                                                                $response = Http::post($url_support, [
                                                                                    'chat_id' => $support_chat_id,
                                                                                    'text' => $message_support,
                                                                                    'parse_mode' => 'Markdown',
                                                                                ]);
                                                                                
                                                                                
                                                                                    $message = "";
                                                                                    $message .= "Your transaction has been marked as completed, and the callback has also been sent.\n\n";
                                                                                    $message .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                    $message .= "*Order Id:* `".$deposit->id."`\n";
                                                                                    $message .= "*Transaction ID:* `".$txnId."`\n";
                                                                                    $message .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                                    $message .= "*Status:* `Complete`\n";
                                                                                
                                                                                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                        'chat_id' => $TG_message['chat']['id'],
                                                                                        'text' => $message,
                                                                                        'parse_mode' => 'Markdown',
                                                                                        'reply_to_message_id' => $TG_message['message_id']
                                                                                    ]);
                                                                                    
                                                                                    $image_processed=1;
                                                                            }else{
                                                                                $support_chat_id = "-4786890063";
                                                                                $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                                $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                                $message_support = "";
                                                                                $message_support .= "?? *Transaction Not Found* ??\n\n";
                                                                                $message_support .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                $message_support .= "*Order Id:* `".$deposit->id."`\n";
                                                                                $message_support .= "*Transaction ID:* `".$txnId."`\n";
                                                                                $message_support .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                                $message_support .= "*Remark:* Transaction processed and callback sent.\n";
                                                                                $message_support .= "*Status:* `Not Found`\n";
                                                                                
                                                                                $response = Http::post($url_support, [
                                                                                    'chat_id' => $support_chat_id,
                                                                                    'text' => $message_support,
                                                                                    'parse_mode' => 'Markdown',
                                                                                ]);
                                                                                
                                                                                $message = sprintf($this->messages[$api->lang]['transaction_pending'], 
                                                                                    $deposit->partner_transection_id,
                                                                                    $deposit->id,
                                                                                    $txnId,
                                                                                    (isset($amount) ? $amount : "Not found")
                                                                                );
                                                                                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                    'chat_id' => $TG_message['chat']['id'],
                                                                                    'text' => $message,
                                                                                    'parse_mode' => 'Markdown',
                                                                                    'reply_to_message_id' => $TG_message['message_id']
                                                                                ]);
                                                                                
                                                                                $image_processed=1;
                                                                            }
                                                                            
                                                                            
                                                                                
                                                                        }
                                                                        
                                                                    }else{
                                                                        LaravelLog::info("else else: ");
                                                                        $support_chat_id = "-4786890063";
                                                                        $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                        $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                        $message_support = "";
                                                                        $message_support .= "?? *Transaction Not Found* ??\n\n";
                                                                        $message_support .= " *Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                        $message_support .= " *Transaction ID:* `".$txnId."`\n";
                                                                        $message_support .= " *Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                        $message_support .= " *Remark:* Transaction ID not found in system.\n";
                                                                        $message_support .= " *Status:* `Pending`\n";
                                                                        $message_support .= " *Payment Platform:* `".$deposit->gateway->name."`\n";
                                                                        
                                                                        $response = Http::post($url_support, [
                                                                            'chat_id' => $support_chat_id,
                                                                            'text' => $message_support,
                                                                            'parse_mode' => 'Markdown',
                                                                        ]);
                                                                        
                                                                        $message = sprintf($this->messages[$api->lang]['transaction_pending'], 
                                                                            $deposit->partner_transection_id,
                                                                            $deposit->id,
                                                                            $txnId,
                                                                            (isset($amount) ? $amount : "Not found")
                                                                        );
                                                                        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                            'chat_id' => $TG_message['chat']['id'],
                                                                            'text' => $message,
                                                                            'parse_mode' => 'Markdown',
                                                                            'reply_to_message_id' => $TG_message['message_id']
                                                                        ]);
                                                                        
                                                                        $image_processed=1;
                                                                    }
                                                                    
                                                                    if($commit==0){
                                                                        DB::commit();
                                                                    }
                                                                    
                                                                    
                                                                    
                                                                    
                                                            
                                                                }
                                                                
                                                                
                                                                LaravelLog::info("re else else: ".$txnId);
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = sprintf($this->messages[$api->lang]['image_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                        } else {
                                                            LaravelLog::error("OCR.space API Error: " . $result);
                                                            $message = sprintf($this->messages[$api->lang]['image_error'], 
                                                                $deposit->partner_transection_id,
                                                                $deposit->id
                                                            );
                                                            $response = Http::post($url, [
                                                                'chat_id' => $sender_chat['id'],
                                                                'text' => $message,
                                                                'reply_to_message_id' => $TG_message['message_id'],
                                                                'parse_mode' => 'Markdown',
                                                            ]);
                                                            
                                                            $image_processed=1;
                                                        }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                        }
                                        
                                        if($image_processed==0){
                                            $message = sprintf($this->messages[$api->lang]['service_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                        }
                                        
                                    }else{
                                        $message = sprintf($this->messages[$api->lang]['service_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                        $response = Http::post($url, [
                                            'chat_id' => $sender_chat['id'],
                                            'text' => $message,
                                            'reply_to_message_id' => $TG_message['message_id'],
                                            'parse_mode' => 'Markdown',
                                        ]);
                                    }
                                }

                                    
                                    
                                    

                            }else{
                                $withdrawal = Payout::where('partner_transection_id',$orderNumber)->where('api_id',$api->api_id)->latest()->first();
                                if($withdrawal){
                                    if($withdrawal->status=="Complete"){
                                        $message = $this->messages[$api->lang]['transaction_completed_callback'];
                                    }elseif($withdrawal->status=="Reject"){
                                        // Fetch remarks from the payout log
                                        $reason = $withdrawal->feedback;
                                        $message = sprintf($this->messages[$api->lang]['transaction_rejected_with_reason'], $reason);
                                    }else{
                                        $message = $this->messages[$api->lang]['transaction_pending_callback'];
                                        
                                        //
                                        // Add code that send message to support
                                        //
                                        $support_chat_id = "-4786890063";
                                        $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                        $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                        $message_support = "";

                                        if ($withdrawal->status == "Pending") {
                                            $message_support .= "💤 *Withdrawal Pending* 💤\n\n";
                                            $message_support .= "*Merchant Order:* `" . $withdrawal->partner_transection_id . "`\n";
                                            $message_support .= "*Order Id:* `" . $withdrawal->id . "`\n";
                                            $message_support .= "*Amount:* `" . $withdrawal->amount . "`\n";
                                            $message_support .= "*Phone:* `" . $withdrawal->user_account_no . "`\n";
                                            $message_support .= "*Remark:* Withdrawal request is pending for processing.\n";
                                            $message_support .= "*Status:* `Pending`\n";
                                            $message_support .= "*Payment Platform:* `" . $withdrawal->e_wallet_name . "`\n";
                                        }                                            
                                        
                                        
                                        $response = Http::post($url_support, [
                                            'chat_id' => $support_chat_id,
                                            'text' => $message_support,
                                            'parse_mode' => 'Markdown',
                                        ]);
                                    }

                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
        
                                    if($withdrawal->status=="Complete" || $withdrawal->status=="Reject"){
                                        

                                        if (!empty($api_key->api_endpoint_withdrawal) && $api_key->website != env('APP_WEBSITE')) {

                                            $string_to_hash = json_encode(array(
                                                "amount" => strval($this->convertStringToNumber($withdrawal->amount)),
                                                "api_key" => $api_key->api_key,
                                                "e_wallet_name" => $withdrawal->e_wallet_name,
                                                "id" => strval($withdrawal->id),
                                                'transaction_type' => 'Withdrawal',
                                                "user_account_no" => strval($withdrawal->user_account_no),
                                            ));
                                            $secretKey = $api_key->secret_key;
                                            $hash = hash("sha256", $string_to_hash);
                                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                                            $timestamp = time();
                                            $combined = $hmac . $timestamp;
                                            $sign = base64_encode($combined);

                                            $array_data = [
                                                        'id' => $withdrawal->id,
                                                        'partner_transection_id' => $withdrawal->partner_transection_id,
                                                        'transaction_type' => 'Withdrawal',
                                                        'e_wallet_name' => $withdrawal->e_wallet_name,
                                                        'amount' => $this->convertStringToNumber($withdrawal->amount),
                                                        'user_account_no' => $withdrawal->user_account_no,
                                                        'txn_id' => $withdrawal->txn_id,
                                                        'e_wallet_phone_number' => $withdrawal->e_wallet_phone_number,
                                                        'e_wallet_type' => $withdrawal->e_wallet_type,
                                                        'charges' => $this->convertStringToNumber($withdrawal->charge),
                                                        'status' => $withdrawal->status,
                                                        'completion_date' => Carbon::parse($withdrawal->date_time)->toDateString(),
                                                        'completion_time' => Carbon::parse($withdrawal->date_time)->toTimeString(),
                                                        'created_at' => $withdrawal->created_at,
                                                        'updated_at' => $withdrawal->updated_at,
                                                        'sign' => $sign,
                                                        'remarks' => $withdrawal->feedback,
                                                        
                                            ];

                                            if(!empty($withdrawal->member_id)){
                                                $array_data['member_id'] = $withdrawal->member_id;
                                            }


                                            $requestData = [
                                                'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                                'request_url' => $api_key->api_endpoint_withdrawal,
                                                'request_payload' => json_encode($array_data),
                                                'request_headers' => json_encode([
                                                    'Content-Type' => 'application/json',
                                                    'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                                ]),
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ];

                                            $logId = DB::table('api_logs')->insertGetId($requestData);

                                            $csrfToken = csrf_token();
                                            $responseData = [];
                                            try {

                                                $response = Http::withHeaders([
                                                    'Content-Type' => 'application/json',
                                                    'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                                ])
                                                    ->post($api_key->api_endpoint_withdrawal, $array_data);

                                                $responseData = [
                                                    'response_code' => $response->status(),
                                                    'response_payload' => $response->body(),
                                                    'response_headers' => json_encode($response->headers()),
                                                ];

                                                DB::table('api_logs')->where('id', $logId)->update($responseData);
                                                
                                            } catch (\Exception $e) {
                                                LaravelLog::info('Telegram Withdrawal Callback not sent');
                                            }
                                        }
                                    }
                                }else{
                                    $message = sprintf($this->messages[$api->lang]['transaction_not_found'], 
                                        $sender_message,
                                        $api->api_id
                                    );
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                }
                                    
                            }
                        }else{
                            $message = sprintf($this->messages[$api->lang]['invalid_command'], 
                                $sender_message,
                                $api->api_id
                            );
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }    
                    
                        
                    }elseif(strpos($lowercaseText, "/test") === 0){
                        
                        
                        
                        
                        $parts = explode(" ", $sender_message);
                        $extractedText = '';
                        
                            $gateway_name = "";
                        if(count($parts) >= 2) {
                            $gateway_name = trim($parts[1]);
                        }
                        
                        
                        
                        
                        
                        
                        if (isset($TG_message['photo'])) {
                                        $image_processed = 0;
                                        
                                        try {
                                            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                            $photo = end($TG_message['photo']);
                                            $file_id = $photo['file_id'];
                                            LaravelLog::info("Got file_id: $file_id");
                                    
                                            // Get file info from Telegram
                                            $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                            LaravelLog::info("Requesting file info from: $getFileUrl");
                                            $fileData = Http::get($getFileUrl)->json();
                                            LaravelLog::info("File info response: " . json_encode($fileData));
                                            
                                            
                                    
                                            if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                                $file_path = $fileData['result']['file_path'];
                                                $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                                LaravelLog::info("Downloading image from: $fileUrl");
                                    
                                                // Use cURL to fetch the image data because allow_url_fopen is disabled
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                $imageContent = curl_exec($ch);
                                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);
                                                
                                                
                                                
                                    
                                                if ($imageContent && $httpCode === 200) {
                                                    $tempPath = 'ocr_' . time() . '.jpg';
                                                    $tempImagePath = storage_path('app/public/ocr_images/' . $tempPath);
                                                    file_put_contents($tempImagePath, $imageContent);
                                                    $imageUrl = url('storage/app/public/ocr_images/' . $tempPath);
                                                    LaravelLog::info("Image saved temporarily at: $tempImagePath");
                                                    LaravelLog::info("Image saved temporarily at: $imageUrl");
                                    
                                                    try {
                                                        $apiKey = env('OCR_SPACE_API_KEY', 'K83793710188957');  // Update this with your new API key
                                                        
                                                        // Process the image before sending to OCR
                                                        try {
                                                            // Try to improve image quality before OCR processing
                                                            if (extension_loaded('imagick')) {
                                                                LaravelLog::info("Using Imagick for image preprocessing");
                                                                $imagick = new \Imagick($tempImagePath);
                                                                
                                                                // Enhanced image preprocessing
                                                                $imagick->setImageFormat('png'); // Convert to PNG for better quality
                                                                $imagick->contrastImage(1);
                                                                $imagick->sharpenImage(0, 1.0);
                                                                $imagick->normalizeImage();
                                                                $imagick->despeckleImage(); // Remove small dots
                                                                $imagick->enhanceImage(); // Enhance local contrast
                                                                
                                                                // Increase resolution if too low
                                                                $resolution = $imagick->getImageResolution();
                                                                if ($resolution['x'] < 300 || $resolution['y'] < 300) {
                                                                    $imagick->setImageResolution(300, 300);
                                                                    $imagick->resampleImage(300, 300, \Imagick::FILTER_LANCZOS, 1);
                                                                }
                                                                
                                                                // Convert to grayscale for better OCR
                                                                $imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
                                                                
                                                                // Save the enhanced image
                                                                $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                                $imagick->writeImage($enhancedImagePath);
                                                                
                                                                // Use the enhanced image if it exists
                                                                if (file_exists($enhancedImagePath)) {
                                                                    $tempImagePath = $enhancedImagePath;
                                                                    LaravelLog::info("Using enhanced image: $enhancedImagePath");
                                                                }
                                                            } else {
                                                                LaravelLog::info("Imagick not available, using GD for basic enhancement");
                                                                $image = imagecreatefromstring(file_get_contents($tempImagePath));
                                                                if ($image !== false) {
                                                                    // Apply basic enhancements
                                                                    imagefilter($image, IMG_FILTER_CONTRAST, -10);
                                                                    imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);
                                                                    imagefilter($image, IMG_FILTER_GRAYSCALE);
                                                                    
                                                                    $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                                    imagepng($image, $enhancedImagePath, 9); // High quality PNG
                                                                    imagedestroy($image);
                                                                    
                                                                    if (file_exists($enhancedImagePath)) {
                                                                        $tempImagePath = $enhancedImagePath;
                                                                        LaravelLog::info("Using GD enhanced image: $enhancedImagePath");
                                                                    }
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            LaravelLog::error("Image enhancement failed: " . $e->getMessage());
                                                            // Continue with original image
                                                        }
                                                        
                                                        
                                                        
                                                        
                                                        // First try with OCR Engine 2 (better for receipts and complex text)
                                                        $ch = curl_init();
                                                        curl_setopt_array($ch, [
                                                            CURLOPT_URL => 'https://api.ocr.space/parse/image',
                                                            CURLOPT_RETURNTRANSFER => true,
                                                            CURLOPT_HTTPHEADER => ['apikey: ' . $apiKey],
                                                            CURLOPT_POST => true,
                                                            CURLOPT_POSTFIELDS => [
                                                                'file' => new \CURLFile($tempImagePath),
                                                                'language' => 'eng',  // Changed from eng+ben to just eng as it was causing issues
                                                                'isOverlayRequired' => 'false',
                                                                'detectOrientation' => 'true',
                                                                'scale' => 'true',
                                                                'OCREngine' => '2',
                                                                'isTable' => 'true',
                                                                'filetype' => 'Auto',
                                                                'detectCheckbox' => 'false',
                                                                'isCreateSearchablePdf' => 'false',
                                                                'isSearchablePdfHideTextLayer' => 'false'
                                                            ],
                                                        ]);
                                                        
                                                        // Add detailed logging
                                                        LaravelLog::info('Sending request to OCR.space API...');
                                                        $result = curl_exec($ch);
                                                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                        $error = curl_error($ch);
                                                        curl_close($ch);
                                                        
                                                        if ($error) {
                                                            LaravelLog::error("CURL Error: " . $error);
                                                            throw new \Exception("OCR API request failed: " . $error);
                                                        }
                                                        
                                                        // Log the raw OCR response for debugging
                                                        LaravelLog::info("OCR API Raw Response: " . $result);
                                                        
                                                        
                                                        
                                                        
                                                        if ($httpCode === 200) {
                                                            $ocrResult = json_decode($result, true);
                                                            LaravelLog::info("OCR.space API Response: " . json_encode($ocrResult));
                                                            
                                                            if (isset($ocrResult['ParsedResults'][0]['ParsedText'])) {
                                                                $extractedText = $ocrResult['ParsedResults'][0]['ParsedText'];
                                                                LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Initialize the gateway_name variable with a default value
                                                                $gateway_name = $deposit->gateway->name ?? '';
                                                                
                                                                if(strtolower($gateway_name)=="bkash"){
                                                                    // Enhanced bKash transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // CDR/CD/CDP patterns with more flexible length
                                                                        '/\b(?:CDR|CD|CDP)[0-9A-Z]{6,12}\b/i',
                                                                        // Generic bKash transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found bKash Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns
                                                                    if (preg_match('/(?:Account|Number)\s*:?\s*(01\d{9})/', $extractedText, $matches) ||
                                                                        preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number: " . $phone_number_by_telegram_message);
                                                                    }
                                                                } elseif(strtolower($gateway_name)=="nagad"){
                                                                    LaravelLog::info("Processing Nagad payment with text: " . $extractedText);
                                                                    
                                                                    // Enhanced Nagad transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // 73 prefix pattern with flexible length
                                                                        '/\b(?:73|NAGAD)[0-9A-Z]{5,10}\b/i',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                        // Generic Nagad transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                        // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{5,10})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Nagad Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns - handle both formats
                                                                    if (preg_match('/(\d{5})-(\d{6})/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                        LaravelLog::info("Found phone number with dash format: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/\b(01\d{3})(\d{6})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                        LaravelLog::info("Found phone number without dash: " . $phone_number_by_telegram_message);
                                                                    }
                                                                }
                                                                elseif(strtolower($gateway_name)=="rocket"){
                                                                    LaravelLog::info("Processing Rocket payment with text: " . $extractedText);
                                                                    
                                                                    // Enhanced Rocket transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // 5359/5358 prefix pattern
                                                                        '/\b(?:5359|5358)[0-9]{6,8}\b/',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9]{10,12})\b/i',
                                                                        // Generic Rocket transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9]{10,12})\b/i',
                                                                        // Fallback pattern for any numeric sequence that looks like a transaction ID
                                                                        '/\b(?:[0-9]{10,12})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Rocket Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns for Rocket
                                                                    if (preg_match('/Cash-out\s*\((\d{11})\)/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number from Cash-out: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/(?:Agent|Rocket)\s*A\/C\s*No\.?\s*:?\s*(\d{11})/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number from A/C No: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number generic format: " . $phone_number_by_telegram_message);
                                                                    }
                                                                }
                                                                
                                                                // If no specific provider pattern matched, try generic patterns as fallback
                                                                if (!isset($txnId) || empty($txnId)) {
                                                                    $genericPatterns = [
                                                                        // Generic transaction ID patterns
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Look for any sequence that might be a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/',
                                                                        '/\b(?:[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($genericPatterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Transaction ID using generic pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }
                                                                }

                                                                // Validate the found transaction ID
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    // Remove any non-alphanumeric characters
                                                                    $txnId = preg_replace('/[^A-Z0-9]/i', '', $txnId);
                                                                    
                                                                    // Log the final transaction ID
                                                                    LaravelLog::info("Final Transaction ID after validation: " . $txnId);
                                                                }

                                                                // Generic amount pattern as fallback
                                                                if (!isset($amount) || empty($amount)) {
                                                                    if (preg_match('/\b(\d{2,4}(?:\.\d{2})?)\s*(?:Tk|BDT)?\b/', $extractedText, $matches)) {
                                                                        $amount = $matches[1];
                                                                    }
                                                                }

                                                                // Generic phone number pattern as fallback
                                                                if (!isset($phone_number_by_telegram_message) || empty($phone_number_by_telegram_message)) {
                                                                    if (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                    }
                                                                }
                                                                
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Format the message with extracted information
                                                                $message = "?? *Extracted Information:*\n\n";
                                                                
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                    LaravelLog::info("Adding transaction ID to message: " . $txnId);
                                                                }
                                                                
                                                                if (isset($amount) && !empty($amount)) {
                                                                    $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                    LaravelLog::info("Adding amount to message: " . $amount);
                                                                }
                                                                
                                                                if (isset($phone_number_by_telegram_message) && !empty($phone_number_by_telegram_message)) {
                                                                    $message .= "?? *Phone:* `" . $phone_number_by_telegram_message . "`\n";
                                                                    LaravelLog::info("Adding phone to message: " . $phone_number_by_telegram_message);
                                                                }
                                                                
                                                                // Add the full extracted text at the bottom
                                                                $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                
                                                                
                                                                LaravelLog::info("Final message being sent 4: " . $message);
                                                                
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = "No text found in the image";
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                        } else {
                                                            LaravelLog::error("OCR.space API Error: " . $result);
                                                            $message = "OCR.space API Error";
                                                            $response = Http::post($url, [
                                                                'chat_id' => $sender_chat['id'],
                                                                'text' => $message,
                                                                'reply_to_message_id' => $TG_message['message_id'],
                                                                'parse_mode' => 'Markdown',
                                                            ]);
                                                            
                                                            $image_processed=1;
                                                        }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                            
                                            $message = 'catch';
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                            
                                            return response()->json(['status' => 'success'], 200);
                                        }
                                        
                                        if($image_processed==0){
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => 'Error',
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                        }
                                        
                            }else{
                                $message = 'Attach Image';
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                    
                                    return response()->json(['status' => 'success'], 200);
                            }    
                    
                        
                    }
                    elseif(strpos($lowercaseText, "/newocr") === 0){
                        
                        
                        
                        
                        $parts = explode(" ", $sender_message);
                        $extractedText = '';
                        
                            $gateway_name = "";
                        if(count($parts) >= 2) {
                            $gateway_name = trim($parts[1]);
                        }
                        
                        
                        
                        
                        
                        
                        if (isset($TG_message['photo'])) {
                                        $image_processed = 0;
                                        
                                        try {
                                            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                            $photo = end($TG_message['photo']);
                                            $file_id = $photo['file_id'];
                                            LaravelLog::info("Got file_id: $file_id");
                                    
                                            // Get file info from Telegram
                                            $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                            LaravelLog::info("Requesting file info from: $getFileUrl");
                                            $fileData = Http::get($getFileUrl)->json();
                                            LaravelLog::info("File info response: " . json_encode($fileData));
                                            
                                            
                                    
                                            if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                                $file_path = $fileData['result']['file_path'];
                                                $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                                LaravelLog::info("Downloading image from: $fileUrl");
                                    
                                                // Use cURL to fetch the image data because allow_url_fopen is disabled
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                $imageContent = curl_exec($ch);
                                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);
                                                
                                                
                                                
                                    
                                                if ($imageContent && $httpCode === 200) {
                                                    
                                                    
                                                    
                                                    $tempPath = 'ocr_' . time() . '.jpg';
                                                    $tempImagePath = storage_path('app/public/ocr_images/' . $tempPath);
                                                    file_put_contents($tempImagePath, $imageContent);
                                                    $imageUrl = url('storage/app/public/ocr_images/' . $tempPath);
                                                    LaravelLog::info("Image saved temporarily at: $tempImagePath");
                                                    LaravelLog::info("Image saved temporarily at: $imageUrl");
                                                    
                                                    
                                                    
                                                    $ocrtext = "";

                                                            $response = Http::withHeaders([
                                                                'Content-Type' => 'application/json',
                                                            ])->post('http://89.46.62.251/ocr/api/applyocr', [
                                                                'imageurl' => $imageUrl,
                                                            ]);
                                                            
                                                            
                                                            
                                                            LaravelLog::info("OCR API Raw Response: " . $response);
                                                            
                                                            
                                                            
                                                            if ($response->successful()) {
                                                                $ocr_response = $response->json();
                                                                
                                                                if(isset($ocr_response['ocr_text'])){
                                                                $ocrtext = $ocr_response['ocr_text'];  
                                                                }

                                                            } else {
                                                                
                                                                $message = 'Unexpected error occurred.';
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                            }
                                                            
                                                            LaravelLog::info("OCR API Raw Response: " . $ocrtext);

                                    
                                                    try {
                                                            
                                            
                                                        
                                                        
                                                        $extractedText = $ocrtext;
                                                            if (isset($extractedText)) {
                                                                
                                                                LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                                
                                                                
                                                                // Initialize the gateway_name variable with a default value
                                                                $gateway_name = $deposit->gateway->name ?? '';
                                                                
                                                                $extracted_text_values = $this->extractTransactionDetails($extractedText);
                                                                
                                                                $message = "";
                                                                $message .= "\n*E-Wallet:* " . $extracted_text_values['ewallet'] . "\n";
                                                                $message .= "\n*TXN:* " . $extracted_text_values['txn'] . "\n";
                                                                $message .= "\n*Amount:* " . $extracted_text_values['amount'] . "\n";
                                                                
                                                                
                                                                $message .= "\n-----------------------\n";
                                                                
                                                                
                                                                
                                                                // Add the fixed extracted text at the bottom
                                                                $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                
                                                                //  $message = preg_replace('/[\x00-\x1F\x7F]/u', '', $message);
                                                                $message = mb_convert_encoding($message, 'UTF-8', 'UTF-8');
                                                                
                                                                LaravelLog::info("Final message being sent 7: " . $message);
                                                                
                                                               
                                                                
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = "No text found in the image";
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        // unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                            
                                            $message = 'catch';
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                            
                                            return response()->json(['status' => 'success'], 200);
                                        }
                                        
                                        // if($image_processed==0){
                                        //     $response = Http::post($url, [
                                        //         'chat_id' => $sender_chat['id'],
                                        //         'text' => 'naveed error',
                                        //         'reply_to_message_id' => $TG_message['message_id'],
                                        //         'parse_mode' => 'Markdown',
                                        //     ]);
                                        // }
                                        
                            }else{
                                $message = 'Attach Image';
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                    
                                    return response()->json(['status' => 'success'], 200);
                            }    
                    
                        
                    }
                    
                    
                    
                    elseif(strpos($lowercaseText, "/callback") === 0){
                        $deposit = Payment::where('partner_transection_id',$sender_message)->where('api_id',$api->api_id)->with('gateway')->latest()->first();
                        if($deposit){
                            if($deposit->status==1){
                                $message = $this->messages[$api->lang]['transaction_completed_callback'];
                            }elseif($deposit->status==3){
                                $message = $this->messages[$api->lang]['transaction_rejected_callback'];
                            }else{
                                $message = $this->messages[$api->lang]['transaction_pending_callback'];
                            }

                            // 'message_id' => $telegaram_message_p->response_id,

                            $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);

                                
                                if ($api_key && !empty($api_key->api_endpoint_deposit) && $api_key->website != env('APP_WEBSITE')) {

                                    $payment = $deposit;
                                    if($payment){
                                        $string_to_hash = json_encode(array(
                                            "amount" => strval($this->convertStringToNumber($payment->amount)),
                                            "api_key" => $api_key->api_key,
                                            "e_wallet_name" => $payment->e_wallet_name,
                                            "id" => strval($payment->id),
                                            'transaction_type' => 'Deposit',
                                            "user_account_no" => strval($payment->sender),

                                        ));
                                        $secretKey = $api_key->secret_key;
                                        $hash = hash("sha256", $string_to_hash);
                                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                                        $timestamp = time();
                                        $combined = $hmac . $timestamp;
                                        $sign = base64_encode($combined);


                                        $array_data = [
                                                    'id' => $payment->id,
                                                    'partner_transection_id' => $payment->partner_transection_id,
                                                    'transaction_type' => 'Deposit',
                                                    'e_wallet_name' => $payment->e_wallet_name,
                                                    'amount' => $this->convertStringToNumber($payment->amount),
                                                    'user_account_no' => $payment->sender,
                                                    'txn_id' => $payment->txn_id,
                                                    'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                                                    'e_wallet_type' => $payment->e_wallet_type,
                                                    'charges' => $this->convertStringToNumber($payment->charge),
                                                    'status' => $payment->status,
                                                    'completion_date' => Carbon::parse($payment->date_time)->toDateString(),
                                                    'completion_time' => Carbon::parse($payment->date_time)->toTimeString(),
                                                    'created_at' => $payment->created_at,
                                                    'updated_at' => $payment->updated_at,
                                                    'sign' => $sign,
                                        ];

                                        if(!empty($payment->member_id)){
                                            $array_data['member_id'] = $payment->member_id;
                                        }
                                    }
                                    

                                    $requestData = [
                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                        'request_url' => $api_key->api_endpoint_deposit,
                                        'request_payload' => json_encode($array_data),
                                        'request_headers' => json_encode([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                        ]),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];

                                    $logId = DB::table('api_logs')->insertGetId($requestData);

                                    $csrfToken = csrf_token();
                                    $responseData = [];
                                    try {
                                        $response = Http::withHeaders([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                        ])
                                            ->post($api_key->api_endpoint_deposit, $array_data);
                                        $responseData = [
                                            'response_code' => $response->status(),
                                            'response_payload' => $response->body(),
                                            'response_headers' => json_encode($response->headers()),
                                        ];

                                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                                        
                                    } catch (\Exception $e) {
                                        LaravelLog::info('Telegram Deposit Callback not sent');
                                    }
                                }

                        }else{
                            $withdrawal = Payout::where('partner_transection_id',$sender_message)->where('api_id',$api->api_id)->latest()->first();
                            if($withdrawal){
                                if($withdrawal->status=="Complete"){
                                    $message = $this->messages[$api->lang]['transaction_completed_callback'];
                                }elseif($withdrawal->status=="Reject"){
                                    $message = $this->messages[$api->lang]['transaction_rejected_callback'];
                                }else{
                                    $message = $this->messages[$api->lang]['transaction_pending_callback'];
                                }

                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);

                                
                                $payout_log = $withdrawal;

                                if (!empty($api_key->api_endpoint_withdrawal) && $api_key->website != env('APP_WEBSITE')) {

                                    $string_to_hash = json_encode(array(
                                        "amount" => strval($this->convertStringToNumber($withdrawal->amount)),
                                        "api_key" => $api_key->api_key,
                                        "e_wallet_name" => $withdrawal->e_wallet_name,
                                        "id" => strval($withdrawal->id),
                                        'transaction_type' => 'Withdrawal',
                                        "user_account_no" => strval($withdrawal->user_account_no),
                                    ));
                                    $secretKey = $api_key->secret_key;
                                    $hash = hash("sha256", $string_to_hash);
                                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                                    $timestamp = time();
                                    $combined = $hmac . $timestamp;
                                    $sign = base64_encode($combined);

                                    $array_data = [
                                                'id' => $withdrawal->id,
                                                'partner_transection_id' => $withdrawal->partner_transection_id,
                                                'transaction_type' => 'Withdrawal',
                                                'e_wallet_name' => $withdrawal->e_wallet_name,
                                                'amount' => $this->convertStringToNumber($withdrawal->amount),
                                                'user_account_no' => $withdrawal->user_account_no,
                                                'txn_id' => $withdrawal->txn_id,
                                                'e_wallet_phone_number' => $withdrawal->e_wallet_phone_number,
                                                'e_wallet_type' => $withdrawal->e_wallet_type,
                                                'charges' => $this->convertStringToNumber($withdrawal->charge),
                                                'status' => $withdrawal->status,
                                                'completion_date' => Carbon::parse($withdrawal->date_time)->toDateString(),
                                                'completion_time' => Carbon::parse($withdrawal->date_time)->toTimeString(),
                                                'created_at' => $withdrawal->created_at,
                                                'updated_at' => $withdrawal->updated_at,
                                                'sign' => $sign,
                                                'remarks' => $payout_log->feedback,
                                                
                                    ];

                                    if(!empty($withdrawal->member_id)){
                                        $array_data['member_id'] = $withdrawal->member_id;
                                    }


                                    $requestData = [
                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                        'request_url' => $api_key->api_endpoint_withdrawal,
                                        'request_payload' => json_encode($array_data),
                                        'request_headers' => json_encode([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                        ]),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];

                                    $logId = DB::table('api_logs')->insertGetId($requestData);

                                    $csrfToken = csrf_token();
                                    $responseData = [];
                                    try {

                                        $response = Http::withHeaders([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                        ])
                                            ->post($api_key->api_endpoint_withdrawal, $array_data);

                                        $responseData = [
                                            'response_code' => $response->status(),
                                            'response_payload' => $response->body(),
                                            'response_headers' => json_encode($response->headers()),
                                        ];

                                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                                        
                                    } catch (\Exception $e) {
                                        LaravelLog::info('Telegram Withdrawal Callback not sent');
                                    }
                                }

                            }else{
                                $message = sprintf($this->messages[$api->lang]['transaction_not_found'], 
                                    $sender_message,
                                    $api->api_id
                                );
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }
                                
                        }
                    }else{
                        $message = sprintf($this->messages[$api->lang]['invalid_command'], 
                            $sender_message,
                            $api->api_id
                        );
                        $response = Http::post($url, [
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                    
                        

                }
            }
            
            LaravelLog::info('Telegram Message'.$data);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            LaravelLog::info('Telegram failed');
            return response()->json(['status' => 'success'], 200);
        } 

    }



    public function telegramwebhook_old(Request $request){
    LaravelLog::info('Telegram function loaded');

    try {
        $data = file_get_contents('php://input');
        $array = json_decode($data, true);
        
        // Add message deduplication check
        if(isset($array['message']['message_id'])) {
            $messageId = $array['message']['message_id'];
            $cacheKey = 'telegram_message_' . $messageId;
            
            // Check if we've already processed this message
            if (Cache::has($cacheKey)) {
                LaravelLog::info("Duplicate message received: " . $messageId);
                return response()->json(['status' => 'success'], 200);
            }
            
            // Mark message as processed
            Cache::put($cacheKey, true, now()->addHours(24));
        }
        
        if(isset($array['message'])){
            $TG_message = $array['message'];
        }elseif(isset($array['edited_message'])){
            $TG_message = $array['edited_message'];
        }

        if(isset($TG_message)){
            $sender_chat = $TG_message['chat'];
            $api = TelegramGroup::where('group_username',$sender_chat)->first();
            if(!$api){
                $title = "";
                if(isset($sender_chat['title'])){
                    $title = $sender_chat['title'];
                }elseif(isset($sender_chat['first_name']) && isset($sender_chat['last_name'])){
                    $title = $sender_chat['first_name'] . " " . $sender_chat['last_name'];
                }
                $api = new TelegramGroup;
                $api->group_name = $title;
                $api->group_username = $sender_chat['id'];
                $api->status = 0;
                $api->save();
            }

            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // Add timeout and retry logic for Telegram API calls
            $maxRetries = 3;
            $timeout = 10; // seconds
            $retryDelay = 1; // seconds

            $sendMessage = function($params) use ($url, $maxRetries, $timeout, $retryDelay) {
                $attempt = 0;
                while ($attempt < $maxRetries) {
                    try {
                        $response = Http::timeout($timeout)->post($url, $params);
                        if ($response->successful()) {
                            return $response;
                        }
                        LaravelLog::warning("Telegram API call failed (attempt " . ($attempt + 1) . "): " . $response->body());
                    } catch (\Exception $e) {
                        LaravelLog::error("Telegram API exception (attempt " . ($attempt + 1) . "): " . $e->getMessage());
                    }
                    $attempt++;
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                    }
                }
                throw new \Exception("Failed to send message after $maxRetries attempts");
            };

            // Use the new sendMessage function for all Telegram API calls
            if(empty($api->api_id) || $api->api_id==0 || $api->status==0){
                $message = $this->messages[$api->lang]['request_pending'];
                try {
                    $sendMessage([
                        'chat_id' => $sender_chat['id'],
                        'text' => $message,
                        'reply_to_message_id' => $TG_message['message_id'],
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Exception $e) {
                    LaravelLog::error("Failed to send pending message: " . $e->getMessage());
                }
            } else {
                if(isset($TG_message['text'])){
                    $sender_message = $TG_message['text'];
                }elseif(isset($TG_message['caption'])){
                    $sender_message = $TG_message['caption'];
                }

                $api_key = Api::where('id', $api->api_id)->first();
                $lowercaseText = strtolower($sender_message);
                if($lowercaseText=="checkbalance" || $lowercaseText=="/checkbalance"){
                    if (!$api_key) {
                        $message = $this->messages[$api->lang]['request_pending'];
                    } else {
                        $message = sprintf($this->messages[$api->lang]['checkbalance'], $api_key->name, $api_key->balance);
                    }
                    try {
                        $sendMessage([
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    } catch (\Exception $e) {
                        LaravelLog::error("Failed to send balance message: " . $e->getMessage());
                    }
                }elseif(strpos($lowercaseText, "/lang") === 0){
                    $parts = explode(" ", $sender_message);
                    if(count($parts) >= 2) {
                        $language = trim($parts[1]);
                        if($language=="en" || $language=="ch"){
                            $api->lang = $language;
                            $api->save();
                            
                            $message = $this->messages[$language]['lang_selected'];
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }else{
                            $message = $this->messages[$api->lang]['lang_invalid'];
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }
                    }else{
                        $message = $this->messages[$api->lang]['lang_invalid'];
                        $response = Http::post($url, [
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                }
                elseif(strpos($lowercaseText, "/checkorder") === 0){
                    
                    $parts = explode(" ", $sender_message);
                    $extractedText = '';
                    
                    if(count($parts) >= 2) {
                        $orderNumber = trim($parts[1]);
                    
                    
                        $deposit = Payment::where('partner_transection_id',$orderNumber)->where('api_id',$api->api_id)->with('gateway')->latest()->first();
                        if($deposit){
                            if($deposit->status=="Complete"){
                                $message = $this->messages[$api->lang]['transaction_completed_again'];
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }elseif($deposit->status=="Reject"){
                                $message = $this->messages[$api->lang]['transaction_rejected'];
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }else{
                                // $message = "The transaction is in pending state. Please hold on while we transfer your request to our customer service.";
                                
                                if (isset($TG_message['photo'])) {
                                    $image_processed = 0;
                                    try {
                                        $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                        $photo = end($TG_message['photo']);
                                        $file_id = $photo['file_id'];
                                        LaravelLog::info("Got file_id: $file_id");
                            
                                        // Get file info from Telegram
                                        $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                        LaravelLog::info("Requesting file info from: $getFileUrl");
                                        $fileData = Http::get($getFileUrl)->json();
                                        LaravelLog::info("File info response: " . json_encode($fileData));
                            
                                        if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                            $file_path = $fileData['result']['file_path'];
                                            $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                            LaravelLog::info("Downloading image from: $fileUrl");
                            
                                            // Use cURL to fetch the image data because allow_url_fopen is disabled
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                            $imageContent = curl_exec($ch);
                                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                            curl_close($ch);
                            
                                            if ($imageContent && $httpCode === 200) {
                                                $tempImagePath = storage_path('app/temp_' . time() . '.jpg');
                                                file_put_contents($tempImagePath, $imageContent);
                                                LaravelLog::info("Image saved temporarily at: $tempImagePath");
                            
                                                try {
                                                    $apiKey = env('OCR_SPACE_API_KEY', 'K83793710188957');  // Update this with your new API key
                                                    
                                                    // Process the image before sending to OCR
                                                    try {
                                                        // Try to improve image quality before OCR processing
                                                        if (extension_loaded('imagick')) {
                                                            LaravelLog::info("Using Imagick for image preprocessing");
                                                            $imagick = new \Imagick($tempImagePath);
                                                            
                                                            // Enhanced image preprocessing
                                                            $imagick->setImageFormat('png'); // Convert to PNG for better quality
                                                            $imagick->contrastImage(1);
                                                            $imagick->sharpenImage(0, 1.0);
                                                            $imagick->normalizeImage();
                                                            $imagick->despeckleImage(); // Remove small dots
                                                            $imagick->enhanceImage(); // Enhance local contrast
                                                            
                                                            // Increase resolution if too low
                                                            $resolution = $imagick->getImageResolution();
                                                            if ($resolution['x'] < 300 || $resolution['y'] < 300) {
                                                                $imagick->setImageResolution(300, 300);
                                                                $imagick->resampleImage(300, 300, \Imagick::FILTER_LANCZOS, 1);
                                                            }
                                                            
                                                            // Convert to grayscale for better OCR
                                                            $imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
                                                            
                                                            // Additional preprocessing steps
                                                            $imagick->levelImage(0, 1.0, 1.0); // Adjust levels
                                                            $imagick->unsharpMaskImage(0, 1, 1, 0.05); // Sharpen
                                                            $imagick->despeckleImage(); // Remove noise
                                                            
                                                            // Save the enhanced image
                                                            $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                            $imagick->writeImage($enhancedImagePath);
                                                            
                                                            // Use the enhanced image if it exists
                                                            if (file_exists($enhancedImagePath)) {
                                                                $tempImagePath = $enhancedImagePath;
                                                                LaravelLog::info("Using enhanced image: $enhancedImagePath");
                                                            }
                                                        } else {
                                                            LaravelLog::info("Imagick not available, using GD for basic enhancement");
                                                            $image = imagecreatefromstring(file_get_contents($tempImagePath));
                                                            if ($image !== false) {
                                                                // Apply basic enhancements
                                                                imagefilter($image, IMG_FILTER_CONTRAST, -10);
                                                                imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);
                                                                imagefilter($image, IMG_FILTER_GRAYSCALE);
                                                                
                                                                $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                                imagepng($image, $enhancedImagePath, 9); // High quality PNG
                                                                imagedestroy($image);
                                                                
                                                                if (file_exists($enhancedImagePath)) {
                                                                    $tempImagePath = $enhancedImagePath;
                                                                    LaravelLog::info("Using GD enhanced image: $enhancedImagePath");
                                                                }
                                                            }
                                                        }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("Image enhancement failed: " . $e->getMessage());
                                                        // Continue with original image
                                                    }
                                                    
                                                    // First try with OCR Engine 2 (better for receipts and complex text)
                                                    $ch = curl_init();
                                                    curl_setopt_array($ch, [
                                                        CURLOPT_URL => 'https://api.ocr.space/parse/image',
                                                        CURLOPT_RETURNTRANSFER => true,
                                                        CURLOPT_HTTPHEADER => ['apikey: ' . $apiKey],
                                                        CURLOPT_POST => true,
                                                        CURLOPT_POSTFIELDS => [
                                                            'file' => new \CURLFile($tempImagePath),
                                                            'language' => 'eng',  // Changed from eng+ben to just eng as it was causing issues
                                                            'isOverlayRequired' => 'false',
                                                            'detectOrientation' => 'true',
                                                            'scale' => 'true',
                                                            'OCREngine' => '2',
                                                            'isTable' => 'true',
                                                            'filetype' => 'Auto',
                                                            'detectCheckbox' => 'false',
                                                            'isCreateSearchablePdf' => 'false',
                                                            'isSearchablePdfHideTextLayer' => 'false'
                                                        ],
                                                    ]);
                                                    
                                                    // Add detailed logging
                                                    LaravelLog::info('Sending request to OCR.space API...');
                                                    $result = curl_exec($ch);
                                                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                    $error = curl_error($ch);
                                                    curl_close($ch);
                                                    
                                                    if ($error) {
                                                        LaravelLog::error("CURL Error: " . $error);
                                                        throw new \Exception("OCR API request failed: " . $error);
                                                    }
                                                    
                                                    // Log the raw OCR response for debugging
                                                    LaravelLog::info("OCR API Raw Response: " . $result);
                                                    
                                                    if ($httpCode === 200) {
                                                        $ocrResult = json_decode($result, true);
                                                        LaravelLog::info("OCR.space API Response: " . json_encode($ocrResult));
                                                        
                                                        if (isset($ocrResult['ParsedResults'][0]['ParsedText'])) {
                                                            $extractedText = $ocrResult['ParsedResults'][0]['ParsedText'];
                                                            LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                            
                                                            /////////////////////////////
                                                            //////////////////////////////
                                                            /////////////////////////////////
                                                            
                                                            // Initialize the gateway_name variable with a default value
                                                            $gateway_name = $deposit->gateway->name ?? '';
                                                            if (!empty($deposit->gateway) && !empty($deposit->gateway->name)) {
                                                                $gateway_name = $deposit->gateway->name;
                                                            }
                                                            
                                                            if(strtolower($gateway_name)=="bkash"){
                                                                // Enhanced bKash transaction ID patterns
                                                                $txnId = null;
                                                                $patterns = [
                                                                    // CDR/CD/CDP patterns with more flexible length
                                                                    '/\b(?:CDR|CD|CDP)[0-9A-Z]{6,12}\b/i',
                                                                    // Generic bKash transaction pattern
                                                                    '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                    // Transaction ID with ID label
                                                                    '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                    // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                    '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/'
                                                                ];

                                                                foreach ($patterns as $pattern) {
                                                                    if (preg_match($pattern, $extractedText, $matches)) {
                                                                        $txnId = $matches[0];
                                                                        LaravelLog::info("Found bKash Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                        break;
                                                                    }
                                                                }

                                                                // Amount patterns (handle both Bengali and English numerals)
                                                                LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                
                                                                // Try each pattern separately and log results
                                                                if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                } 
                                                                elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                }

                                                                // Phone number patterns
                                                                if (preg_match('/(?:Account|Number)\s*:?\s*(01\d{9})/', $extractedText, $matches) ||
                                                                    preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1];
                                                                    LaravelLog::info("Found phone number: " . $phone_number_by_telegram_message);
                                                                }
                                                            } elseif(strtolower($gateway_name)=="nagad"){
                                                                LaravelLog::info("Processing Nagad payment with text: " . $extractedText);
                                                                
                                                                // Enhanced Nagad transaction ID patterns
                                                                $txnId = null;
                                                                $patterns = [
                                                                    // 73 prefix pattern with flexible length
                                                                    '/\b(?:73|NAGAD)[0-9A-Z]{5,10}\b/i',
                                                                    // Transaction ID with ID label
                                                                    '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                    // Generic Nagad transaction pattern
                                                                    '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                    // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                    '/\b(?:[A-Z]{2,4}[0-9A-Z]{5,10})\b/'
                                                                ];

                                                                foreach ($patterns as $pattern) {
                                                                    if (preg_match($pattern, $extractedText, $matches)) {
                                                                        $txnId = $matches[0];
                                                                        LaravelLog::info("Found Nagad Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                        break;
                                                                    }
                                                                }

                                                                // Amount patterns (handle both Bengali and English numerals)
                                                                LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                
                                                                // Try each pattern separately and log results
                                                                if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                } 
                                                                elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                }

                                                                // Phone number patterns - handle both formats
                                                                if (preg_match('/(\d{5})-(\d{6})/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                    LaravelLog::info("Found phone number with dash format: " . $phone_number_by_telegram_message);
                                                                }
                                                                elseif (preg_match('/\b(01\d{3})(\d{6})\b/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                    LaravelLog::info("Found phone number without dash: " . $phone_number_by_telegram_message);
                                                                }
                                                            }
                                                            elseif(strtolower($gateway_name)=="rocket"){
                                                                LaravelLog::info("Processing Rocket payment with text: " . $extractedText);
                                                                
                                                                // Enhanced Rocket transaction ID patterns
                                                                $txnId = null;
                                                                $patterns = [
                                                                    // 5359/5358 prefix pattern
                                                                    '/\b(?:5359|5358)[0-9]{6,8}\b/',
                                                                    // Transaction ID with ID label
                                                                    '/\b(?:ID|Transaction ID)[\s#:]*([0-9]{10,12})\b/i',
                                                                    // Generic Rocket transaction pattern
                                                                    '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9]{10,12})\b/i',
                                                                    // Fallback pattern for any numeric sequence that looks like a transaction ID
                                                                    '/\b(?:[0-9]{10,12})\b/'
                                                                ];

                                                                foreach ($patterns as $pattern) {
                                                                    if (preg_match($pattern, $extractedText, $matches)) {
                                                                        $txnId = $matches[0];
                                                                        LaravelLog::info("Found Rocket Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                        break;
                                                                    }
                                                                }

                                                                // Amount patterns (handle both Bengali and English numerals)
                                                                LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                
                                                                // Try each pattern separately and log results
                                                                if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                } 
                                                                elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                }
                                                                elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                    $amount = str_replace(',', '', $matches[1]);
                                                                    LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                }

                                                                // Phone number patterns for Rocket
                                                                if (preg_match('/Cash-out\s*\((\d{11})\)/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1];
                                                                    LaravelLog::info("Found phone number from Cash-out: " . $phone_number_by_telegram_message);
                                                                }
                                                                elseif (preg_match('/(?:Agent|Rocket)\s*A\/C\s*No\.?\s*:?\s*(\d{11})/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1];
                                                                    LaravelLog::info("Found phone number from A/C No: " . $phone_number_by_telegram_message);
                                                                }
                                                                elseif (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1];
                                                                    LaravelLog::info("Found phone number generic format: " . $phone_number_by_telegram_message);
                                                                }
                                                            }
                                                            
                                                            // If no specific provider pattern matched, try generic patterns as fallback
                                                            if (!isset($txnId) || empty($txnId)) {
                                                                $genericPatterns = [
                                                                    // Generic transaction ID patterns
                                                                    '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                    '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                    // Look for any sequence that might be a transaction ID
                                                                    '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/',
                                                                    '/\b(?:[0-9A-Z]{6,12})\b/'
                                                                ];

                                                                foreach ($genericPatterns as $pattern) {
                                                                    if (preg_match($pattern, $extractedText, $matches)) {
                                                                        $txnId = $matches[0];
                                                                        LaravelLog::info("Found Transaction ID using generic pattern: " . $pattern . " - ID: " . $txnId);
                                                                        break;
                                                                    }
                                                                }
                                                            }

                                                            // Validate the found transaction ID
                                                            if (isset($txnId) && !empty($txnId)) {
                                                                // Remove any non-alphanumeric characters
                                                                $txnId = preg_replace('/[^A-Z0-9]/i', '', $txnId);
                                                                
                                                                // Log the final transaction ID
                                                                LaravelLog::info("Final Transaction ID after validation: " . $txnId);
                                                            }

                                                            // Generic amount pattern as fallback
                                                            if (!isset($amount) || empty($amount)) {
                                                                if (preg_match('/\b(\d{2,4}(?:\.\d{2})?)\s*(?:Tk|BDT)?\b/', $extractedText, $matches)) {
                                                                    $amount = $matches[1];
                                                                }
                                                            }

                                                            // Generic phone number pattern as fallback
                                                            if (!isset($phone_number_by_telegram_message) || empty($phone_number_by_telegram_message)) {
                                                                if (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                    $phone_number_by_telegram_message = $matches[1];
                                                                }
                                                            }
                                                            
                                                            
                                                            /////////////////////////////
                                                            //////////////////////////////
                                                            /////////////////////////////////
                                                            
                                                            // Format the message with extracted information
                                                            $message = "?? *Extracted Information:*\n\n";
                                                            
                                                            if (isset($txnId) && !empty($txnId)) {
                                                                $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                LaravelLog::info("Adding transaction ID to message: " . $txnId);
                                                            }
                                                            
                                                            if (isset($amount) && !empty($amount)) {
                                                                $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                LaravelLog::info("Adding amount to message: " . $amount);
                                                            }
                                                            
                                                            if (isset($phone_number_by_telegram_message) && !empty($phone_number_by_telegram_message)) {
                                                                $message .= "?? *Phone:* `" . $phone_number_by_telegram_message . "`\n";
                                                                LaravelLog::info("Adding phone to message: " . $phone_number_by_telegram_message);
                                                            }
                                                            
                                                            // Add the full extracted text at the bottom
                                                            $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                            
                                                            
                                                            LaravelLog::info("Final message being sent 8: " . $message);
                                                            
                                                            // Add instructions for verification with the correct transaction ID
                                                            if ($txnId) {
                                                                
                                                            /////////////////////////////
                                                            //////////////////////////////
                                                            /////////////////////////////////
                                                            
                                                            DB::beginTransaction();
                                                            $payment = PendingPayment::where('txn_id', $txnId)->where('status', 0)->lockForUpdate()->first();
                                                            if($payment){
                                                                if($payment){

                                                                        $check_payment_txn = Payment::where('txn_id', $payment->txn_id)->first();
                                                                        if ($check_payment_txn) {
                                                                            DB::rollBack();

                                                                            $message = "By This Txn no, Payment Already Completed.";

                                                                            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                'chat_id' => $TG_message['chat']['id'],
                                                                                'text' => $message,
                                                                                'parse_mode' => 'Markdown',
                                                                                'reply_to_message_id' => $TG_message['message_id']
                                                                            ]);

                                                                            $image_processed=1;
                                                                            return response()->json(['status' => 'success'], 200);
                                                                        }
                                                                    // Add amount validation
                                                                    if(isset($amount) && $amount > 0) {
                                                                        $expectedAmount = $deposit->amount;
                                                                        $extractedAmount = (float)$amount;
                                                                        
                                                                        // Check if amounts don't match
                                                                        if(abs($extractedAmount - $expectedAmount) > 0.01) { // Using small epsilon for float comparison
                                                                            // Save the new TRX ID to the deposit/order
                                                                            $deposit->txn_id = $txnId; // Save the new TRX ID
                                                                            $deposit->save();

                                                                            $message = "⚠️ *Amount Mismatch* ⚠️\n\n";
                                                                            $message .= "Expected Amount: `" . $expectedAmount . "`\n";
                                                                            $message .= "Image Amount: `" . $extractedAmount . "`\n\n";
                                                                            $message .= "New TRX number submitted and callback sent with correct amount.";

                                                                            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                'chat_id' => $TG_message['chat']['id'],
                                                                                'text' => $message,
                                                                                'parse_mode' => 'Markdown',
                                                                                'reply_to_message_id' => $TG_message['message_id']
                                                                            ]);

                                                                            $image_processed=1;
                                                                            return response()->json(['status' => 'success'], 200);
                                                                        }
                                                                    }
                                                                    
                                                                    $partner_api_key = $api_key;
                                                                    $source = $partner_api_key->website;
                                                                    $api_id = $partner_api_key->id;
                                                                    
                                                                    
                                                                    $sum = Payment::whereYear('created_at', now()->year)
                                                                        ->whereMonth('created_at', now()->month)
                                                                        ->where('api_id', $api_id)
                                                                        ->where('status', 'Complete')
                                                                        ->sum('amount');

                                                                    $account = EWalletAccount::where('e_wallet_name', $deposit->gateway->code)
                                                                        ->where('account_no', $request->e_wallet_phone_number)
                                                                        ->where('status', 1)
                                                                        ->first();
                                                                    if (!$account) {
                                                                        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                            'chat_id' => $TG_message['chat']['id'],
                                                                            'text' => 'E-Wallet Account Issue, Contact With Administrator',
                                                                            'parse_mode' => 'Markdown',
                                                                            'reply_to_message_id' => $TG_message['message_id']
                                                                        ]);

                                                                        $image_processed=1;
                                                                        return response()->json(['status' => 'success'], 200);
                                                                    }


                                                                    $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                                                                    if ($commissions) {
                                                                        $charge = $commissions->deposit_percentage * $deposit->amount / 100;
                                                                    } else {
                                                                        $commissions = Commission::where('category_id', $partner_api_key->category_id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                                                                        if ($commissions) {
                                                                            $charge = $commissions->deposit_percentage * $deposit->amount / 100;
                                                                        }
                                                                    }
                                                
                                                                    $charge = str_replace(',', '', $charge);
                                                                    $charge = (float)$charge;
                                                                    $charge = round($charge, 2);
                                                                    
                                                                    $amount = str_replace(',', '', $amount);
                                                                    $amount = (float)$amount;
                                                                    $amount = round($amount, 2);
                                                                    
                                                                    if($amount>0){
                                                                        $final_amo = getAmount($amount - $charge);
                                                                            
                                                                        if($amount==$payment->amount){
                                                                            $order = Payment::where('id', $deposit->id)->with(['gateway', 'user'])->lockForUpdate()->first();
                                                                            $message_to_show = "*Transection of Differant Amount Completed*";
                                                                        }else{
                                                                            $message_to_show = "*Transection Completed*";
                                                                            $partner_transection_id = "createdByAdmin_" . time();
                                                                            
                                                                            $order = new Payment();
                                                                            $order->user_id = 0;
                                                                            $order->gateway_id = $deposit->gateway_id;
                                                                            $order->amount = $payment->amount;
                                                                            $order->partner_transection_id = $partner_transection_id;
                                                                            $order->member_id = $deposit->member_id;
                                                                            $order->charge = $charge;
                                                                            $order->sender = $deposit->account_no;
                                                                            $order->transaction = strRandom();
                                                                            $order->try = 0;
                                                                            $order->status = "Pending";
                                                                            $order->api_key = $partner_api_key->api_key;
                                                                            $order->api_id = $api_id;
                                                                            $order->e_wallet_phone_number = $deposit->e_wallet_phone_number;
                                                                            $order->request_source = "Telegram";
                                                                            $order->save();


                                                                            $parentIds = ParentCommission::where('user_id', $partner_api_key->id)
                                                                                ->pluck('parent_id')
                                                                                ->unique()
                                                                                ->values();
                                                                            foreach($parentIds as  $parentId){

                                                                                $parent_charge = 0;

                                                                                $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('from_amount', '<=', $sum)->where('to_amount', '>=', $sum)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->first();
                                                                                if ($parent_commission) {
                                                                                    $parent_charge = $parent_commission->deposit_percentage * $deposit->amount / 100;
                                                                                } else {
                                                                                    $parent_commission = ParentCommission::where('user_id', $partner_api_key->id)->where('parent_id', $parentId)->where('commission_id', $commissions->id)->where('gateway_id', 'like', "%{$account->e_wallet_name}%")->where('type', 'like', "%{$account->type}%")->orderBy('to_amount', 'desc')->first();
                                                                                    if ($parent_commission) {
                                                                                        $parent_charge = $parent_commission->deposit_percentage * $deposit->amount / 100;
                                                                                    }
                                                                                }

                                                                                if($parent_charge>0){
                                                                                    $PartnerCommission = new PartnerCommission();
                                                                                    $PartnerCommission->api_id = $partner_api_key->id;
                                                                                    $PartnerCommission->from_id = $parentId;
                                                                                    $PartnerCommission->type = 1;
                                                                                    $PartnerCommission->amount = $deposit->amount;
                                                                                    $PartnerCommission->charges = $charge;
                                                                                    $PartnerCommission->total_amount = $deposit->amount - $charge;
                                                                                    $PartnerCommission->charges_p = $commissions->deposit_percentage ?? 0;
                                                                                    $profit_p = $parent_commission->deposit_percentage;
                                                                                    $profit = $profit_p * $deposit->amount / 100;
                                                                                    $PartnerCommission->profit = $profit;
                                                                                    $PartnerCommission->profit_p = $profit_p;
                                                                                    $PartnerCommission->transaction_id = $deposit->id;
                                                                                    $PartnerCommission->status = 0;
                                                                                    $PartnerCommission->save();
                                                                                }




                                                                            }
                                                                            
                                                                            
                                                                            if($order){
                                                                                $order = Payment::where('id', $deposit->id)->with(['gateway', 'user'])->lockForUpdate()->first();
                                                                                $commit = 0;
                                                                                if (strpos($payment->sender, 'XXXX') !== false && ($payment->mac_address=="111.111.11.111" || $payment->mac_address=="222.222.22.222")) {
                                                                                    if(!empty($order->sender)){
                                                                                        $payment->sender = $order->sender;
                                                                                    }
                                                                                }elseif (strpos($payment->sender, '***') !== false && ($payment->mac_address=="111.111.11.111" || $payment->mac_address=="222.222.22.222")) {
                                                                                    if(!empty($order->sender)){
                                                                                        $payment->sender = $order->sender;
                                                                                    }
                                                                                }
                                                                                
                                                                                
                                                                                
                                                                                if ($source != env('APP_WEBSITE')) {
                                                                                    $api_balance_row = Api::where('api_key', $api_id)->where('type', 'Admin')->lockForUpdate()->first();
                                                                                    $net_amount = $payment->amount - $charge; // Move this outside the if block
                                                                                    
                                                                                    if ($api_balance_row) {
                                                                                        $api_balance_row->balance += $net_amount;
                                                                                        $api_balance_row->save();

                                                                                        $Log = new Log();
                                                                                        $Log->date_time = $payment->updated_at;
                                                                                        $Log->final_amount = $net_amount;
                                                                                        $Log->balance = $api_balance_row->balance;
                                                                                        $Log->transection_type = 1;
                                                                                        $Log->transection_id = $order->id;
                                                                                        $Log->partner_id = $api_balance_row->id;
                                                                                        $Log->source = 'TelegramVerify';
                                                                                        $Log->save();
                                                                                    } else {
                                                                                        LaravelLog::error('API balance row not found for api_id: ' . $api_id . '. Transaction ID: ' . $payment->id);
                                                                                        // Continue processing but log the error
                                                                                    }
                                                                                } else {
                                                                                    $net_amount = $payment->amount - $charge; // Define net_amount for other cases too
                                                                                }
                                                            
                                                                                $order->status = 'Complete';
                                                                                $order->trans_complete_date = Carbon::now();
                                                                                $order->completed_source = 'Telegram';
                                                                                $order->charge = $charge;

                                                                                if(empty($order->sender) || $order->sender==0){
                                                                                    $order->sender = $payment->sender;
                                                                                }
                                                                                
                                                                                $order->txn_id = $payment->txn_id;
                                                                                $order->date_time = $payment->date_time;
                                                                                $order->transaction_type = $payment->transaction_type;
                                                                                $order->ip_address = $payment->ip_address;
                                                                                $order->e_wallet_type = $payment->e_wallet_type;
                                                                                $order->mac_address = $payment->mac_address;
                                                                                $order->fee = $payment->fee;
                                                                                $order->commission = $payment->commission;
                                                                                $order->e_wallet_charges = $payment->e_wallet_charges;
                                                                                $order->payment_received_at = $payment->created_at;



                                                                                $order->save();

                                                                                $payment->status = 1;
                                                                                $payment->save();
                                                                                $payment=null;
                                                                                // $payment->delete();
                                                                                
                                                            
                                                                                DB::commit();
                                                                                $commit = 1;
                                                            
                                                                                $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $api_id)->whereDate('created_at', '>=', $order->created_at)->get();
                                                                                foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                                                                    $amount_to_update = $DailyPartnerSummary_record->closing_balance + $net_amount;
                                                                                    $amount_to_update = round($amount_to_update, 2);
                                                                                    // $amount_to_update = floor($amount_to_update * 100) / 100;
                                                                                    $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                                                                    $DailyPartnerSummary_record->save();

                                                                                    $summary_log = new DailyPartnerSummaryLog();
                                                                                    if ($partner_api_key) {
                                                                                        $summary_log->partner_id = $partner_api_key->id;
                                                                                        $summary_log->partner_balance = $partner_api_key->balance;
                                                                                    } else {
                                                                                        LaravelLog::error('Partner API key not found for api_id: ' . $api_id);
                                                                                        $summary_log->partner_id = $api_id;
                                                                                        $summary_log->partner_balance = 0;
                                                                                    }
                                                                                    $summary_log->payment_id = $order->id;
                                                                                    $summary_log->total_amount = $net_amount;
                                                                                    $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                                                                    $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                                                                    $summary_log->source = 'Telegram';
                                                                                    $summary_log->save();
                                                                                }
                                                            
                                                            
                                                                                
                                                                                
                                                            
                                                                                $PartnerCommissions = PartnerCommission::where('transaction_id', $order->id)->where('type', 1)->where('status', 0)->get();
                                                                                foreach ($PartnerCommissions as $PartnerCommission) {
                                                                                    $PartnerCommission->status = 1;
                                                                                    $PartnerCommission->save();
                                                            
                                                                                    DB::beginTransaction();
                                                                                    $parent_api_key = Api::where('id', $PartnerCommission->from_id)->lockForUpdate()->first();
                                                                                    if($parent_api_key){
                                                                                        $parent_api_key->balance += $PartnerCommission->profit;
                                                                                        $parent_api_key->save();
                                                                
                                                                                        $Log = new Log();
                                                                                        $Log->date_time = $PartnerCommission->created_at;
                                                                                        $Log->final_amount = $PartnerCommission->profit;
                                                                                        $Log->balance = $parent_api_key->balance;
                                                                                        $Log->transection_type = 5;
                                                                                        $Log->transection_id = $PartnerCommission->id;
                                                                                        $Log->partner_id = $PartnerCommission->from_id;
                                                                                        $Log->source = 'Telegram';
                                                                                        $Log->save();
                                                                                        DB::commit();
                                                                
                                                                                        $DailyPartnerSummary_records =  DailyPartnerSummary::where('api_id', $parent_api_key->id)->whereDate('created_at', '>=', $PartnerCommission->created_at)->get();
                                                                                        foreach ($DailyPartnerSummary_records as $DailyPartnerSummary_record) {
                                                                                            $amount_to_update = $DailyPartnerSummary_record->closing_balance + ($PartnerCommission->profit);
                                                                                            $amount_to_update = round($amount_to_update, 2);
                                                                                            // $amount_to_update = floor($amount_to_update * 100) / 100;
                                                                                            $DailyPartnerSummary_record->closing_balance = $amount_to_update;
                                                                                            $DailyPartnerSummary_record->save();
                                                                
                                                                                            $summary_log = new DailyPartnerSummaryLog();
                                                                                            $summary_log->partner_id = $parent_api_key->id;
                                                                                            $summary_log->partner_balance = $parent_api_key->balance;
                                                                                            $summary_log->payment_id = $PartnerCommission->id;
                                                                                            $summary_log->total_amount = $PartnerCommission->profit;
                                                                                            $summary_log->summary_id = $DailyPartnerSummary_record->id;
                                                                                            $summary_log->closing_balance = $DailyPartnerSummary_record->closing_balance;
                                                                                            $summary_log->source = 'Telegram';
                                                                                            $summary_log->save();
                                                                                        }
                                                                                    }
                                                                                    
                                                                                }
                                                            }
                                                                                if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {
                                                            
                                                                                    $string_to_hash = json_encode(array(
                                                                                        "amount" => strval($this->convertStringToNumber($order->amount)),
                                                                                        "api_key" => $partner_api_key->api_key,
                                                                                        "e_wallet_name" => $order->e_wallet_name,
                                                                                        "id" => strval($order->id),
                                                                                        'transaction_type' => 'Deposit',
                                                                                        "user_account_no" => strval($order->sender),
                                                            
                                                                                    ));
                                                                                    $secretKey = $partner_api_key->secret_key;
                                                                                    $hash = hash("sha256", $string_to_hash);
                                                                                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                                                                                    $timestamp = time();
                                                                                    $combined = $hmac . $timestamp;
                                                                                    $sign = base64_encode($combined);
                                                            
                                                            
                                                                                    $array_data = [
                                                                                                'id' => $order->id,
                                                                                                'partner_transection_id' => $order->partner_transection_id,
                                                                                                'transaction_type' => 'Deposit',
                                                                                                'e_wallet_name' => $order->e_wallet_name,
                                                                                                'amount' => $this->convertStringToNumber($order->amount),
                                                                                                'user_account_no' => $order->sender,
                                                                                                'txn_id' => $order->txn_id,
                                                                                                'e_wallet_phone_number' => $order->e_wallet_phone_number,
                                                                                                'e_wallet_type' => $order->e_wallet_type,
                                                                                                'charges' => $this->convertStringToNumber($order->charge),
                                                                                                'status' => $order->status,
                                                                                                'completion_date' => Carbon::parse($order->date_time)->toDateString(),
                                                                                                'completion_time' => Carbon::parse($order->date_time)->toTimeString(),
                                                                                                'created_at' => $order->created_at,
                                                                                                'updated_at' => $order->updated_at,
                                                                                                'sign' => $sign,
                                                                                    ];
                                                            
                                                                                    if(!empty($order->member_id)){
                                                                                        $array_data['member_id'] = $order->member_id;
                                                                                    }
                                                            
                                                            
                                                                                    $requestData = [
                                                                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                                                                        'request_url' => $partner_api_key->api_endpoint_deposit,
                                                                                        'request_payload' => json_encode($array_data),
                                                                                        'request_headers' => json_encode([
                                                                                            'Content-Type' => 'application/json',
                                                                                            'Cookie' => 'XSRF-TOKEN=' . Str::random(40),
                                                                                        ]),
                                                                                        'created_at' => now(),
                                                                                        'updated_at' => now(),
                                                                                    ];
                                                            
                                                                                    $logId = DB::table('api_logs')->insertGetId($requestData);
                                                                                    try {
                                                                                        $csrfToken = Str::random(40);
                                                                                        $response = Http::withHeaders([
                                                                                            'Content-Type' => 'application/json',
                                                                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                                                                        ])
                                                                                            ->post($partner_api_key->api_endpoint_deposit, $array_data);
                                                            
                                                                                        if ($response) {
                                                                                            $responseData = [
                                                                                                'response_code' => $response->status(),
                                                                                                'response_payload' => $response->body(),
                                                                                                'response_headers' => json_encode($response->headers()),
                                                                                            ];
                                                            
                                                                                            DB::table('api_logs')->where('id', $logId)->update($responseData);
                                                                                        }
                                                                                    } catch (\Exception $e) {
                                                                                        //
                                                                                    }
                                                                                }
                                                                                
                                                                                
                                                                                
                                                                                
                                                                            }
                                                                            
                                                                            $support_chat_id = "-4786890063";
                                                                            $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                            $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                            $message_support = "";
                                                                            $message_support .= "?? ".$message_to_show." ??\n\n";
                                                                            $message_support .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                            $message_support .= "*Order Id:* `".$deposit->id."`\n";
                                                                            $message_support .= "*Transaction ID:* `".$txnId."`\n";
                                                                            $message_support .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                            $message_support .= "*Remark:* Transaction processed and callback sent.\n";
                                                                            $message_support .= "*Status:* `Complete`\n";
                                                                            
                                                                            $response = Http::post($url_support, [
                                                                                'chat_id' => $support_chat_id,
                                                                                'text' => $message_support,
                                                                                'parse_mode' => 'Markdown',
                                                                            ]);
                                                                            
                                                                            
                                                                                $message = "";
                                                                                $message .= "Your transaction has been marked as completed, and the callback has also been sent.\n\n";
                                                                                $message .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                                $message .= "*Order Id:* `".$deposit->id."`\n";
                                                                                $message .= "*Transaction ID:* `".$txnId."`\n";
                                                                                $message .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                                $message .= "*Status:* `Complete`\n";
                                                                            
                                                                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                    'chat_id' => $TG_message['chat']['id'],
                                                                                    'text' => $message,
                                                                                    'parse_mode' => 'Markdown',
                                                                                    'reply_to_message_id' => $TG_message['message_id']
                                                                                ]);
                                                                                
                                                                                $image_processed=1;
                                                                        }else{
                                                                            $support_chat_id = "-4786890063";
                                                                            $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                            $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                            $message_support = "";
                                                                            $message_support .= "?? *Transaction Not Found* ??\n\n";
                                                                            $message_support .= "*Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                            $message_support .= "*Order Id:* `".$deposit->id."`\n";
                                                                            $message_support .= "*Transaction ID:* `".$txnId."`\n";
                                                                            $message_support .= "*Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                            $message_support .= "*Remark:* Transaction processed and callback sent.\n";
                                                                            $message_support .= "*Status:* `Not Found`\n";
                                                                            
                                                                            $response = Http::post($url_support, [
                                                                                'chat_id' => $support_chat_id,
                                                                                'text' => $message_support,
                                                                                'parse_mode' => 'Markdown',
                                                                            ]);
                                                                            
                                                                            $message = sprintf($this->messages[$api->lang]['transaction_pending'], 
                                                                                $deposit->partner_transection_id,
                                                                                $deposit->id,
                                                                                $txnId,
                                                                                (isset($amount) ? $amount : "Not found")
                                                                            );
                                                                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                                'chat_id' => $TG_message['chat']['id'],
                                                                                'text' => $message,
                                                                                'parse_mode' => 'Markdown',
                                                                                'reply_to_message_id' => $TG_message['message_id']
                                                                            ]);
                                                                            
                                                                            $image_processed=1;
                                                                        }
                                                                        
                                                                        
                                                                            
                                                                    }
                                                                    
                                                                }else{
                                                                    $support_chat_id = "-4786890063";
                                                                    $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                                                    $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                                                    $message_support = "";
                                                                    $message_support .= "?? *Transaction Not Found* ??\n\n";
                                                                    $message_support .= " *Merchant Order:* `".$deposit->partner_transection_id."`\n";
                                                                    $message_support .= " *Transaction ID:* `".$txnId."`\n";
                                                                    $message_support .= " *Amount:* `".(isset($amount) ? $amount : "Not found")."`\n";
                                                                    $message_support .= " *Remark:* Transaction ID not found in system.\n";
                                                                    $message_support .= " *Status:* `Pending`\n";
                                                                    $message_support .= " *Payment Platform:* `".$deposit->gateway->name."`\n";
                                                                    
                                                                    $response = Http::post($url_support, [
                                                                        'chat_id' => $support_chat_id,
                                                                        'text' => $message_support,
                                                                        'parse_mode' => 'Markdown',
                                                                    ]);
                                                                    
                                                                    $message = sprintf($this->messages[$api->lang]['transaction_pending'], 
                                                                        $deposit->partner_transection_id,
                                                                        $deposit->id,
                                                                        $txnId,
                                                                        (isset($amount) ? $amount : "Not found")
                                                                    );
                                                                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                                                        'chat_id' => $TG_message['chat']['id'],
                                                                        'text' => $message,
                                                                        'parse_mode' => 'Markdown',
                                                                        'reply_to_message_id' => $TG_message['message_id']
                                                                    ]);
                                                                    
                                                                    $image_processed=1;
                                                                }
                                                                
                                                                if($commit==0){
                                                                    DB::commit();
                                                                }
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                ///////////////////////////////// 
                                                                    
                                                                    
                                                                    
                                                            
                                                                } else {
                                                                    $message = sprintf($this->messages[$api->lang]['invalid_command'], 
                                                                        $deposit->partner_transection_id,
                                                                        $deposit->id
                                                                    );
                                                                    $response = Http::post($url, [
                                                                        'chat_id' => $sender_chat['id'],
                                                                        'text' => $message,
                                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                                        'parse_mode' => 'Markdown',
                                                                    ]);
                                                                    
                                                                    $image_processed=1;
                                                                }
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = sprintf($this->messages[$api->lang]['image_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                        } else {
                                                            LaravelLog::error("OCR.space API Error: " . $result);
                                                            $message = sprintf($this->messages[$api->lang]['image_error'], 
                                                                $deposit->partner_transection_id,
                                                                $deposit->id
                                                            );
                                                            $response = Http::post($url, [
                                                                'chat_id' => $sender_chat['id'],
                                                                'text' => $message,
                                                                'reply_to_message_id' => $TG_message['message_id'],
                                                                'parse_mode' => 'Markdown',
                                                            ]);
                                                            
                                                            $image_processed=1;
                                                        }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                        }
                                        
                                        if($image_processed==0){
                                            $message = sprintf($this->messages[$api->lang]['service_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                        }
                                        
                                    }else{
                                        $message = sprintf($this->messages[$api->lang]['service_error'], 
                                                                    $deposit->partner_transection_id,
                                                                    $deposit->id
                                                                );
                                        $response = Http::post($url, [
                                            'chat_id' => $sender_chat['id'],
                                            'text' => $message,
                                            'reply_to_message_id' => $TG_message['message_id'],
                                            'parse_mode' => 'Markdown',
                                        ]);
                                    }
                                }

                                    
                                    
                                    

                            }else{
                                $withdrawal = Payout::where('partner_transection_id',$orderNumber)->where('api_id',$api->api_id)->latest()->first();
                                if($withdrawal){
                                    if($withdrawal->status=="Complete"){
                                        $message = $this->messages[$api->lang]['transaction_completed_callback'];
                                    }elseif($withdrawal->status=="Reject"){
                                        // Fetch remarks from the payout log
                                        $reason = $withdrawal->feedback;
                                        $message = sprintf($this->messages[$api->lang]['transaction_rejected_with_reason'], $reason);
                                    }else{
                                        $message = $this->messages[$api->lang]['transaction_pending_callback'];
                                        
                                        //
                                        // Add code that send message to support
                                        //
                                        $support_chat_id = "-4786890063";
                                        $botToken_supprot = "7813176060:AAEduBE3za8d-MjoN79ZOBHAhWLVDeLiVBk";
                                        $url_support = "https://api.telegram.org/bot{$botToken_supprot}/sendMessage";
                                        $message_support = "";

                                        if ($withdrawal->status == "Pending") {
                                            $message_support .= "💤 *Withdrawal Pending* 💤\n\n";
                                            $message_support .= "*Merchant Order:* `" . $withdrawal->partner_transection_id . "`\n";
                                            $message_support .= "*Order Id:* `" . $withdrawal->id . "`\n";
                                            $message_support .= "*Amount:* `" . $withdrawal->amount . "`\n";
                                            $message_support .= "*Phone:* `" . $withdrawal->user_account_no . "`\n";
                                            $message_support .= "*Remark:* Withdrawal request is pending for processing.\n";
                                            $message_support .= "*Status:* `Pending`\n";
                                            $message_support .= "*Payment Platform:* `" . $withdrawal->e_wallet_name . "`\n";
                                        }                                            
                                        
                                        
                                        $response = Http::post($url_support, [
                                            'chat_id' => $support_chat_id,
                                            'text' => $message_support,
                                            'parse_mode' => 'Markdown',
                                        ]);
                                    }

                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
        
                                    if($withdrawal->status=="Complete" || $withdrawal->status=="Reject"){
                                        

                                        if (!empty($api_key->api_endpoint_withdrawal) && $api_key->website != env('APP_WEBSITE')) {

                                            $string_to_hash = json_encode(array(
                                                "amount" => strval($this->convertStringToNumber($withdrawal->amount)),
                                                "api_key" => $api_key->api_key,
                                                "e_wallet_name" => $withdrawal->e_wallet_name,
                                                "id" => strval($withdrawal->id),
                                                'transaction_type' => 'Withdrawal',
                                                "user_account_no" => strval($withdrawal->user_account_no),
                                            ));
                                            $secretKey = $api_key->secret_key;
                                            $hash = hash("sha256", $string_to_hash);
                                            $hmac = hash_hmac('sha256', $hash, $secretKey);
                                            $timestamp = time();
                                            $combined = $hmac . $timestamp;
                                            $sign = base64_encode($combined);

                                            $array_data = [
                                                        'id' => $withdrawal->id,
                                                        'partner_transection_id' => $withdrawal->partner_transection_id,
                                                        'transaction_type' => 'Withdrawal',
                                                        'e_wallet_name' => $withdrawal->e_wallet_name,
                                                        'amount' => $this->convertStringToNumber($withdrawal->amount),
                                                        'user_account_no' => $withdrawal->user_account_no,
                                                        'txn_id' => $withdrawal->txn_id,
                                                        'e_wallet_phone_number' => $withdrawal->e_wallet_phone_number,
                                                        'e_wallet_type' => $withdrawal->e_wallet_type,
                                                        'charges' => $this->convertStringToNumber($withdrawal->charge),
                                                        'status' => $withdrawal->status,
                                                        'completion_date' => Carbon::parse($withdrawal->date_time)->toDateString(),
                                                        'completion_time' => Carbon::parse($withdrawal->date_time)->toTimeString(),
                                                        'created_at' => $withdrawal->created_at,
                                                        'updated_at' => $withdrawal->updated_at,
                                                        'sign' => $sign,
                                                        'remarks' => $withdrawal->feedback,
                                                        
                                            ];

                                            if(!empty($withdrawal->member_id)){
                                                $array_data['member_id'] = $withdrawal->member_id;
                                            }


                                            $requestData = [
                                                'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                                'request_url' => $api_key->api_endpoint_withdrawal,
                                                'request_payload' => json_encode($array_data),
                                                'request_headers' => json_encode([
                                                    'Content-Type' => 'application/json',
                                                    'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                                ]),
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ];

                                            $logId = DB::table('api_logs')->insertGetId($requestData);

                                            $csrfToken = csrf_token();
                                            $responseData = [];
                                            try {

                                                $response = Http::withHeaders([
                                                    'Content-Type' => 'application/json',
                                                    'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                                ])
                                                    ->post($api_key->api_endpoint_withdrawal, $array_data);

                                                $responseData = [
                                                    'response_code' => $response->status(),
                                                    'response_payload' => $response->body(),
                                                    'response_headers' => json_encode($response->headers()),
                                                ];

                                                DB::table('api_logs')->where('id', $logId)->update($responseData);
                                                
                                            } catch (\Exception $e) {
                                                LaravelLog::info('Telegram Withdrawal Callback not sent');
                                            }
                                        }
                                    }
                                }else{
                                    $message = sprintf($this->messages[$api->lang]['transaction_not_found'], 
                                        $sender_message,
                                        $api->api_id
                                    );
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                }
                                    
                            }
                        }else{
                            $message = sprintf($this->messages[$api->lang]['invalid_command'], 
                                $sender_message,
                                $api->api_id
                            );
                            $response = Http::post($url, [
                                'chat_id' => $sender_chat['id'],
                                'text' => $message,
                                'reply_to_message_id' => $TG_message['message_id'],
                                'parse_mode' => 'Markdown',
                            ]);
                        }    
                    
                        
                    }elseif(strpos($lowercaseText, "/test") === 0){
                        
                        
                        
                        
                        $parts = explode(" ", $sender_message);
                        $extractedText = '';
                        
                            $gateway_name = "";
                        if(count($parts) >= 2) {
                            $gateway_name = trim($parts[1]);
                        }
                        
                        
                        
                        
                        
                        
                        if (isset($TG_message['photo'])) {
                                        $image_processed = 0;
                                        
                                        try {
                                            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                            $photo = end($TG_message['photo']);
                                            $file_id = $photo['file_id'];
                                            LaravelLog::info("Got file_id: $file_id");
                                    
                                            // Get file info from Telegram
                                            $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                            LaravelLog::info("Requesting file info from: $getFileUrl");
                                            $fileData = Http::get($getFileUrl)->json();
                                            LaravelLog::info("File info response: " . json_encode($fileData));
                                            
                                            
                                    
                                            if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                                $file_path = $fileData['result']['file_path'];
                                                $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                                LaravelLog::info("Downloading image from: $fileUrl");
                                    
                                                // Use cURL to fetch the image data because allow_url_fopen is disabled
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                $imageContent = curl_exec($ch);
                                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);
                                                
                                                
                                                
                                    
                                                if ($imageContent && $httpCode === 200) {
                                                    $tempPath = 'ocr_' . time() . '.jpg';
                                                    $tempImagePath = storage_path('app/public/ocr_images/' . $tempPath);
                                                    file_put_contents($tempImagePath, $imageContent);
                                                    $imageUrl = url('storage/app/public/ocr_images/' . $tempPath);
                                                    LaravelLog::info("Image saved temporarily at: $tempImagePath");
                                                    LaravelLog::info("Image saved temporarily at: $imageUrl");
                                    
                                                    try {
                                                        $apiKey = env('OCR_SPACE_API_KEY', 'K83793710188957');  // Update this with your new API key
                                                        
                                                        // Process the image before sending to OCR
                                                        try {
                                                            // Try to improve image quality before OCR processing
                                                            if (extension_loaded('imagick')) {
                                                                LaravelLog::info("Using Imagick for image preprocessing");
                                                                $imagick = new \Imagick($tempImagePath);
                                                                
                                                                // Enhanced image preprocessing
                                                                $imagick->setImageFormat('png'); // Convert to PNG for better quality
                                                                $imagick->contrastImage(1);
                                                                $imagick->sharpenImage(0, 1.0);
                                                                $imagick->normalizeImage();
                                                                $imagick->despeckleImage(); // Remove small dots
                                                                $imagick->enhanceImage(); // Enhance local contrast
                                                                
                                                                // Increase resolution if too low
                                                                $resolution = $imagick->getImageResolution();
                                                                if ($resolution['x'] < 300 || $resolution['y'] < 300) {
                                                                    $imagick->setImageResolution(300, 300);
                                                                    $imagick->resampleImage(300, 300, \Imagick::FILTER_LANCZOS, 1);
                                                                }
                                                                
                                                                // Convert to grayscale for better OCR
                                                                $imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
                                                                
                                                                // Save the enhanced image
                                                                $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                                $imagick->writeImage($enhancedImagePath);
                                                                
                                                                // Use the enhanced image if it exists
                                                                if (file_exists($enhancedImagePath)) {
                                                                    $tempImagePath = $enhancedImagePath;
                                                                    LaravelLog::info("Using enhanced image: $enhancedImagePath");
                                                                }
                                                            } else {
                                                                LaravelLog::info("Imagick not available, using GD for basic enhancement");
                                                                $image = imagecreatefromstring(file_get_contents($tempImagePath));
                                                                if ($image !== false) {
                                                                    // Apply basic enhancements
                                                                    imagefilter($image, IMG_FILTER_CONTRAST, -10);
                                                                    imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);
                                                                    imagefilter($image, IMG_FILTER_GRAYSCALE);
                                                                    
                                                                    $enhancedImagePath = $tempImagePath . '_enhanced.png';
                                                                    imagepng($image, $enhancedImagePath, 9); // High quality PNG
                                                                    imagedestroy($image);
                                                                    
                                                                    if (file_exists($enhancedImagePath)) {
                                                                        $tempImagePath = $enhancedImagePath;
                                                                        LaravelLog::info("Using GD enhanced image: $enhancedImagePath");
                                                                    }
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            LaravelLog::error("Image enhancement failed: " . $e->getMessage());
                                                            // Continue with original image
                                                        }
                                                        
                                                        
                                                        
                                                        
                                                        // First try with OCR Engine 2 (better for receipts and complex text)
                                                        $ch = curl_init();
                                                        curl_setopt_array($ch, [
                                                            CURLOPT_URL => 'https://api.ocr.space/parse/image',
                                                            CURLOPT_RETURNTRANSFER => true,
                                                            CURLOPT_HTTPHEADER => ['apikey: ' . $apiKey],
                                                            CURLOPT_POST => true,
                                                            CURLOPT_POSTFIELDS => [
                                                                'file' => new \CURLFile($tempImagePath),
                                                                'language' => 'eng',  // Changed from eng+ben to just eng as it was causing issues
                                                                'isOverlayRequired' => 'false',
                                                                'detectOrientation' => 'true',
                                                                'scale' => 'true',
                                                                'OCREngine' => '2',
                                                                'isTable' => 'true',
                                                                'filetype' => 'Auto',
                                                                'detectCheckbox' => 'false',
                                                                'isCreateSearchablePdf' => 'false',
                                                                'isSearchablePdfHideTextLayer' => 'false'
                                                            ],
                                                        ]);
                                                        
                                                        // Add detailed logging
                                                        LaravelLog::info('Sending request to OCR.space API...');
                                                        $result = curl_exec($ch);
                                                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                        $error = curl_error($ch);
                                                        curl_close($ch);
                                                        
                                                        if ($error) {
                                                            LaravelLog::error("CURL Error: " . $error);
                                                            throw new \Exception("OCR API request failed: " . $error);
                                                        }
                                                        
                                                        // Log the raw OCR response for debugging
                                                        LaravelLog::info("OCR API Raw Response: " . $result);
                                                        
                                                        
                                                        
                                                        
                                                        if ($httpCode === 200) {
                                                            $ocrResult = json_decode($result, true);
                                                            LaravelLog::info("OCR.space API Response: " . json_encode($ocrResult));
                                                            
                                                            if (isset($ocrResult['ParsedResults'][0]['ParsedText'])) {
                                                                $extractedText = $ocrResult['ParsedResults'][0]['ParsedText'];
                                                                LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Initialize the gateway_name variable with a default value
                                                                $gateway_name = $deposit->gateway->name ?? '';
                                                                
                                                                if(strtolower($gateway_name)=="bkash"){
                                                                    // Enhanced bKash transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // CDR/CD/CDP patterns with more flexible length
                                                                        '/\b(?:CDR|CD|CDP)[0-9A-Z]{6,12}\b/i',
                                                                        // Generic bKash transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found bKash Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns
                                                                    if (preg_match('/(?:Account|Number)\s*:?\s*(01\d{9})/', $extractedText, $matches) ||
                                                                        preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number: " . $phone_number_by_telegram_message);
                                                                    }
                                                                } elseif(strtolower($gateway_name)=="nagad"){
                                                                    LaravelLog::info("Processing Nagad payment with text: " . $extractedText);
                                                                    
                                                                    // Enhanced Nagad transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // 73 prefix pattern with flexible length
                                                                        '/\b(?:73|NAGAD)[0-9A-Z]{5,10}\b/i',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                        // Generic Nagad transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{5,10})\b/i',
                                                                        // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{5,10})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Nagad Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns - handle both formats
                                                                    if (preg_match('/(\d{5})-(\d{6})/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                        LaravelLog::info("Found phone number with dash format: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/\b(01\d{3})(\d{6})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1] . $matches[2];
                                                                        LaravelLog::info("Found phone number without dash: " . $phone_number_by_telegram_message);
                                                                    }
                                                                }
                                                                elseif(strtolower($gateway_name)=="rocket"){
                                                                    LaravelLog::info("Processing Rocket payment with text: " . $extractedText);
                                                                    
                                                                    // Enhanced Rocket transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // 5359/5358 prefix pattern
                                                                        '/\b(?:5359|5358)[0-9]{6,8}\b/',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9]{10,12})\b/i',
                                                                        // Generic Rocket transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9]{10,12})\b/i',
                                                                        // Fallback pattern for any numeric sequence that looks like a transaction ID
                                                                        '/\b(?:[0-9]{10,12})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Rocket Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Amount patterns (handle both Bengali and English numerals)
                                                                    LaravelLog::info("Trying to extract amount from text: " . $extractedText);
                                                                    
                                                                    // Try each pattern separately and log results
                                                                    if (preg_match('/t(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using t pattern: " . $amount);
                                                                    } 
                                                                    elseif (preg_match('/(?:??????|Amount)\s*:?\s*(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using amount label pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\s*(?:????|Tk|BDT|?)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using currency pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/DIST\s*t?(\d+(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using DIST pattern: " . $amount);
                                                                    }
                                                                    elseif (preg_match('/\b(\d+(?:\.\d{2})?)\b/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found amount using generic number pattern: " . $amount);
                                                                    }

                                                                    // Phone number patterns for Rocket
                                                                    if (preg_match('/Cash-out\s*\((\d{11})\)/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number from Cash-out: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/(?:Agent|Rocket)\s*A\/C\s*No\.?\s*:?\s*(\d{11})/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number from A/C No: " . $phone_number_by_telegram_message);
                                                                    }
                                                                    elseif (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number generic format: " . $phone_number_by_telegram_message);
                                                                    }
                                                                }
                                                                
                                                                // If no specific provider pattern matched, try generic patterns as fallback
                                                                if (!isset($txnId) || empty($txnId)) {
                                                                    $genericPatterns = [
                                                                        // Generic transaction ID patterns
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Look for any sequence that might be a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/',
                                                                        '/\b(?:[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($genericPatterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Transaction ID using generic pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }
                                                                }

                                                                // Validate the found transaction ID
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    // Remove any non-alphanumeric characters
                                                                    $txnId = preg_replace('/[^A-Z0-9]/i', '', $txnId);
                                                                    
                                                                    // Log the final transaction ID
                                                                    LaravelLog::info("Final Transaction ID after validation: " . $txnId);
                                                                }

                                                                // Generic amount pattern as fallback
                                                                if (!isset($amount) || empty($amount)) {
                                                                    if (preg_match('/\b(\d{2,4}(?:\.\d{2})?)\s*(?:Tk|BDT)?\b/', $extractedText, $matches)) {
                                                                        $amount = $matches[1];
                                                                    }
                                                                }

                                                                // Generic phone number pattern as fallback
                                                                if (!isset($phone_number_by_telegram_message) || empty($phone_number_by_telegram_message)) {
                                                                    if (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                    }
                                                                }
                                                                
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Format the message with extracted information
                                                                $message = "?? *Extracted Information:*\n\n";
                                                                
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                    LaravelLog::info("Adding transaction ID to message: " . $txnId);
                                                                }
                                                                
                                                                if (isset($amount) && !empty($amount)) {
                                                                    $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                    LaravelLog::info("Adding amount to message: " . $amount);
                                                                }
                                                                
                                                                if (isset($phone_number_by_telegram_message) && !empty($phone_number_by_telegram_message)) {
                                                                    $message .= "?? *Phone:* `" . $phone_number_by_telegram_message . "`\n";
                                                                    LaravelLog::info("Adding phone to message: " . $phone_number_by_telegram_message);
                                                                }
                                                                
                                                                // Add the full extracted text at the bottom
                                                                $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                
                                                                
                                                                LaravelLog::info("Final message being sent 9: " . $message);
                                                                
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = "No text found in the image";
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                        } else {
                                                            LaravelLog::error("OCR.space API Error: " . $result);
                                                            $message = "OCR.space API Error";
                                                            $response = Http::post($url, [
                                                                'chat_id' => $sender_chat['id'],
                                                                'text' => $message,
                                                                'reply_to_message_id' => $TG_message['message_id'],
                                                                'parse_mode' => 'Markdown',
                                                            ]);
                                                            
                                                            $image_processed=1;
                                                        }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                            
                                            $message = 'catch';
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                            
                                            return response()->json(['status' => 'success'], 200);
                                        }
                                        
                                        if($image_processed==0){
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => 'Error',
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                        }
                                        
                            }else{
                                $message = 'Attach Image';
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                    
                                    return response()->json(['status' => 'success'], 200);
                            }    
                    
                        
                    }
                    elseif(strpos($lowercaseText, "/newocr") === 0){
                        
                        
                        
                        
                        $parts = explode(" ", $sender_message);
                        $extractedText = '';
                        
                            $gateway_name = "";
                        if(count($parts) >= 2) {
                            $gateway_name = trim($parts[1]);
                        }
                        
                        
                        
                        
                        
                        
                        if (isset($TG_message['photo'])) {
                                        $image_processed = 0;
                                        
                                        try {
                                            $botToken = "7437302099:AAFdYOPOqw4t-1LHDWbmUb3zgrLkEkY6Gr4";
                                            $photo = end($TG_message['photo']);
                                            $file_id = $photo['file_id'];
                                            LaravelLog::info("Got file_id: $file_id");
                                    
                                            // Get file info from Telegram
                                            $getFileUrl = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$file_id}";
                                            LaravelLog::info("Requesting file info from: $getFileUrl");
                                            $fileData = Http::get($getFileUrl)->json();
                                            LaravelLog::info("File info response: " . json_encode($fileData));
                                            
                                            
                                    
                                            if (isset($fileData['ok']) && $fileData['ok'] === true) {
                                                $file_path = $fileData['result']['file_path'];
                                                $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$file_path}";
                                                LaravelLog::info("Downloading image from: $fileUrl");
                                    
                                                // Use cURL to fetch the image data because allow_url_fopen is disabled
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $fileUrl);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                $imageContent = curl_exec($ch);
                                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);
                                                
                                                
                                                
                                    
                                                if ($imageContent && $httpCode === 200) {
                                                    
                                                    
                                                    
                                                    $tempPath = 'ocr_' . time() . '.jpg';
                                                    $tempImagePath = storage_path('app/public/ocr_images/' . $tempPath);
                                                    file_put_contents($tempImagePath, $imageContent);
                                                    $imageUrl = url('storage/app/public/ocr_images/' . $tempPath);
                                                    LaravelLog::info("Image saved temporarily at: $tempImagePath");
                                                    LaravelLog::info("Image saved temporarily at: $imageUrl");
                                                    
                                                    
                                                    
                                                    $ocrtext = "";

                                                            $response = Http::withHeaders([
                                                                'Content-Type' => 'application/json',
                                                            ])->post('http://89.46.62.251/ocr/api/applyocr', [
                                                                'imageurl' => $imageUrl,
                                                            ]);
                                                            
                                                            
                                                            
                                                            LaravelLog::info("OCR API Raw Response: " . $response);
                                                            
                                                            
                                                            
                                                            if ($response->successful()) {
                                                                $ocr_response = $response->json();
                                                                
                                                                if(isset($ocr_response['ocr_text'])){
                                                                $ocrtext = $ocr_response['ocr_text'];  
                                                                }

                                                            } else {
                                                                
                                                                $message = 'Unexpected error occurred.';
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                            }
                                                            
                                                            LaravelLog::info("OCR API Raw Response: " . $ocrtext);

                                    
                                                    try {
                                                            
                                            
                                                        
                                                        
                                                        $extractedText = $ocrtext;
                                                            if (isset($extractedText)) {
                                                                
                                                                LaravelLog::info("Successfully extracted text from image: " . $extractedText);
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Initialize the gateway_name variable with a default value
                                                                $gateway_name = $deposit->gateway->name ?? '';
                                                                
                                                                if(strtolower($gateway_name)=="bkash"){
                                                                    // Enhanced bKash transaction ID patterns
                                                                    $txnId = null;
                                                                    $patterns = [
                                                                        // CDR/CD/CDP patterns with more flexible length
                                                                        '/\b(?:CDR|CD|CDP)[0-9A-Z]{6,12}\b/i',
                                                                        // Generic bKash transaction pattern
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Transaction ID with ID label
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Fallback pattern for any alphanumeric sequence that looks like a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found bKash Transaction ID using pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Phone number patterns
                                                                    if (preg_match('/(?:Account|Number)\s*:?\s*(01\d{9})/', $extractedText, $matches) ||
                                                                        preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                        LaravelLog::info("Found phone number: " . $phone_number_by_telegram_message);
                                                                    }

                                                                    // bKash specific amount patterns
                                                                    $amount = null;
                                                                    if (preg_match('/Amount\s*:?\s*৳\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found bKash amount with ৳ symbol: " . $amount);
                                                                    } elseif (preg_match('/Amount\s*:?\s*Tk\.?\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found bKash amount with Tk: " . $amount);
                                                                    } elseif (preg_match('/Cash\s+Out\s+৳\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Found bKash Cash Out amount: " . $amount);
                                                                    }

                                                                } elseif(strtolower($gateway_name)=="nagad"){
                                                                    LaravelLog::info("Processing Nagad payment with text: " . $extractedText);
                                                                    
                                                                    // First extract e-wallet number (it's the most reliable identifier)
                                                                    $ewallet_number = null;
                                                                    if (preg_match('/01[0-9]{3}-[0-9]{6}/', $extractedText, $matches)) {
                                                                        $ewallet_number = $matches[0];
                                                                        LaravelLog::info("Found Nagad e-wallet number: " . $ewallet_number);
                                                                    }

                                                                    // Extract transaction ID from Bengali text
                                                                    $txnId = null;
                                                                    if (preg_match('/ট্রানজেকশন\s*আইডি\s*([A-Z0-9]+)/u', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Found transaction ID from Bengali text: " . $txnId);
                                                                    } elseif (preg_match('/[A-Z0-9]{8}/', $extractedText, $matches)) {
                                                                        $txnId = $matches[0];
                                                                        LaravelLog::info("Found transaction ID from alphanumeric: " . $txnId);
                                                                    }

                                                                    // Extract amount from Nagad receipt with multiple patterns
                                                                    // First try the standard format: "পরিমাণ X,XXX.XX টাকা"
                                                                    if (preg_match('/পরিমাণ\s+(\d+(?:,\d{3})*(?:\.\d{2})?)\s*টাকা/u', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Extracted Nagad amount from পরিমাণ: " . $amount);
                                                                    } 
                                                                    // Try alternative format without space: "পরিমাণ X,XXX.XXটাকা"
                                                                    else if (preg_match('/পরিমাণ\s+(\d+(?:,\d{3})*(?:\.\d{2})?)টাকা/u', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Extracted Nagad amount from পরিমাণ (no space): " . $amount);
                                                                    }
                                                                    // Try simple format: "X,XXX.XX টাকা"
                                                                    else if (preg_match('/(\d+(?:,\d{3})*(?:\.\d{2})?)\s*টাকা/u', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Extracted Nagad amount from simple format: " . $amount);
                                                                    }
                                                                    
                                                                    // Validate the amount to ensure it's not "00" or too small
                                                                    if (isset($amount) && ($amount == "00" || floatval($amount) < 1)) {
                                                                        LaravelLog::info("Invalid amount detected: " . $amount . ", trying alternative patterns");
                                                                        // Try to find amount in the full text with a more general pattern
                                                                        if (preg_match('/(\d{2,3}(?:,\d{3})*(?:\.\d{2})?)\s*টাকা/u', $extractedText, $matches)) {
                                                                            $amount = str_replace(',', '', $matches[1]);
                                                                            LaravelLog::info("Found valid amount with alternative pattern: " . $amount);
                                                                        }
                                                                    }

                                                                    // Format the message
                                                                    $message = "?? *Extracted Information:*\n\n";
                                                                    
                                                                    if ($txnId) {
                                                                        $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                    }
                                                                    
                                                                    if ($ewallet_number) {
                                                                        $message .= "?? *E-Wallet:* `" . $ewallet_number . "`\n";
                                                                    }
                                                                    
                                                                    if ($amount) {
                                                                        $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                    }
                                                                    
                                                                    $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                    
                                                                    LaravelLog::info("Final message being sent: " . $message);
                                                                    
                                                                    $response = Http::post($url, [
                                                                        'chat_id' => $sender_chat['id'],
                                                                        'text' => $message,
                                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                                        'parse_mode' => 'Markdown',
                                                                    ]);
                                                                    
                                                                    return response()->json(['status' => 'success'], 200);

                                                                } elseif(strtolower($gateway_name)=="rocket"){
                                                                    LaravelLog::info("Processing Rocket payment with text: " . $extractedText);
                                                                    
                                                                    // Exactly match what we need with very simple patterns
                                                                    $txnId = null;
                                                                    $agent_account = null;
                                                                    $cashout_account = null;
                                                                    $amount = null;
                                                                    
                                                                    // Case 1: Look for "Transaction ID" followed by 10 digits - more precise pattern
                                                                    if (preg_match('/Transaction\s+ID\s*:?\s*(\d{10})/', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Found Transaction ID: " . $txnId);
                                                                    } 
                                                                    
                                                                    // Case 2: Look for "ID: #" followed by digits
                                                                    if (!$txnId && preg_match('/ID:\s*#?(\d+)/', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Found ID with # format: " . $txnId);
                                                                    }
                                                                    
                                                                    // Look for Transaction Amount pattern
                                                                    if (preg_match('/Transaction\s+Amount\s*:?\s*(\d+\.\d+)/', $extractedText, $matches)) {
                                                                        $amount = $matches[1];
                                                                        LaravelLog::info("Found Transaction Amount: " . $amount);
                                                                    }
                                                                    
                                                                    // Look for amount after ID pattern (with Bengali symbol)
                                                                    if (!$amount && preg_match('/ID:\s*#?\d+\s*৳?(\d+\.\d+)/', $extractedText, $matches)) {
                                                                        $amount = $matches[1];
                                                                        LaravelLog::info("Found Amount with ৳ symbol: " . $amount);
                                                                    }
                                                                    
                                                                    // Look for Agent A/C pattern - more precise pattern
                                                                    if (preg_match('/Agent\s+A\/C\s+No\.\s*:?\s*(\d+)/', $extractedText, $matches)) {
                                                                        $agent_account = $matches[1];
                                                                        LaravelLog::info("Found Agent A/C: " . $agent_account);
                                                                    }
                                                                    
                                                                    // Look for Cash-out pattern with parentheses
                                                                    if (preg_match('/Cash-out\s*\((\d+)\)/', $extractedText, $matches)) {
                                                                        $cashout_account = $matches[1];
                                                                        LaravelLog::info("Found Cash-out with parentheses: " . $cashout_account);
                                                                    }
                                                                    
                                                                    // Look for Cash-out pattern without parentheses (for Mini Statement)
                                                                    if (!$cashout_account && preg_match('/Cash-out\s+(\d+)/', $extractedText, $matches)) {
                                                                        $cashout_account = $matches[1];
                                                                        LaravelLog::info("Found Cash-out without parentheses: " . $cashout_account);
                                                                    }
                                                                    
                                                                    // Create the message exactly as requested
                                                                    $message = "?? *Extracted Information:*\n\n";
                                                                    
                                                                    // Always include Transaction ID if found
                                                                    if ($txnId) {
                                                                        $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                    } else {
                                                                        // Try harder to find Transaction ID in Mini Statement format
                                                                        if (strpos($extractedText, "Mini Statement") !== false && preg_match('/ID:\s*#?(\d+)/', $extractedText, $matches)) {
                                                                            $txnId = $matches[1];
                                                                            $message .= "?? *ID:* `" . $txnId . "`\n";
                                                                            LaravelLog::info("Found ID from Mini Statement: " . $txnId);
                                                                        }
                                                                    }
                                                                    
                                                                    // Always include Amount if found
                                                                    if ($amount) {
                                                                        if (strpos($extractedText, '৳') !== false) {
                                                                            $message .= "?? *Amount:* `৳" . $amount . "`\n";
                                                                        } else {
                                                                            $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                        }
                                                                    }
                                                                    
                                                                    // Always include Agent A/C if found
                                                                    if ($agent_account) {
                                                                        $message .= "?? *Agent A/C:* `" . $agent_account . "`\n";
                                                                    }
                                                                    
                                                                    // Always include Cash-out if found
                                                                    if ($cashout_account) {
                                                                        $message .= "?? *Cash-out:* `" . $cashout_account . "`\n";
                                                                    }
                                                                    
                                                                    $message .= "\n?? *Full Text:*\n```\n" . $extractedText . "```\n";
                                                                    
                                                                    LaravelLog::info("Final message being sent: " . $message);
                                                                    
                                                                    $response = Http::post($url, [
                                                                        'chat_id' => $sender_chat['id'],
                                                                        'text' => $message,
                                                                        'reply_to_message_id' => $TG_message['message_id'],
                                                                        'parse_mode' => 'Markdown',
                                                                    ]);
                                                                    
                                                                    return response()->json(['status' => 'success'], 200);
                                                                }
                                                                
                                                                // Handle Rocket Mini Statement specially
                                                                if (strpos($extractedText, "Mini Statement") !== false) {
                                                                    // For Mini Statement format, extract ID and amount in special way
                                                                    if (preg_match('/ID:[\s#]*(\d+)[\s]*৳?(\d+(?:\.\d+)?)/', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        $amount = $matches[2];
                                                                        LaravelLog::info("Mini Statement format found - ID: " . $txnId . ", Amount: " . $amount);
                                                                    }
                                                                    
                                                                    // Look for Cash-out pattern in Mini Statement
                                                                    if (preg_match('/Cash-out[\s\(]*(\d+)[\)\s]*/', $extractedText, $matches)) {
                                                                        $cashout_account = $matches[1];
                                                                        LaravelLog::info("Mini Statement - Found Cash-out: " . $cashout_account);
                                                                    }
                                                                }
                                                                
                                                                // If no specific provider pattern matched, try generic patterns as fallback
                                                                if (!isset($txnId) || empty($txnId)) {
                                                                    $genericPatterns = [
                                                                        // Generic transaction ID patterns
                                                                        '/\b(?:TRX|TXN|TRANS)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        '/\b(?:ID|Transaction ID)[\s#:]*([0-9A-Z]{6,12})\b/i',
                                                                        // Look for any sequence that might be a transaction ID
                                                                        '/\b(?:[A-Z]{2,4}[0-9A-Z]{6,12})\b/',
                                                                        '/\b(?:[0-9A-Z]{6,12})\b/'
                                                                    ];

                                                                    foreach ($genericPatterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            $txnId = $matches[0];
                                                                            LaravelLog::info("Found Transaction ID using generic pattern: " . $pattern . " - ID: " . $txnId);
                                                                            break;
                                                                        }
                                                                    }
                                                                }

                                                                // Validate the found transaction ID
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    // Remove any non-alphanumeric characters
                                                                    $txnId = preg_replace('/[^A-Z0-9]/i', '', $txnId);
                                                                    
                                                                    // Log the final transaction ID
                                                                    LaravelLog::info("Final Transaction ID after validation: " . $txnId);
                                                                }

                                                                // For Rocket, use a simpler and more direct approach to get Transaction ID
                                                                // Remove any previous Transaction ID extraction that might be causing issues
                                                                $txnId = null;
                                                                $cashout_number = null;
                                                                $phone_number = null;
                                                                
                                                                // Check for different receipt formats and extract Transaction ID accordingly
                                                                if (stripos($extractedText, "Mini Statement") !== false) {
                                                                    // Mini Statement format - Extract ID from format "ID: #number"
                                                                    if (preg_match('/ID:\s*#?(\d+)/i', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Extracted ID from Mini Statement: " . $txnId);
                                                                    }
                                                                    
                                                                    // Extract Cash-out number from Mini Statement
                                                                    if (preg_match('/Cash-out\s*\((\d+)\)/i', $extractedText, $matches)) {
                                                                        $cashout_number = $matches[1];
                                                                        LaravelLog::info("Extracted Cash-out number from Mini Statement: " . $cashout_number);
                                                                    }
                                                                    
                                                                    // Extract amount from Mini Statement with proper handling of Bengali currency symbol
                                                                    if (preg_match('/ID:\s*#?\d+\s*-?\s*(?:৳|6)([0-9.,]+)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Extracted amount from Mini Statement with Bengali symbol: " . $amount);
                                                                    }
                                                                } else if (preg_match('/TxnId:(\d+)/i', $extractedText, $matches)) {
                                                                    // Format with TxnId: pattern (typically found in the Cash-Out receipts)
                                                                    $txnId = $matches[1];
                                                                    LaravelLog::info("Extracted TxnId from Cash-Out receipt: " . $txnId);
                                                                } else if (stripos($extractedText, "ক্যাশ আউট") !== false) {
                                                                    // Nagad case - Extract transaction ID (alphanumeric code)
                                                                    // First try to find it after "ট্রানজেকশন আইডি"
                                                                    if (preg_match('/ট্রানজেকশন\s+আইডি\s*([A-Z0-9]{7,8})/u', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Extracted Nagad transaction ID from Bengali text: " . $txnId);
                                                                    }
                                                                    // If not found, try general pattern
                                                                    else if (preg_match('/\b([A-Z0-9]{7,8})\b/', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Extracted Nagad transaction ID from general pattern: " . $txnId);
                                                                    }
                                                                    
                                                                    // Extract phone number from Nagad receipt
                                                                    // First try to find it directly in the format 01336-736805
                                                                    if (preg_match('/\b(01\d{3}-\d{6})\b/', $extractedText, $matches)) {
                                                                        $phone_number = $matches[1];
                                                                        LaravelLog::info("Extracted Nagad phone number from direct format: " . $phone_number);
                                                                    }
                                                                    // Then try Bengali text format
                                                                    else if (preg_match('/একাউন্ট\s+নং\s*(\d{3}-\d{6})/u', $extractedText, $matches) || 
                                                                            preg_match('/একাউন্ট\s+নং\s*(\d{9})/u', $extractedText, $matches)) {
                                                                        $phone_number = $matches[1];
                                                                        LaravelLog::info("Extracted Nagad phone number from Bengali text: " . $phone_number);
                                                                    }
                                                                } else {
                                                                    // Regular receipt format - Direct pattern to match Transaction ID
                                                                    if (preg_match('/Transaction\s+ID\s+(\d+)/i', $extractedText, $matches)) {
                                                                        $txnId = $matches[1];
                                                                        LaravelLog::info("Extracted Transaction ID directly: " . $txnId);
                                                                    }
                                                                }
                                                                
                                                                // For Rocket, check specifically for Agent A/C No
                                                                if (preg_match('/Agent\s+A\/C\s+No\.\s*:?\s*(\d+)/', $extractedText, $matches) || 
                                                                    preg_match('/Agent\s+A\/C\s+No\.\s+(\d+)/', $extractedText, $matches)) {
                                                                    $agent_account = $matches[1];
                                                                    LaravelLog::info("Found Agent A/C No from Rocket format: " . $agent_account);
                                                                }

                                                                // Add specific Rocket transaction ID pattern
                                                                if (preg_match('/Transaction\s+ID\s+(\d{10})/', $extractedText, $matches)) {
                                                                    $txnId = $matches[1];
                                                                    LaravelLog::info("Found Rocket Transaction ID: " . $txnId);
                                                                }
                                                                // Add pattern for Txnld: format
                                                                else if (preg_match('/Txnld:(\d{10})/', $extractedText, $matches)) {
                                                                    $txnId = $matches[1];
                                                                    LaravelLog::info("Found Rocket Transaction ID from Txnld format: " . $txnId);
                                                                }
                                                                // Add pattern for 1D: format (OCR misreading)
                                                                else if (preg_match('/1D:\s*#?(\d{10})/', $extractedText, $matches)) {
                                                                    $txnId = $matches[1];
                                                                    LaravelLog::info("Found Rocket Transaction ID from 1D format: " . $txnId);
                                                                }

                                                                // Extract cash-out number
                                                                if (preg_match('/Cash-out\s*\((\d+)\)/', $extractedText, $matches)) {
                                                                    $cashout_number = $matches[1];
                                                                    LaravelLog::info("Found Cash-out number: " . $cashout_number);
                                                                }

                                                                // Generic phone number pattern as fallback
                                                                if (!isset($phone_number_by_telegram_message) || empty($phone_number_by_telegram_message)) {
                                                                    if (preg_match('/\b(01[3-9]\d{8})\b/', $extractedText, $matches)) {
                                                                        $phone_number_by_telegram_message = $matches[1];
                                                                    }
                                                                }
                                                                
                                                                
                                                                /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                // Extract amount from text if not already found
                                                                if (!isset($amount) || empty($amount)) {
                                                                    // Check for specific Rocket pattern with ID and misinterpreted Bengali symbol
                                                                    if (preg_match('/ID:\s*#?\d+\s+6([0-9.,]+)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Fixed Rocket receipt with ৳ misread as 6: " . $amount);
                                                                    }
                                                                    
                                                                    // Try to find amount in common formats
                                                                    $amountPatterns = [
                                                                        // Add pattern for ID: #number - ৳amount format
                                                                        '/ID:\s*#?\d+\s*-\s*৳(\d+(?:,\d{3})*(?:\.\d{2})?)/',
                                                                        // Add pattern for ¥ symbol
                                                                        '/¥(\d+(?:\.\d{2})?)/',
                                                                        // Add pattern for Bengali numerals
                                                                        '/[০-৯]+(?:\.[০-৯]{2})?/u',
                                                                        // Add specific pattern for TkK format
                                                                        '/TkK(\d+(?:\.\d{2})?)/',
                                                                        // TxnAmount format specific to Rocket
                                                                        '/TxnAmount\s*:?\s*(?:Tk\.?|৳)?\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // Cash-out with amount in parentheses format
                                                                        '/Cash-out.*?(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // Special case for OCR misreading Bengali currency symbol as "6"
                                                                        '/ID:\s*#?\d+\s+6(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // ID: #number amount format (where amount follows the ID)
                                                                        '/ID:\s*#?\d+\s+(?:৳)?\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // Amount with comma and Tk/টাকা
                                                                        '/(\d+(?:,\d{3})*(?:\.\d{2})?)\s*(?:Tk\.?|টাকা)/i',
                                                                        // Amount with ৳ symbol
                                                                        '/৳\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // Amount after "Amount" word
                                                                        '/Amount\s*:?\s*(\d+(?:,\d{3})*(?:\.\d{2})?)/i',
                                                                        // Amount after "Cash Out" or "ক্যাশ আউট"
                                                                        '/(?:Cash Out|ক্যাশ আউট).*?(\d+(?:,\d{3})*(?:\.\d{2})?)/s',
                                                                        // Simple digits with commas that might be amount
                                                                        '/\b(\d{1,3}(?:,\d{3})+(?:\.\d{2})?)\b/',
                                                                        // Special case for Rocket receipt with "Tk" followed by amount
                                                                        '/Tk\.?(\d+(?:,\d{3})*(?:\.\d{2})?)/i'
                                                                    ];

                                                                    foreach ($amountPatterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            // Skip if the match is part of a cash-out number
                                                                            if (strpos($matches[0], 'Cash-out') !== false) {
                                                                                continue;
                                                                            }
                                                                            $amount = $matches[0];
                                                                            // Convert Bengali numerals to English if needed
                                                                            if (preg_match('/[০-৯]/u', $amount)) {
                                                                                $amount = str_replace(
                                                                                    ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'],
                                                                                    ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                                                                                    $amount
                                                                                );
                                                                            }
                                                                            // Remove currency symbols and clean up
                                                                            $amount = preg_replace('/[¥৳Tk]/', '', $amount);
                                                                            $amount = str_replace(',', '', $amount);
                                                                            
                                                                            // Validate amount is reasonable (not too large)
                                                                            if (floatval($amount) > 100000) {
                                                                                continue; // Skip if amount seems too large
                                                                            }
                                                                            
                                                                            LaravelLog::info("Found amount using pattern: " . $pattern . " - Amount: " . $amount);
                                                                            break;
                                                                        }
                                                                    }
                                                                    
                                                                                                                                        // Validate extracted amount to ensure it's not a phone number
                                                                if (isset($amount) && !empty($amount)) {
                                                                    // If the amount looks like a phone number (01XXXXXXXX format), it's likely incorrect
                                                                    if (preg_match('/^01\d{8,9}$/', $amount) || strlen($amount) >= 9 && strlen($amount) <= 11) {
                                                                        LaravelLog::info("Possible phone number detected as amount, discarding: " . $amount);
                                                                        $amount = null;
                                                                        
                                                                        // Try to find amount by checking specific patterns in the Mini Statement
                                                                        if (strpos($extractedText, "Mini Statement") !== false) {
                                                                            if (preg_match('/ID:\s*#?\d+\s+6([0-9.,]+)/', $extractedText, $matches)) {
                                                                                $amount = str_replace(',', '', $matches[1]);
                                                                                LaravelLog::info("Fixed amount from Mini Statement with ID: " . $amount);
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                
                                                                // Additional check for Mini Statement case with misread Bengali currency symbol
                                                                if ((!isset($amount) || empty($amount)) && strpos($extractedText, "Mini Statement") !== false) {
                                                                    if (preg_match('/ID:\s*#?\d+\s+6([0-9.,]+)/', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        LaravelLog::info("Fixed amount from Mini Statement as fallback: " . $amount);
                                                                    }
                                                                }
                                                                }
                                                                
                                                                // Format the message with extracted information
                                                                $message = "?? *Extracted Information:*\n\n";
                                                                
                                                                // Add Transaction ID or ID from Mini Statement to message if found
                                                                if (isset($txnId) && !empty($txnId)) {
                                                                    // Check the format of the receipt to use the appropriate label
                                                                    if (stripos($extractedText, "Mini Statement") !== false) {
                                                                        // For Rocket Mini Statement, extract ID from the format "ID: #number"
                                                                        if (preg_match('/ID:\s*#?(\d+)/', $extractedText, $matches)) {
                                                                            $txnId = $matches[1];
                                                                            $message .= "?? *ID:* `" . $txnId . "`\n";
                                                                            LaravelLog::info("Adding ID from Mini Statement to message: " . $txnId);
                                                                        }
                                                                    } else if (stripos($extractedText, "TxnId:") !== false) {
                                                                        $message .= "?? *TxnId:* `" . $txnId . "`\n";
                                                                        LaravelLog::info("Adding TxnId from Cash-Out receipt to message: " . $txnId);
                                                                    } else if (stripos($extractedText, "ক্যাশ আউট") !== false) {
                                                                        $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                        LaravelLog::info("Adding Nagad transaction ID to message: " . $txnId);
                                                                    } else {
                                                                        $message .= "?? *Transaction ID:* `" . $txnId . "`\n";
                                                                        LaravelLog::info("Adding transaction ID to message: " . $txnId);
                                                                    }
                                                                }
                                                                
                                                                // Add Agent A/C to message if found (Rocket specific)
                                                                if (isset($agent_account) && !empty($agent_account)) {
                                                                    $message .= "?? *Agent A/C:* `" . $agent_account . "`\n";
                                                                    LaravelLog::info("Adding Agent A/C to message: " . $agent_account);
                                                                }
                                                                
                                                                // Add Cash-out number to message if found (from Mini Statement)
                                                                if (isset($cashout_number) && !empty($cashout_number)) {
                                                                    $message .= "?? *Cash-out:* `" . $cashout_number . "`\n";
                                                                    LaravelLog::info("Adding Cash-out number to message: " . $cashout_number);
                                                                }
                                                                
                                                                // Add phone number to message if found (from Nagad receipt)
                                                                if (isset($phone_number) && !empty($phone_number)) {
                                                                    $message .= "?? *Phone:* `" . $phone_number . "`\n";
                                                                    LaravelLog::info("Adding phone number to message: " . $phone_number);
                                                                }

                                                                // Skip Rocket A/C as requested
                                                                // We'll just check for other e-wallet patterns
                                                                {
                                                                    // We already skipped Rocket A/C, now look for other patterns
                                                                    // Ensure we have the Transaction ID for Rocket receipts by using a more precise pattern
                                                                    if (!isset($txnId) || empty($txnId) || $txnId == "Transaction") {
                                                                        if (preg_match('/Transaction\s+Information:[\s\S]*?Transaction\s+ID\s+(\d+)/i', $extractedText, $matches)) {
                                                                            $txnId = $matches[1];
                                                                            LaravelLog::info("Found Transaction ID from Transaction Information section: " . $txnId);
                                                                        }
                                                                    }
                                                                    
                                                                    $ewallet_patterns = [
                                                                        '/\b(01\d{3})-(\d{6})\b/',  // Pattern with hyphen
                                                                        '/\b(01\d{3})(\d{6})\b/',   // Pattern without hyphen
                                                                        '/ক্যাশ আউট\s*(01\d{3})-?(\d{6})\b/u',  // Pattern after ক্যাশ আউট
                                                                        '/\b(01\d{9})\b/'  // Continuous 11 digit pattern
                                                                    ];
                                                                    
                                                                    foreach ($ewallet_patterns as $pattern) {
                                                                        if (preg_match($pattern, $extractedText, $matches)) {
                                                                            if (count($matches) === 2) {
                                                                                // For continuous number pattern
                                                                                $ewallet_number = substr($matches[1], 0, 5) . '-' . substr($matches[1], 5);
                                                                            } else if (count($matches) === 3) {
                                                                                // For split patterns
                                                                                $ewallet_number = $matches[1] . '-' . $matches[2];
                                                                            }
                                                                            // Only add E-Wallet if it's different from the phone number
                                                                            if (!isset($phone_number) || $ewallet_number !== $phone_number) {
                                                                                $message .= "?? *E-Wallet:* `" . $ewallet_number . "`\n";
                                                                                LaravelLog::info("Adding e-wallet number: " . $ewallet_number);
                                                                            }
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                                
                                                                // No longer needed here as we handle Transaction ID earlier in the code
                                                                // and add it to the message at the beginning of the message formatting section

                                                                // Check if the amount might be a phone number (to avoid confusion with Cash-out numbers)
                                                                if (isset($amount) && !empty($amount)) {
                                                                    // Verify this isn't actually a phone number
                                                                    if (preg_match('/^01\d{8,9}$/', $amount) || strlen($amount) > 8) {
                                                                        LaravelLog::info("Detected phone number as amount, discarding: " . $amount);
                                                                        
                                                                        // Try again to find the correct amount in Mini Statement
                                                                        if (stripos($extractedText, "Mini Statement") !== false) {
                                                                            if (preg_match('/ID:\s*#?\d+\s*-?\s*(?:৳|6)([0-9.,]+)/i', $extractedText, $matches)) {
                                                                                $amount = str_replace(',', '', $matches[1]);
                                                                                LaravelLog::info("Corrected amount from Mini Statement: " . $amount);
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                // Add amount to message if found
                                                                if (isset($amount) && !empty($amount)) {
                                                                    // Special handling for Rocket Mini Statement
                                                                    if (stripos($extractedText, "Mini Statement") !== false) {
                                                                        // Simply remove first 6 if amount starts with it
                                                                        if (substr($amount, 0, 1) === '6') {
                                                                            $amount = substr($amount, 1);
                                                                        }
                                                                        $message .= "?? *Amount:* `৳" . $amount . "`\n";
                                                                        LaravelLog::info("Rocket Mini Statement amount: ৳" . $amount);
                                                                    } else {
                                                                        $message .= "?? *Amount:* `" . $amount . "`\n";
                                                                        LaravelLog::info("Adding amount to message: " . $amount);
                                                                    }
                                                                } else {
                                                                    // Last attempt to find amount in Mini Statement pattern
                                                                    if (strpos($extractedText, "Mini Statement") !== false) {
                                                                        // Try different patterns for Mini Statement
                                                                        if (preg_match('/ID:\s*#?\d+\s*-?\s*[৳6]([0-9.,]+)/i', $extractedText, $matches)) {
                                                                        $amount = str_replace(',', '', $matches[1]);
                                                                        $message .= "?? *Amount:* `৳" . $amount . "`\n";
                                                                            LaravelLog::info("Last attempt - Adding amount with Bengali symbol: ৳" . $amount);
                                                                        } else {
                                                                            LaravelLog::info("No amount found in Mini Statement text");
                                                                        }
                                                                } else {
                                                                    LaravelLog::info("No amount found in the text");
                                                                    }
                                                                }
                                                                
                                                                if (isset($phone_number_by_telegram_message) && !empty($phone_number_by_telegram_message)) {
                                                                    $message .= "?? *Phone:* `" . $phone_number_by_telegram_message . "`\n";
                                                                    LaravelLog::info("Adding phone to message: " . $phone_number_by_telegram_message);
                                                                }
                                                                
                                                                // Fix the full text to replace misread "6" with Bengali currency symbol "৳" where appropriate
                                                                $fixedText = $extractedText;
                                                                
                                                                // Common patterns where Bengali currency symbol gets misread as "6"
                                                                $replacementPatterns = [
                                                                    // ID pattern (e.g., ID: #5473918508 6300.00 -> ID: #5473918508 ৳300.00)
                                                                    '/(ID:\s*#?\d+\s+)6([0-9.,]+)/' => '$1৳$2',
                                                                    
                                                                    // Space followed by 6 and then digits (e.g., " 6300.00" -> " ৳300.00")
                                                                    '/(\s)6([0-9]{2,3}(?:\.[0-9]{2})?)/' => '$1৳$2',
                                                                    
                                                                    // Amount format in Rocket receipts
                                                                    '/(Amount\s*:?\s*)6([0-9.,]+)/' => '$1৳$2',
                                                                    
                                                                    // Transaction amount format
                                                                    '/(Transaction\s+Amount\s*:?\s*)6([0-9.,]+)/' => '$1৳$2',
                                                                    
                                                                    // General case where 6 precedes a standard amount pattern
                                                                    '/(\s)6(\d{1,3}(?:,\d{3})*(?:\.\d{2})?)/' => '$1৳$2'
                                                                ];
                                                                
                                                                // Apply all replacements
                                                                foreach ($replacementPatterns as $pattern => $replacement) {
                                                                    $fixedText = preg_replace($pattern, $replacement, $fixedText);
                                                                }
                                                                
                                                                // Add the fixed extracted text at the bottom
                                                                $message .= "\n?? *Full Text:*\n```\n" . $fixedText . "```\n";
                                                                
                                                                LaravelLog::info("Final message being sent: " . $message);
                                                                
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                
                                                                
                                                            } else {
                                                                LaravelLog::info('No text found in the image');
                                                                $message = "No text found in the image";
                                                                $response = Http::post($url, [
                                                                    'chat_id' => $sender_chat['id'],
                                                                    'text' => $message,
                                                                    'reply_to_message_id' => $TG_message['message_id'],
                                                                    'parse_mode' => 'Markdown',
                                                                ]);
                                                                
                                                                $image_processed=1;
                                            
                                                            }
                                                    } catch (\Exception $e) {
                                                        LaravelLog::error("OCR Processing Error: " . $e->getMessage());
                                                        // $message = "Image Processing Error! Try Again. Please Attach clear image and add caption /ckorder XXX123XXX  for further checking.";
                                                        // $response = Http::post($url, [
                                                        //     'chat_id' => $sender_chat['id'],
                                                        //     'text' => $message,
                                                        //     'reply_to_message_id' => $TG_message['message_id'],
                                                        //     'parse_mode' => 'Markdown',
                                                        // ]);
                                                        
                                                        // $image_processed=1;
                                                    }
                                                    
                                                    // Clean up temporary file
                                                    if (file_exists($tempImagePath)) {
                                                        // unlink($tempImagePath);
                                                        LaravelLog::info("Temporary image file cleaned up: $tempImagePath");
                                                    }
                                                } else {
                                                    LaravelLog::error("Failed to download image content. HTTP Code: $httpCode");
                                                }
                                            } else {
                                                LaravelLog::error("Failed to get file info from Telegram. Response: " . json_encode($fileData));
                                            }
                                        } catch (\Exception $e) {
                                            LaravelLog::error("Processing exception: " . $e->getMessage());
                                            
                                            $message = 'catch';
                                            $response = Http::post($url, [
                                                'chat_id' => $sender_chat['id'],
                                                'text' => $message,
                                                'reply_to_message_id' => $TG_message['message_id'],
                                                'parse_mode' => 'Markdown',
                                            ]);
                                            
                                            return response()->json(['status' => 'success'], 200);
                                        }
                                        
                                        // if($image_processed==0){
                                        //     $response = Http::post($url, [
                                        //         'chat_id' => $sender_chat['id'],
                                        //         'text' => 'naveed error',
                                        //         'reply_to_message_id' => $TG_message['message_id'],
                                        //         'parse_mode' => 'Markdown',
                                        //     ]);
                                        // }
                                        
                            }else{
                                $message = 'Attach Image';
                                    $response = Http::post($url, [
                                        'chat_id' => $sender_chat['id'],
                                        'text' => $message,
                                        'reply_to_message_id' => $TG_message['message_id'],
                                        'parse_mode' => 'Markdown',
                                    ]);
                                    
                                    return response()->json(['status' => 'success'], 200);
                            }    
                    
                        
                    }
                    
                    
                    
                    elseif(strpos($lowercaseText, "/callback") === 0){
                        $deposit = Payment::where('partner_transection_id',$sender_message)->where('api_id',$api->api_id)->with('gateway')->latest()->first();
                        if($deposit){
                            if($deposit->status==1){
                                $message = $this->messages[$api->lang]['transaction_completed_callback'];
                            }elseif($deposit->status==3){
                                $message = $this->messages[$api->lang]['transaction_rejected_callback'];
                            }else{
                                $message = $this->messages[$api->lang]['transaction_pending_callback'];
                            }

                            // 'message_id' => $telegaram_message_p->response_id,

                            $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);

                                
                                if ($api_key && !empty($api_key->api_endpoint_deposit) && $api_key->website != env('APP_WEBSITE')) {

                                    $payment = $deposit;
                                    if($payment){
                                        $string_to_hash = json_encode(array(
                                            "amount" => strval($this->convertStringToNumber($payment->amount)),
                                            "api_key" => $api_key->api_key,
                                            "e_wallet_name" => $payment->e_wallet_name,
                                            "id" => strval($payment->id),
                                            'transaction_type' => 'Deposit',
                                            "user_account_no" => strval($payment->sender),

                                        ));
                                        $secretKey = $api_key->secret_key;
                                        $hash = hash("sha256", $string_to_hash);
                                        $hmac = hash_hmac('sha256', $hash, $secretKey);
                                        $timestamp = time();
                                        $combined = $hmac . $timestamp;
                                        $sign = base64_encode($combined);


                                        $array_data = [
                                                    'id' => $payment->id,
                                                    'partner_transection_id' => $payment->partner_transection_id,
                                                    'transaction_type' => 'Deposit',
                                                    'e_wallet_name' => $payment->e_wallet_name,
                                                    'amount' => $this->convertStringToNumber($payment->amount),
                                                    'user_account_no' => $payment->sender,
                                                    'txn_id' => $payment->txn_id,
                                                    'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                                                    'e_wallet_type' => $payment->e_wallet_type,
                                                    'charges' => $this->convertStringToNumber($payment->charge),
                                                    'status' => $payment->status,
                                                    'completion_date' => Carbon::parse($payment->date_time)->toDateString(),
                                                    'completion_time' => Carbon::parse($payment->date_time)->toTimeString(),
                                                    'created_at' => $payment->created_at,
                                                    'updated_at' => $payment->updated_at,
                                                    'sign' => $sign,
                                        ];

                                        if(!empty($payment->member_id)){
                                            $array_data['member_id'] = $payment->member_id;
                                        }
                                    }
                                    

                                    $requestData = [
                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                        'request_url' => $api_key->api_endpoint_deposit,
                                        'request_payload' => json_encode($array_data),
                                        'request_headers' => json_encode([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                        ]),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];

                                    $logId = DB::table('api_logs')->insertGetId($requestData);

                                    $csrfToken = csrf_token();
                                    $responseData = [];
                                    try {
                                        $response = Http::withHeaders([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                        ])
                                            ->post($api_key->api_endpoint_deposit, $array_data);
                                        $responseData = [
                                            'response_code' => $response->status(),
                                            'response_payload' => $response->body(),
                                            'response_headers' => json_encode($response->headers()),
                                        ];

                                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                                        
                                    } catch (\Exception $e) {
                                        LaravelLog::info('Telegram Deposit Callback not sent');
                                    }
                                }

                        }else{
                            $withdrawal = Payout::where('partner_transection_id',$sender_message)->where('api_id',$api->api_id)->latest()->first();
                            if($withdrawal){
                                if($withdrawal->status=="Complete"){
                                    $message = $this->messages[$api->lang]['transaction_completed_callback'];
                                }elseif($withdrawal->status=="Reject"){
                                    $message = $this->messages[$api->lang]['transaction_rejected_callback'];
                                }else{
                                    $message = $this->messages[$api->lang]['transaction_pending_callback'];
                                }

                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);

                                
                                $payout_log = $withdrawal;

                                if (!empty($api_key->api_endpoint_withdrawal) && $api_key->website != env('APP_WEBSITE')) {

                                    $string_to_hash = json_encode(array(
                                        "amount" => strval($this->convertStringToNumber($withdrawal->amount)),
                                        "api_key" => $api_key->api_key,
                                        "e_wallet_name" => $withdrawal->e_wallet_name,
                                        "id" => strval($withdrawal->id),
                                        'transaction_type' => 'Withdrawal',
                                        "user_account_no" => strval($withdrawal->user_account_no),
                                    ));
                                    $secretKey = $api_key->secret_key;
                                    $hash = hash("sha256", $string_to_hash);
                                    $hmac = hash_hmac('sha256', $hash, $secretKey);
                                    $timestamp = time();
                                    $combined = $hmac . $timestamp;
                                    $sign = base64_encode($combined);

                                    $array_data = [
                                                'id' => $withdrawal->id,
                                                'partner_transection_id' => $withdrawal->partner_transection_id,
                                                'transaction_type' => 'Withdrawal',
                                                'e_wallet_name' => $withdrawal->e_wallet_name,
                                                'amount' => $this->convertStringToNumber($withdrawal->amount),
                                                'user_account_no' => $withdrawal->user_account_no,
                                                'txn_id' => $withdrawal->txn_id,
                                                'e_wallet_phone_number' => $withdrawal->e_wallet_phone_number,
                                                'e_wallet_type' => $withdrawal->e_wallet_type,
                                                'charges' => $this->convertStringToNumber($withdrawal->charge),
                                                'status' => $withdrawal->status,
                                                'completion_date' => Carbon::parse($withdrawal->date_time)->toDateString(),
                                                'completion_time' => Carbon::parse($withdrawal->date_time)->toTimeString(),
                                                'created_at' => $withdrawal->created_at,
                                                'updated_at' => $withdrawal->updated_at,
                                                'sign' => $sign,
                                                'remarks' => $payout_log->feedback,
                                                
                                    ];

                                    if(!empty($withdrawal->member_id)){
                                        $array_data['member_id'] = $withdrawal->member_id;
                                    }


                                    $requestData = [
                                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                                        'request_url' => $api_key->api_endpoint_withdrawal,
                                        'request_payload' => json_encode($array_data),
                                        'request_headers' => json_encode([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
                                        ]),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];

                                    $logId = DB::table('api_logs')->insertGetId($requestData);

                                    $csrfToken = csrf_token();
                                    $responseData = [];
                                    try {

                                        $response = Http::withHeaders([
                                            'Content-Type' => 'application/json',
                                            'Cookie' => 'XSRF-TOKEN=' . $csrfToken,
                                        ])
                                            ->post($api_key->api_endpoint_withdrawal, $array_data);

                                        $responseData = [
                                            'response_code' => $response->status(),
                                            'response_payload' => $response->body(),
                                            'response_headers' => json_encode($response->headers()),
                                        ];

                                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                                        
                                    } catch (\Exception $e) {
                                        LaravelLog::info('Telegram Withdrawal Callback not sent');
                                    }
                                }

                            }else{
                                $message = sprintf($this->messages[$api->lang]['transaction_not_found'], 
                                    $sender_message,
                                    $api->api_id
                                );
                                $response = Http::post($url, [
                                    'chat_id' => $sender_chat['id'],
                                    'text' => $message,
                                    'reply_to_message_id' => $TG_message['message_id'],
                                    'parse_mode' => 'Markdown',
                                ]);
                            }
                                
                        }
                    }else{
                        $message = sprintf($this->messages[$api->lang]['invalid_command'], 
                            $sender_message,
                            $api->api_id
                        );
                        $response = Http::post($url, [
                            'chat_id' => $sender_chat['id'],
                            'text' => $message,
                            'reply_to_message_id' => $TG_message['message_id'],
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                    
                        

                }
            }
            
            LaravelLog::info('Telegram Message'.$data);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            LaravelLog::info('Telegram failed');
            return response()->json(['status' => 'success'], 200);
        } 

    }


    public function convertStringToNumber($string)
    {
    if (strpos($string, '.') !== false) {
        $float = (float)$string;
        // If the number has no decimal places, return as integer
        if ($float == (int)$float) {
            return (int)$float;
        }
        // Otherwise return with 2 decimal places
        return number_format($float, 2, '.', '');
    } else {
        return (int)$string;
    }
    }

    protected function validationRules()
    {
    return [
        'group_name' => 'required|string|max:255',
        'group_username' => 'required|string|max:255',
        'status' => 'required|boolean',
        'api_id' => 'required|exists:apis,id'
    ];
    }

    private function processWebhookData(array $data)
    {
    if ($message = $data['message'] ?? $data['edited_message'] ?? null) {
        $this->handleIncomingMessage($message);
    }
    }

    private function handleIncomingMessage(array $message)
    {
    $senderChat = $message['chat'];
    $group = $this->findOrCreateGroup($senderChat);

    if ($this->shouldSendPendingMessage($group)) {
        $this->sendTelegramResponse($senderChat['id'], self::PENDING_MESSAGE, $message['message_id']);
        return;
    }

    $this->processUserMessage($group, $message, $senderChat);
    }

    private function findOrCreateGroup(array $senderChat): TelegramGroup
    {
    return TelegramGroup::firstOrCreate(
        ['group_username' => $senderChat['id']],
        [
            'group_name' => $senderChat['title'] ?? $this->generatePersonalChatTitle($senderChat),
            'status' => 0
        ]
    );
    }

    private function generatePersonalChatTitle(array $senderChat): string
    {
    return trim("{$senderChat['first_name']} {$senderChat['last_name']}");
    }

    private function shouldSendPendingMessage(TelegramGroup $group): bool
    {
    return empty($group->api_id) || $group->status == 0;
    }

    private function processUserMessage(TelegramGroup $group, array $message, array $senderChat)
    {
    $senderMessage = $message['text'] ?? $message['caption'] ?? '';
    $apiKey = Api::find($group->api_id);

    if (strtolower($senderMessage) === 'checkbalance') {
        $this->sendBalanceMessage($apiKey, $senderChat['id'], $message['message_id']);
        return;
    }

    $this->handleTransactionRequest($senderMessage, $group->api_id, $senderChat, $message['message_id']);
    }

    private function sendBalanceMessage(?Api $api, string $chatId, int $messageId)
    {
    $message = $api
        ? "Hello {$api->name}, Your Current Balance is: {$api->balance}."
        : 'Associated API account not found';

    $this->sendTelegramResponse($chatId, $message, $messageId);
    }

    private function handleTransactionRequest(string $transactionId, int $apiId, array $senderChat, int $messageId)
    {
    $deposit = Payment::with('gateway')
        ->where('partner_transection_id', $transactionId)
        ->where('api_id', $apiId)
        ->latest()
        ->first();

    $withdrawal = Payout::where('partner_transection_id', $transactionId)
        ->where('api_id', $apiId)
        ->latest()
        ->first();

    match (true) {
        !is_null($deposit) => $this->handleDeposit($deposit, $senderChat, $messageId),
        !is_null($withdrawal) => $this->handleWithdrawal($withdrawal, $senderChat, $messageId),
        default => $this->sendInvalidTicketMessage($senderChat['id'], $messageId)
    };
    }

    private function handleDeposit(Payment $deposit, array $senderChat, int $messageId)
    {
    $this->sendStatusMessage($deposit->status, $senderChat['id'], $messageId);
    $this->processDepositCallback($deposit);
    }

    private function handleWithdrawal(Payout $withdrawal, array $senderChat, int $messageId)
    {
    $this->sendStatusMessage($withdrawal->status, $senderChat['id'], $messageId);
    $this->processWithdrawalCallback($withdrawal);
    }

    private function sendStatusMessage($status, string $chatId, int $messageId)
    {
    $message = self::TRANSACTION_STATUS_MESSAGES[$status]
        ?? 'The transaction is in pending state. Please hold on while we transfer your request to our customer service.';

    $this->sendTelegramResponse($chatId, $message, $messageId);
    }

    private function processDepositCallback(Payment $deposit)
    {
    $apiKey = Api::find($deposit->api_id);

    if ($this->shouldProcessCallback($apiKey, 'api_endpoint_deposit')) {
        $payment = Payment::where('transaction_id', $deposit->id)->first();
        $callbackData = $payment ? $this->preparePaymentData($payment, $apiKey) : $this->prepareFundData($deposit, $apiKey);

        $this->sendApiCallback(
            $apiKey->api_endpoint_deposit,
            $callbackData,
            'Deposit'
        );
    }
    }

    private function processWithdrawalCallback(Payout $withdrawal)
    {
    $apiKey = Api::find($withdrawal->api_id);

    if ($this->shouldProcessCallback($apiKey, 'api_endpoint_withdrawal')) {
        $callbackData = $this->prepareWithdrawalData($withdrawal, $apiKey);
        $this->sendApiCallback(
            $apiKey->api_endpoint_withdrawal,
            $callbackData,
            'Withdrawal'
        );
    }
    }

    private function shouldProcessCallback(?Api $api, string $endpointField): bool
    {
    return $api && !empty($api->{$endpointField}) && $api->website !== config('app.website');
    }

    private function preparePaymentData(Payment $payment, Api $api): array
    {
    $baseData = [
        'amount' => $this->convertStringToNumber($payment->amount),
        'api_key' => $api->api_key,
        'e_wallet_name' => $payment->e_wallet_name,
        'id' => (string)$payment->id,
        'transaction_type' => 'Deposit',
        'user_account_no' => (string)$payment->sender,
    ];

    return $this->addSignature($baseData, $api->secret_key);
    }

    private function prepareFundData(Payment $fund, Api $api): array
    {
    $baseData = [
        'amount' => $this->convertStringToNumber($fund->amount),
        'api_key' => $api->api_key,
        'e_wallet_name' => $fund->gateway->name,
        'id' => '',
        'transaction_type' => 'Deposit',
        'user_account_no' => (string)$fund->account_no,
    ];

    return $this->addSignature($baseData, $api->secret_key);
    }

    private function prepareWithdrawalData(Payout $withdrawal, Api $api): array
    {
    $baseData = [
        'amount' => $this->convertStringToNumber($withdrawal->amount),
        'api_key' => $api->api_key,
        'e_wallet_name' => $withdrawal->e_wallet_name,
        'id' => (string)$withdrawal->id,
        'transaction_type' => 'Withdrawal',
        'user_account_no' => (string)$withdrawal->user_account_no,
    ];

    return $this->addSignature($baseData, $api->secret_key);
    }

    private function addSignature(array $data, string $secretKey): array
    {
    $hash = hash('sha256', json_encode($data));
    $hmac = hash_hmac('sha256', $hash, $secretKey);
    $data['sign'] = base64_encode($hmac . time());

    return $data;
    }

    private function sendApiCallback(string $url, array $data, string $type)
    {
    try {
        $logId = $this->logApiRequest($url, $data);
        $response = Http::withHeaders($this->apiHeaders())->post($url, $data);
        $this->logApiResponse($logId, $response);
    } catch (\Exception $e) {
        LaravelLog::error("Telegram {$type} Callback failed: {$e->getMessage()}");
    }
    }

    private function logApiRequest(string $url, array $data): int
    {
    return DB::table('api_logs')->insertGetId([
        'request_method' => 'POST',
        'request_url' => $url,
        'request_payload' => json_encode($data),
        'request_headers' => json_encode($this->apiHeaders()),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    }

    private function logApiResponse(int $logId, $response)
    {
    DB::table('api_logs')->where('id', $logId)->update([
        'response_code' => $response->status(),
        'response_payload' => $response->body(),
        'response_headers' => json_encode($response->headers()),
    ]);
    }

    private function apiHeaders(): array
    {
    return [
        'Content-Type' => 'application/json',
        'Cookie' => 'XSRF-TOKEN=' . csrf_token(),
    ];
    }

    private function sendInvalidTicketMessage(string $chatId, int $messageId)
    {
    $message = 'The entered ticket number does not match our records. Kindly check your ticket number.';
    $this->sendTelegramResponse($chatId, $message, $messageId);
    }

    private function sendTelegramResponse(string $chatId, string $text, int $replyToMessageId)
    {
    Http::post($this->telegramApiUrl(), [
        'chat_id' => $chatId,
        'text' => $text,
        'reply_to_message_id' => $replyToMessageId,
        'parse_mode' => 'Markdown',
    ]);
    }

    private function telegramApiUrl(): string
    {
    return "https://api.telegram.org/bot" . self::TELEGRAM_BOT_TOKEN . "/sendMessage";
    }
    
    
    
    function extractValueByStartPattern($text, $startKeywords, $valuePattern) {

        foreach ($startKeywords as $start) {
            $startPos = stripos($text, $start);
            if ($startPos !== false) {
                $substr = substr($text, $startPos + strlen($start), 100); // check next 100 chars
                if (preg_match($valuePattern, $substr, $match)) {
                    return trim($match[1]);
                }
            }
        }
        return null;
    }
    
    
    function extractTransactionDetails($text) {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = str_replace('BUG', '', $text);
        $text = str_replace('Date', ' Date', $text);
        $text = preg_replace('/\s+/', ' ', $text); // normalize
        $text = str_replace(["\n", "\r"], ' ', $text);
    
        $text = preg_replace('/(Date|Time|Txnid|Transaction)(\d)/i', '$1 $2', $text);
    
    
        $ewallet = "";
        $amount = "";
        $txn = "";
        
    
    
        $ewalletStarts = ['একাউন্ট নং','ক্যাশ আউট', 'Cash Out from A/C', 'Cash-out (', 'C:', 'Smrity Store_', 'Agent A/C No.', 'Rocket A/C No.', 'কাস্টমার', 'Mehedi Telecom'];
        $amountStarts = ['পরিমাণ', 'Amount', 'Transaction Amount', 'TxnAmount:', '৳', 'Tk', 'TkK', '%', '- ৳'];
        $txnStarts = ['ট্রানজেকশন আইডি রেফারেন্স', 'ট্রানজেকশন আইডি', 'Transaction ID', 'Txnid:', 'Txnld:', 'ID: #', 'সময় ট্রানজেকশন আইডি'];
    
    
        if (stripos($text, 'সময় ট্রানজেকশন আইডি') !== false) {
            if (preg_match('/সময় ট্রানজেকশন আইডি\s+[^\s]+\s+[^\s]+\s+([A-Z0-9]+)/i', $text, $match)) {
                $txn = $match[1];
            }
    
            if (preg_match('/([\d,]+\.\d{2})\s*\+/', $text, $match2)) {
                $amount = str_replace(',', '', $match2[1]);
            }
    
            $pos = stripos($text, 'সময় ট্রানজেকশন আইডি');
            $beforeText = substr($text, 0, $pos);
    
            if (preg_match('/([0-9\-]{6,20})\s*$/', trim($beforeText), $m3)) {
                $ewallet = str_replace('-', '', $m3[1]);
            }
        }elseif (stripos($text, 'কাস্টমার') !== false) {
            $txn     = $this->extractValueByStartPattern($text, $txnStarts, '/([A-Z0-9]{6,15})/i'); 
            $ewallet = $this->extractValueByStartPattern($text, $ewalletStarts, '/([0-9xX\-\*]{6,20})/');
        
    
            if (preg_match('/[%৳]\s*([0-9,]+(?:\.\d{1,2})?)/u', $text, $m2)) {
                $amount = str_replace(',', '', $m2[1]);
            }
    
        
        }
        else{
            $amount  = $this->extractValueByStartPattern($text, $amountStarts, '/([\d]{1,3}(?:,\d{3})*(?:\.\d{1,2})?|\d+(?:\.\d{1,2})?)/');
            $txn     = $this->extractValueByStartPattern($text, $txnStarts, '/([A-Z0-9]{6,15})/i'); 
            $ewallet = $this->extractValueByStartPattern($text, $ewalletStarts, '/([0-9xX\-\*]{6,20})/');   
        }
    
    
        
        
        $amount = str_replace(',', '', $amount);
        $ewallet = str_replace('-', '', $ewallet);
    
    
    
    
    
        return [
            'ewallet' => $ewallet,
            'amount' => $amount,
            'txn' => $txn,
        ];
    }

}