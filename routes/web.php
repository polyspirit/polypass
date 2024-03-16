<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorAuthentication;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\GroupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['configs.set']], function () {
    Auth::routes();

    Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('2fa');
    Route::get('/2fa/{code}', [TwoFactorAuthentication::class, 'check'])->name('2fa_check');
    Route::get('/theme/{theme}', [HomeController::class, 'theme'])->name('theme');
});

Route::group(['middleware' => ['configs.set', 'auth', '2fa']], function () {
    Route::view('/generator', 'pages.generator', ['title' => __('generator.password-generator')]);

    Route::resources([
        'users' => UserController::class,
        'groups' => GroupController::class,
        'credentials' => CredentialController::class,
        'notes' => NoteController::class
    ]);
});

Route::group(['middleware' => ['configs.set', 'auth']], function () {
    Route::get('/2fa', [TwoFactorAuthentication::class, 'index'])->name('2fa');
});
