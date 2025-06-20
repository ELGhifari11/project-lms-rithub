<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [RegisterController::class, 'register'])->name('api.register');
Route::post('/v1/login', [LoginController::class, 'login']);
Route::post('/v1/verify/{unique_id}', [RegisterController::class, 'verify'])->middleware('auth:sanctum');
Route::post('/v1/resend-verification/{unique_id}', [RegisterController::class, 'resendOtp'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/v1/logout', [LoginController::class, 'logout']);
});

Route::get('/password-reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
Route::post('/v1/forgot-password', [ResetPasswordController::class, 'forgotPassword'])->name('password.email');
Route::post('/v1/reset-password', [ResetPasswordController::class, 'updatePassword'])->name('password.update');

