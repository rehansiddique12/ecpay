<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Api;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LaravelLog;

class SendCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send_callback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LaravelLog::info('send callback test');
        // exit;
        $records = Payment::where('callback', 0)->where('status', 'Complete')
            ->whereBetween('updated_at', [Carbon::now()->subMinutes(5), Carbon::now()->subSeconds(30)])
            ->get();

        foreach ($records as $payment) {
            
            $partner_api_key = Api::where('id', $payment->api_id)->first();

            if ($partner_api_key && !empty($partner_api_key->api_endpoint_deposit) && $partner_api_key->website != env('APP_WEBSITE')) {
                    $string_to_hash = json_encode(array(
                        "amount" => strval($this->convertStringToNumber($payment->amount)),
                        "api_key" => $partner_api_key->api_key,
                        "e_wallet_name" => $payment->e_wallet_name,
                        "id" => strval($payment->id),
                        'transaction_type' => 'Deposit',
                        "user_account_no" => strval($payment->sender),
                    ));
                    $secretKey = $partner_api_key->secret_key;
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
                            'request_amount' => $this->convertStringToNumber($payment->request_amount),
                            'user_account_no' => $payment->sender,
                            'txn_id' => $payment->txn_id,
                            'e_wallet_phone_number' => $payment->e_wallet_phone_number,
                            'e_wallet_type' => $payment->e_wallet_type,
                            'charges' => $this->convertStringToNumber($payment->charge),
                            'status' => $payment->status,
                            'completion_date' => $payment->date,
                            'completion_time' => $payment->time,
                            'created_at' => $payment->created_at,
                            'updated_at' => $payment->updated_at,
                            'sign' => $sign,
                            'source' => '1Callback',
                    ];

                    if(!empty($payment->member_id)){
                        $array_data['member_id'] = $payment->member_id;
                    }

                    $requestData = [
                        'request_method' => 'POST', // or 'GET', 'PUT', etc. depending on your HTTP method
                        'request_url' => $partner_api_key->api_endpoint_deposit,
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
                            ->post($partner_api_key->api_endpoint_deposit, $array_data);


                        $responseData = [
                            'response_code' => $response->status(),
                            'response_payload' => $response->body(),
                            'response_headers' => json_encode($response->headers()),
                        ];

                        DB::table('api_logs')->where('id', $logId)->update($responseData);
                        $payment->callback=1;
                        $payment->save();
                        
                    } catch (\Exception $e) {
                        // Ignore the error and do nothing
                    }
                }
                

            

                
        }
        
        
            

        return true;
    }


    public function convertStringToNumber($string)
    {
        if (strpos($string, '.') !== false) {
            return (float)$string;
        } else {
            return (int)$string;
        }
    }
}
