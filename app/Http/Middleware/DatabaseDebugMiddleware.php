<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\DatabaseDebugService;

class DatabaseDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if database debug is enabled
        $debugEnabled = Cache::get('db_debug_capturing', false);
        
        if ($debugEnabled) {
            // Enable query log
            DB::enableQueryLog();
        }
        
        $response = $next($request);
        
        if ($debugEnabled) {
            // Store captured queries in cache for later retrieval
            $queries = DB::getQueryLog();
            
            if (!empty($queries)) {
                $existingQueries = Cache::get('db_debug_queries', []);
                $existingQueries = array_merge($existingQueries, $queries);
                
                // Keep only last 500 queries
                if (count($existingQueries) > 500) {
                    $existingQueries = array_slice($existingQueries, -500);
                }
                
                Cache::put('db_debug_queries', $existingQueries, 3600);
            }
        }
        
        return $response;
    }
}