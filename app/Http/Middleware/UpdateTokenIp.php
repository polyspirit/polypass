<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class UpdateTokenIp
{
    /**
     * Save client IP to the current Sanctum token. Writes only when IP changed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken && $token->last_ip !== $request->ip()) {
            $token->forceFill(['last_ip' => $request->ip()])->save();
        }

        return $next($request);
    }
}
