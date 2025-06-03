<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TrackFunctionUsage
{
    public function handle(Request $request, Closure $next)
    {
        $requestData = $request->all();
        $method = $request->method();
        $url = $request->fullUrl();

        $route = Route::current();
        $controller = null;
        $function = null;

        if ($route) {
            $action = $route->getActionName();
            if (strpos($action, '@') !== false) {
                [$controller, $function] = explode('@', class_basename($action));
            }
        }

        try {
            $response = $next($request);

            $responseData = $this->extractResponseData($response);

            $this->logFunctionUsage($method, $url, $controller, $function, $requestData, $responseData);

            return $response;

        } catch (Throwable $e) {
            // 🔴 Log real error details — not HTML
            $errorDetails = [
                'error' => true,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5), // optional: limit stack trace
            ];

            $this->logFunctionUsage($method, $url, $controller, $function, $requestData, $errorDetails);

            throw $e;
        }
    }

    private function extractResponseData($response)
    {
        try {
            if ($response instanceof View) {
                return $response->getData();
            }

            if ($response instanceof \Illuminate\Http\Response || $response instanceof Response) {
                $original = $response->getOriginalContent();

                if ($original instanceof View) {
                    return $original->getData();
                }

                // dd($original);

                if (is_string($original)) {
                    // If the response is an HTML document (like Blade), attempt to extract the error message
                    if (stripos($original, '<!DOCTYPE html>') !== false || stripos($original, '<html') !== false) {
                        // Try to find a Laravel error line inside the HTML
                        if (preg_match('/(ErrorException|Fatal error|Symfony\\\Component\\\ErrorHandler\\\Error\\\FatalError)[^<>\n]+/i', $original, $matches)) {
                            return trim($matches[0]);
                        }
                
                        // Check for common Blade-related or compact() errors
                        if (preg_match('/compact\(\):[^<>\n]+/i', $original, $matches)) {
                            return 'ErrorException: ' . trim($matches[0]);
                        }
                
                        // Fallback
                        return '[HTML error detected, but no specific message found]';
                    }
                
                    // Otherwise return string response up to 500 characters
                    return mb_substr($original, 0, 500);
                }
                

                return $original;
            }

            return is_string($response) ? mb_substr($response, 0, 500) : $response;
        } catch (Throwable $e) {
            return [
                'error' => true,
                'message' => 'Response extraction failed: ' . $e->getMessage(),
            ];
        }
    }

    private function logFunctionUsage($method, $url, $controller, $function, $requestData, $responseData)
    {
        try {
            DB::table('function_logs')->insert([
                'method' => $method,
                'url' => $url,
                'controller' => $controller,
                'function' => $function,
                'request_data' => json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR),
                'response_data' => json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error("Function log failed: " . $e->getMessage());
        }
    }
}
