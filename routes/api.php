<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('api.register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('api.login');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:sanctum')
        ->name('api.logout');
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/urls', [UrlController::class, 'shortenUrl']);
});


Route::get('/urls/{id}/analytics', [UrlController::class, 'show']);

