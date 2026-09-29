<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CredentialController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile app API. Authentication: Sanctum bearer tokens.
| Login gives full "access" token, or "2fa-pending" token which must be
| exchanged for full one via /auth/2fa/verify with the code from e-mail.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware(['auth:sanctum', 'abilities:' . AuthController::ABILITY_2FA_PENDING])->group(function () {
        Route::post('/auth/2fa/verify', [AuthController::class, 'verify2FA'])->name('auth.2fa.verify');
        Route::post('/auth/2fa/resend', [AuthController::class, 'resend2FA'])
            ->middleware('throttle:3,1')
            ->name('auth.2fa.resend');
    });

    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

    Route::middleware(['auth:sanctum', 'abilities:' . AuthController::ABILITY_ACCESS])->group(function () {
        Route::get('/me', [ProfileController::class, 'show'])->name('me.show');
        Route::patch('/me', [ProfileController::class, 'update'])->name('me.update');

        Route::get('/groups/root', [GroupController::class, 'root'])->name('groups.root');
        Route::apiResources([
            'groups' => GroupController::class,
            'credentials' => CredentialController::class,
            'notes' => NoteController::class,
        ]);

        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::delete('/tokens/{tokenId}', [TokenController::class, 'destroy'])->whereNumber('tokenId')->name('tokens.destroy');
        Route::delete('/tokens', [TokenController::class, 'destroyOthers'])->name('tokens.destroy-others');
    });
});
