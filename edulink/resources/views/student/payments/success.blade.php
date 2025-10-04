@extends('layouts.student')

@section('title', 'Payment Successful')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    
                    @if($payment)
                    <div class="alert alert-success">
                        <h5>Payment Details</h5>
                        <div class="row text-start">
                            <div class="col-sm-6">
                                <strong>Amount:</strong> KSh {{ number_format($payment->amount, 2) }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Course:</strong> {{ $payment->enrollment->course->name ?? 'N/A' }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Date:</strong> {{ $payment->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <p class="lead mb-4">
                        Your payment has been processed successfully. 
                        @if($payment && $payment->payment_method === 'mpesa')
                            You should receive an M-Pesa confirmation SMS shortly.
                        @elseif($payment && in_array($payment->payment_method, ['bank_transfer', 'cash']))
                            Your payment is pending verification and will be confirmed once processed.
                        @endif
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('student.payments') }}" class="btn btn-primary">
                            <i class="bi bi-list me-2"></i>View All Payments
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection