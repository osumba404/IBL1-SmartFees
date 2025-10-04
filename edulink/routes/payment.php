<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Direct payment routes without middleware complications
Route::get('/payment', function() {
    $student = Auth::guard('student')->user();
    if (!$student) {
        return redirect()->route('student.login');
    }
    
    $enrollment = $student->enrollments()->with(['course', 'semester'])->first();
    
    return view('payment.create', compact('student', 'enrollment'));
})->name('payment.create');

Route::post('/payment/process', function() {
    return redirect('/payment/success')->with('success', 'Payment initiated successfully');
})->name('payment.process');

Route::get('/payment/success', function() {
    return view('payment.success');
})->name('payment.success');

Route::get('/payment/cancel', function() {
    return view('payment.cancel');
})->name('payment.cancel');