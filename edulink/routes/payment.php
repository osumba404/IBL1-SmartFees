<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Payment routes
Route::middleware('auth:student')->group(function () {
    Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process.post');
    Route::get('/payment/process/{payment}', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/pending/{paymentId}', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/status/{paymentId}', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/simulate-payment/{paymentId}', [PaymentController::class, 'simulate'])->name('simulate.payment');
});

// PayPal routes (no auth needed for returns)
Route::get('/payment/paypal/return', [PaymentController::class, 'paypalReturn'])->name('payment.paypal.return');
Route::get('/payment/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('payment.paypal.cancel');

// Webhook routes (no auth needed)
Route::post('/webhooks/mpesa', [PaymentController::class, 'callback'])->name('webhooks.mpesa');
Route::post('/webhooks/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');