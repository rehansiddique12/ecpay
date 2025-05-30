<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TrackFunctionUsage
{
    public function handle(Request $request, Closure $next)
    {
        // Clone request data
        $requestData = $request->all();
        $method = $request->method();
        $url = $request->fullUrl();

        // Route info
        $route = Route::current();
        $controller = null;
        $function = null;

        if ($route) {
            $action = $route->getActionName(); // e.g. App\Http\Controllers\MyController@myFunction
            if (strpos($action, '@') !== false) {
                [$controller, $function] = explode('@', class_basename($action));
            }
        }

        // Proceed with the request
        $response = $next($request);

        // Log into DB
        DB::table('function_logs')->insert([
            'method' => $method,
            'url' => $url,
            'controller' => $controller,
            'function' => $function,
            'request_data' => json_encode($requestData),
            'response_data' => method_exists($response, 'getContent') ? $response->getContent() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $response;
    }
}
