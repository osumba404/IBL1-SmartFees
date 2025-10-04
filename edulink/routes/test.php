<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-payment-route', function() {
    return 'Payment route test works!';
});