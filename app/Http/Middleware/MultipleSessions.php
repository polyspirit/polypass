<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\UserSession;

class MultipleSessions
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
        // Skip for static assets and API requests
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Check if multiple sessions are enabled
        if (!config('session.multiple_sessions', true)) {
            return $next($request);
        }

        // If user is authenticated, track the session
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = Session::getId();

            // Find or create session record
            $userSession = UserSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$userSession) {
                // Create new session record
                $userSession = UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity' => now(),
                    'is_active' => true,
                ]);
            } else {
                // Update last activity
                $userSession->updateActivity();
            }

            // Store session info in session for quick access
            if (!$request->session()->has('user_session_id')) {
                $request->session()->put('user_session_id', $sessionId);
                $request->session()->put('user_id', $user->id);
                $request->session()->put('login_time', now());
            }
        }

        return $next($request);
    }

    /**
     * Check if request should be skipped
     *
     * @param Request $request
     * @return bool
     */
    private function shouldSkip(Request $request): bool
    {
        $path = $request->path();

        // Skip for static assets
        if (
            str_starts_with($path, 'build/') ||
            str_starts_with($path, 'images/') ||
            str_starts_with($path, 'favicon') ||
            str_ends_with($path, '.css') ||
            str_ends_with($path, '.js') ||
            str_ends_with($path, '.png') ||
            str_ends_with($path, '.jpg') ||
            str_ends_with($path, '.jpeg') ||
            str_ends_with($path, '.gif') ||
            str_ends_with($path, '.ico') ||
            str_ends_with($path, '.woff') ||
            str_ends_with($path, '.woff2') ||
            str_ends_with($path, '.ttf') ||
            str_ends_with($path, '.eot')
        ) {
            return true;
        }

        // Skip for API requests
        if (str_starts_with($path, 'api/')) {
            return true;
        }

        return false;
    }
}
