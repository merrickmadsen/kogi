<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KogiProfileController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $token = $request->user()->createToken('kogi')->plainTextToken;
        return response()->json(['token' => $token]);
    }
    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [KogiProfileController::class, 'show']);
    Route::put('/profile', [KogiProfileController::class, 'update']);
});