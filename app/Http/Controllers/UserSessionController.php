<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSession;

class UserSessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('2fa');
    }

    /**
     * Display a listing of user sessions.
     */
    public function index()
    {
        $user = Auth::user();
        $sessions = UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('pages.users.sessions.index', compact('sessions'));
    }

    /**
     * Terminate a specific session.
     */
    public function terminate(Request $request, $sessionId)
    {
        $user = Auth::user();

        $session = UserSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return back()->with('error', __('sessions.session_not_found'));
        }

        // Don't allow terminating current session
        if ($sessionId === session()->getId()) {
            return back()->with('error', __('sessions.cannot_terminate_current'));
        }

        $session->deactivate();

        return back()->with('success', __('sessions.session_terminated'));
    }

    /**
     * Terminate all other sessions except current.
     */
    public function terminateOthers(Request $request)
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        $terminatedCount = UserSession::where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', __('sessions.others_terminated', ['count' => $terminatedCount]));
    }

    /**
     * Get session statistics.
     */
    public function stats()
    {
        $user = Auth::user();

        $stats = [
            'total_sessions' => UserSession::where('user_id', $user->id)->count(),
            'active_sessions' => UserSession::getActiveSessionsCount($user->id),
            'current_session' => session()->getId(),
        ];

        return response()->json($stats);
    }
}
