<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Devices (Sanctum tokens) of the current user. API analogue of UserSessionController.
 */
class TokenController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tokens = $request->user()->tokens()
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('last_used_at')
            ->get();

        return TokenResource::collection($tokens);
    }

    public function destroy(Request $request, int $tokenId): Response|JsonResponse
    {
        if ($tokenId === $request->user()->currentAccessToken()->id) {
            return response()->json(['message' => __('sessions.cannot_terminate_current')], 422);
        }

        $deleted = $request->user()->tokens()->whereKey($tokenId)->delete();

        if (!$deleted) {
            return response()->json(['message' => __('sessions.session_not_found')], 404);
        }

        return response()->noContent();
    }

    /**
     * Revoke all tokens except current.
     */
    public function destroyOthers(Request $request): JsonResponse
    {
        $count = $request->user()->tokens()
            ->whereKeyNot($request->user()->currentAccessToken()->id)
            ->delete();

        return response()->json(['message' => __('sessions.others_terminated', ['count' => $count]), 'count' => $count]);
    }
}
