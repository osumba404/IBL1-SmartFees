@extends('layouts.student')

@section('title', 'Payment Success')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Payment Successful!</h3>
                    <p class="text-muted">Your payment has been processed successfully.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                            Back to Dashboard
                        </a>
                        <a href="{{ route('student.payments.history') }}" class="btn btn-outline-primary">
                            View Payment History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection