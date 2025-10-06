@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="text-center">
                        <h2 class="mb-1">Welcome back, {{ auth('student')->user()->first_name }}!</h2>
                        <p class="mb-2 opacity-75">Student ID: {{ auth('student')->user()->student_id }}</p>
                        <p class="mb-3 opacity-75">Enrolled since: {{ auth('student')->user()->enrollment_date->format('F Y') }}</p>
                        
                        <div class="avatar avatar-xl d-inline-block">
                            @if(auth('student')->user()->profile_picture)
                                <img src="{{ asset('storage/profile-pictures/' . auth('student')->user()->profile_picture) }}" 
                                     alt="{{ auth('student')->user()->first_name }}" 
                                     class="rounded-circle" 
                                     style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                            @else
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 1.8rem; font-weight: 600;">
                                    {{ strtoupper(substr(auth('student')->user()->first_name, 0, 1) . substr(auth('student')->user()->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    @php
        $financialSummary = auth('student')->user()->getFinancialSummary();
    @endphp
    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h5 class="card-title">Total Fees</h5>
                    <h3 class="text-primary mb-0">KES {{ number_format($financialSummary['total_fees_owed'], 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h5 class="card-title">Paid Amount</h5>
                    <h3 class="text-success mb-0">KES {{ number_format($financialSummary['total_fees_paid'], 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h5 class="card-title">Outstanding</h5>
                    <h3 class="text-warning mb-0">KES {{ number_format($financialSummary['outstanding_balance'], 2) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h5 class="card-title">Pending</h5>
                    <h3 class="text-info mb-0">KES {{ number_format($financialSummary['pending_payments'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Alert -->
    @if($financialSummary['outstanding_balance'] > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Outstanding Balance:</strong> You have an outstanding balance of KES {{ number_format($financialSummary['outstanding_balance'], 2) }}.
                        @if($financialSummary['has_overdue_payments'])
                            <span class="text-danger">Some payments are overdue.</span>
                        @endif
                    </div>
                    <a href="{{ route('payment.create') }}" class="btn btn-warning btn-sm ms-3">Make Payment</a>
                </div>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-3"></i>
                    <div>
                        <strong>All Caught Up!</strong> You have no outstanding balance. Keep up the good work!
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Recent Payments -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Payments</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($financialSummary['recent_payments']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($financialSummary['recent_payments'] as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>KES {{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst($payment->payment_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No payments recorded yet.</p>
                            <a href="{{ route('payment.create') }}" class="btn btn-primary">Make Your First Payment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('payment.create') }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-2"></i>Make Payment
                        </a>
                        <a href="{{ route('student.statements.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-download me-2"></i>Download Statement
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i>Payment History
                        </a>
                        <a href="{{ route('student.profile') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-edit me-2"></i>Update Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 border rounded payment-method-card" onclick="window.location.href='{{ route('payment.create') }}'" style="cursor: pointer;">
                                <i class="fas fa-mobile-alt fa-2x text-success mb-2"></i>
                                <div class="small">M-Pesa</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 border rounded payment-method-card" onclick="window.location.href='{{ route('payment.create') }}'" style="cursor: pointer;">
                                <i class="fab fa-cc-visa fa-2x text-primary mb-2"></i>
                                <div class="small">Card</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Courses</h5>
                    <span class="badge bg-primary">{{ auth('student')->user()->activeEnrollments()->count() }} Active</span>
                </div>
                <div class="card-body">
                    @if(auth('student')->user()->activeEnrollments()->count() > 0)
                        <div class="row">
                            @foreach(auth('student')->user()->activeEnrollments as $enrollment)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $enrollment->course->name }}</h6>
                                            <p class="card-text small text-muted">{{ $enrollment->course->course_code }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-success">{{ ucfirst($enrollment->status) }}</span>
                                                <small class="text-muted">{{ $enrollment->course->duration_months }} months</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active course enrollments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .alert {
        border: none;
        border-radius: 10px;
    }
    
    .btn {
        border-radius: 8px;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    
    .payment-method-card {
        transition: all 0.3s ease;
        border: 2px solid #dee2e6 !important;
    }
    
    .payment-method-card:hover {
        border-color: #007bff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: #f8f9ff;
    }
</style>
@endpush
