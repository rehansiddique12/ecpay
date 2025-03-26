<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api;
use App\Models\Fund;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\PayoutLog;
use Illuminate\Http\Request;
use App\Models\TelegramGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class TelegramGroupController extends Controller
{
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

    public function telegramwebhook(Request $request)
    {
        LaravelLog::info('Telegram webhook initiated');

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->processWebhookData($data);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            LaravelLog::error("Telegram webhook failed: {$e->getMessage()}");
            return response()->json(['status' => 'error'], 500);
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
        $deposit = Fund::with('gateway')
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

    private function handleDeposit(Fund $deposit, array $senderChat, int $messageId)
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

    private function processDepositCallback(Fund $deposit)
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

    private function prepareFundData(Fund $fund, Api $api): array
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

    public function convertStringToNumber($string)
    {
        return strpos($string, '.') !== false ? (float)$string : (int)$string;
    }
}
