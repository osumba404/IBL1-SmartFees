@extends('layouts.student')

@section('title', 'Payment Plans')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Payment Plans</h2>
            <p class="page-subtitle">Manage your flexible payment schedules</p>
        </div>
    </div>

    @if($paymentPlans->count() > 0)
        <div class="row">
            @foreach($paymentPlans as $plan)
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $plan->plan_name }}</h5>
                        <span class="badge bg-{{ $plan->status === 'completed' ? 'success' : ($plan->status === 'overdue' ? 'danger' : 'primary') }}">
                            {{ ucfirst($plan->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Course</small>
                                <div class="fw-bold">{{ $plan->enrollment->course->name }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Total Amount</small>
                                <div class="fw-bold">KES {{ number_format($plan->total_amount, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: {{ $plan->progress_percentage }}%"></div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-4">
                                <small class="text-muted">Paid</small>
                                <div class="text-success fw-bold">KES {{ number_format($plan->paid_amount, 2) }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Remaining</small>
                                <div class="text-warning fw-bold">KES {{ number_format($plan->remaining_amount, 2) }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Installments</small>
                                <div class="fw-bold">{{ $plan->completed_installments }}/{{ $plan->total_installments }}</div>
                            </div>
                        </div>

                        @if($plan->next_installment)
                        <div class="alert alert-info">
                            <strong>Next Payment:</strong> KES {{ number_format($plan->next_installment->total_amount, 2) }} 
                            due {{ $plan->next_installment->due_date->format('M d, Y') }}
                            @if($plan->next_installment->is_overdue)
                                <span class="badge bg-danger ms-2">Overdue</span>
                            @endif
                        </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('student.payment-plans.show', $plan) }}" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                            @if($plan->next_installment)
                            <a href="{{ route('student.payment-plans.pay-installment', $plan->next_installment) }}" 
                               class="btn btn-success btn-sm">
                                Pay Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-check fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Payment Plans</h5>
            <p class="text-muted">You don't have any payment plans yet.</p>
            <a href="{{ route('student.enrollments.index') }}" class="btn btn-primary">View Enrollments</a>
        </div>
    @endif
</div>
@endsection