<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Send2FALinkMail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use App\Models\User;

class TwoFactorAuthentication extends Controller
{
    public function index(Request $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            Mail::to($user->email)->send(
                new Send2FALinkMail(
                    'Mail from ' . env('APP_NAME'),
                    $this->makeAndSaveNewCode($user)
                )
            );
        } catch (\Throwable $e) {
            report($e);

            return view('auth.2fa.error', ['message' => $e->getMessage()]);
        }

        return response(view('auth.2fa.sent', ['title' => __('signin.2fa')]));
    }

    private function makeAndSaveNewCode(User $user): string
    {
        $date = new \DateTime('1 hour');
        $code = Str::random(6);
        $linkCode = encrypt($user->id . '|' . $code . '|' . $date->getTimestamp());

        $user->auth_code = $code;
        $user->save();

        return $linkCode;
    }

    public function check(Request $request)
    {
        if (empty($request->code)) {
            return view('auth.2fa.error', ['message' => __('signin.2fa_code_wrong')]);
        }

        $linkCode = decrypt($request->code);
        $info = explode('|', $linkCode);
        $userId = $info[0];
        $code = $info[1];
        $timestamp = $info[2];

        $user = User::find($userId);

        if ($user->auth_code !== $code) {
            return view('auth.2fa.error', ['message' => __('signin.2fa_code_wrong')]);
        }

        $dateExpires = (new \DateTime())->setTimestamp($timestamp);
        $now = new \DateTime('now');

        if ($now > $dateExpires) {
            return view('auth.2fa.error', ['message' => __('signin.2fa_code_expired')]);
        }

        $cookie = cookie('user_2fa', $request->code, 43200); // 30 days

        return redirect(RouteServiceProvider::HOME)->cookie($cookie);
    }
}
