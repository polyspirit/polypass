<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\Send2FACodeMail;
use App\Models\RemoteAccess;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public const ABILITY_ACCESS = 'access';
    public const ABILITY_2FA_PENDING = '2fa-pending';

    private const TOKEN_LIFETIME_DAYS = 30;
    private const PENDING_TOKEN_LIFETIME_MINUTES = 15;
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const CODE_MAX_ATTEMPTS = 5;


    // API

    /**
     * Check credentials. Returns full access token, or pending token if 2FA is enabled (code is sent by e-mail).
     */
    public function login(Request $request): JsonResponse
    {
        $ip = $request->ip();
        if (RemoteAccess::isIpBlocked($ip)) {
            abort(404);
        }

        $remoteAccess = RemoteAccess::create(['ip' => $ip, 'path' => $request->path()]);

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, self::LOGIN_MAX_ATTEMPTS)) {
            event(new Lockout($request));

            $remoteAccess->blocked = true;
            $remoteAccess->save();

            throw ValidationException::withMessages([
                'email' => [__('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey)])],
            ])->status(429);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages(['email' => [__('auth.failed')]]);
        }

        RateLimiter::clear($throttleKey);

        if (!config('app.2fa_enabled', true)) {
            return $this->issueAccessToken($user, $request->input('device_name'), $request->ip());
        }

        $pendingToken = $user->createToken(
            $request->input('device_name'),
            [self::ABILITY_2FA_PENDING],
            now()->addMinutes(self::PENDING_TOKEN_LIFETIME_MINUTES)
        );

        $this->sendCode($user, $pendingToken->accessToken);

        return response()->json([
            'two_factor_required' => true,
            'token' => $pendingToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $pendingToken->accessToken->expires_at,
        ]);
    }

    /**
     * Exchange pending token + e-mail code for full access token.
     */
    public function verify2FA(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        /** @var PersonalAccessToken $pendingToken */
        $pendingToken = $request->user()->currentAccessToken();
        $cacheKey = $this->codeCacheKey($pendingToken);
        $stored = Cache::get($cacheKey);

        if (!$stored || !Hash::check($request->input('code'), $stored['hash'])) {
            if (!$stored || ++$stored['attempts'] >= self::CODE_MAX_ATTEMPTS) {
                Cache::forget($cacheKey);
                $pendingToken->delete();

                abort(401, __('signin.2fa_attempts_exceeded'));
            }

            Cache::put($cacheKey, $stored, $pendingToken->expires_at);

            throw ValidationException::withMessages(['code' => [__('signin.2fa_code_invalid')]]);
        }

        Cache::forget($cacheKey);
        $pendingToken->delete();

        return $this->issueAccessToken($request->user(), $pendingToken->name, $request->ip());
    }

    public function resend2FA(Request $request): JsonResponse
    {
        $this->sendCode($request->user(), $request->user()->currentAccessToken());

        return response()->json(['message' => __('signin.2fa_sent')]);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }


    // OTHER

    private function issueAccessToken(User $user, string $deviceName, ?string $ip): JsonResponse
    {
        $token = $user->createToken(
            $deviceName,
            [self::ABILITY_ACCESS],
            now()->addDays(self::TOKEN_LIFETIME_DAYS)
        );
        $token->accessToken->forceFill(['last_ip' => $ip])->save();

        return response()->json([
            'two_factor_required' => false,
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at,
            'user' => new UserResource($user),
        ]);
    }

    private function sendCode(User $user, PersonalAccessToken $pendingToken): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put(
            $this->codeCacheKey($pendingToken),
            ['hash' => Hash::make($code), 'attempts' => 0],
            $pendingToken->expires_at
        );

        Mail::to($user->email)->send(new Send2FACodeMail($code));
    }

    private function codeCacheKey(PersonalAccessToken $token): string
    {
        return 'api_2fa_code:' . $token->id;
    }
}
