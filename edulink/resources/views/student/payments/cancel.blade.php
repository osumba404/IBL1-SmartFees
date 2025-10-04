@extends('layouts.student')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-warning mb-3">Payment Cancelled</h2>
                    
                    <p class="lead mb-4">
                        Your payment was cancelled or could not be completed. 
                        No charges have been made to your account.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('student.payments.create') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Try Again
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