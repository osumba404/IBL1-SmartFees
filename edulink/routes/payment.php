<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Payment routes
Route::middleware('auth:student')->group(function () {
    Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/pending/{paymentId}', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/status/{paymentId}', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/simulate-payment/{paymentId}', [PaymentController::class, 'simulate'])->name('simulate.payment');
});

// Webhook routes (no auth needed)
Route::post('/webhooks/mpesa', [PaymentController::class, 'callback'])->name('webhooks.mpesa');