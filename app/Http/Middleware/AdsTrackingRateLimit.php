<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AdsTrackingRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limit berdasarkan IP + Ads ID untuk mencegah spam
        $adsId = $request->route('id');
        $key = $request->ip() . ':ads:' . $adsId;

        // Limit: 10 requests per minute per IP per ads ID
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'code' => 429,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }

        // Increment attempt count
        RateLimiter::hit($key, 60); // 60 seconds window

        return $next($request);
    }
}
