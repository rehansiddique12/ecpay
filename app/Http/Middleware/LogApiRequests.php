<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log request information
        $requestData = [
            'request_method' => $request->getMethod(),
            'request_url' => $request->fullUrl(),
            'request_payload' => json_encode($request->all()),
            'request_headers' => json_encode($request->header()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $logId = DB::table('api_logs')->insertGetId($requestData);

        // Process the request
        $response = $next($request);

        // Log response information
        $responseData = [
            'response_code' => $response->getStatusCode(),
            'response_payload' => $response->getContent(),
            'response_headers' => json_encode($response->headers->all()),
        ];
        DB::table('api_logs')->where('id', $logId)->update($responseData);

        return $response;
    }
}
