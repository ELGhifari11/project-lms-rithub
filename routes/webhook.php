<?php

use App\Http\Controllers\Webhook\InvoiceWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/webhook/xendit/invoice', [InvoiceWebhookController::class, 'invoiceHandler']);
