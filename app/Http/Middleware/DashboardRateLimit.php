<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;

class DashboardRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'dashboard:' . $request->user()->id . ':' . $request->ip();
        
        $executed = RateLimiter::attempt(
            $key,
            $perMinute = 30, // 30 requests per minute
            function () use ($request, $next) {
                return $next($request);
            }
        );

        if (!$executed) {
            return Response::json([
                'success' => false,
                'message' => 'Too many dashboard requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        return $executed;
    }
}
