<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [RegisterController::class, 'register'])->name('api.register');
Route::post('/v1/login', [LoginController::class, 'login']);

Route::get('/v1/email/verify', [RegisterController::class, 'verificationNotice'])->name('verification.notice');
Route::post('/v1/email/verify/{id}/{hash}', [RegisterController::class, 'verify'])->name('verification.verify');
Route::post('/v1/email/resend', [RegisterController::class, 'resendEmailVerification'])->name('verification.send')->middleware(['auth:sanctum','throttle:6,1']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/v1/logout', [LoginController::class, 'logout']);
});

Route::get('/v1/password-reset{token}', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
Route::post('/v1/forgot-password', [ResetPasswordController::class, 'forgotPassword'])->name('password.email');
Route::post('/v1/reset-password', [ResetPasswordController::class, 'updatePassword'])->name('password.update');

