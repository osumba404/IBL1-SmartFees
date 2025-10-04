@extends('layouts.student')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Payment Cancelled</h3>
                    <p class="text-muted">Your payment was cancelled or failed to process.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('student.payments.create') }}" class="btn btn-primary">
                            Try Again
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection