<?php

namespace App\Http\Middleware;

use Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Check2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (empty($request->cookie('user_2fa'))) {
            return redirect()->route('2fa');
        } else {
            $linkCode = decrypt($request->cookie('user_2fa'));
            $info = explode('|', $linkCode);
            $userId = $info[0];
            $code = $info[1];

            $user = \App\Models\User::find($userId);

            if ($user->auth_code !== $code) {
                return redirect()->route('2fa');
            }
        }

        return $next($request);
    }
}
