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
        $pageTitle = $title = "Manage Telegram Groups";

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
                    }elseif(strpos($lowercaseText, "/checkorder") === 0){
                        
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
                                                                
                                                                
                                                                LaravelLog::info("Final message being sent: " . $message);
                                                                
                                                                // Add instructions for verification with the correct transaction ID
                                                                if ($txnId) {
                                                                    
                                                                   /////////////////////////////
                                                                //////////////////////////////
                                                                /////////////////////////////////
                                                                
                                                                DB::beginTransaction();
                                                                $payment = PendingPayment::where('txn_id', $txnId)->lockForUpdate()->first();
                                                                if($payment){
                                                                    if($payment){
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
                        
                            
                        }elseif(strpos($lowercaseText, "/callback") === 0){
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
}
