<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\RegisterController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/v1/verified-email', [RegisterController::class, 'verifiedEmail']);