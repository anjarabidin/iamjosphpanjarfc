<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateApiKey
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
        if (!$this->isValidApiKey($request)) {
            return response()->json([
                'code'    => 401,
                'success' => false,
                'message' => 'API key is invalid!',
            ]);
        }

        $this->autoLogoutBlockedUser($request);

        return $next($request);
    }

    private function isValidApiKey(Request $request): bool
    {
        $apiKey = config('app.api_key');
        return !empty($apiKey) && $request->header('x-api-key') === $apiKey;
    }

    private function autoLogoutBlockedUser(Request $request): void
    {
        $user = $request->user();

        if ($user && !$user->is_active) {
            $user->token()->revoke();
        }
    }
}
