<?php

namespace App\Http\Middleware;

use Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User2FAToken;

class Check2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip 2FA check if disabled in configuration
        if (!config('app.2fa_enabled', true)) {
            return $next($request);
        }

        // Check for new token-based 2FA
        if ($request->hasCookie('user_2fa_token')) {
            $token = $request->cookie('user_2fa_token');
            $user = auth()->user();

            if ($user) {
                $tokenRecord = User2FAToken::where('user_id', $user->id)
                    ->where('token', $token)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->first();

                if ($tokenRecord) {
                    return $next($request);
                }
            }
        }

        // Fallback to old cookie-based 2FA for backward compatibility
        // Temporarily disabled to force new token system
        /*
        if (!empty($request->cookie('user_2fa'))) {
            try {
                $linkCode = decrypt($request->cookie('user_2fa'));
                $info = explode('|', $linkCode);
                $userId = $info[0];
                $code = $info[1];

                $user = \App\Models\User::find($userId);

                if ($user && $user->auth_code === $code) {
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Invalid cookie, continue to 2FA
            }
        }
        */

        return redirect()->route('2fa');
    }
}
