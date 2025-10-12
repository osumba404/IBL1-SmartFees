@extends('layouts.student')

@section('title', 'Payment Plan Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Payment Plan Details</h2>
                <p class="page-subtitle">View your payment plan progress and upcoming installments</p>
            </div>
            <a href="{{ route('student.payment-plans.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Payment Plans
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Plan: {{ $paymentPlan->plan_name ?? 'Payment Plan' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <small class="text-muted">Total Amount</small>
                            <div class="h5">KES {{ number_format($paymentPlan->total_amount ?? 0, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Total Installments</small>
                            <div class="h5">{{ $paymentPlan->total_installments ?? 0 }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <div class="h5">
                                <span class="badge bg-primary">{{ ucfirst($paymentPlan->status ?? 'active') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Created</small>
                            <div class="h5">{{ $paymentPlan->created_at ? $paymentPlan->created_at->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>

                    <h6>Installment Schedule</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentPlan->installments as $installment)
                                <tr>
                                    <td>{{ $installment->installment_number }}</td>
                                    <td>KES {{ number_format($installment->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($installment->due_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($installment->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($installment->status === 'overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($installment->status !== 'paid')
                                            <a href="{{ route('payment.create', ['enrollment_id' => $paymentPlan->enrollment->id, 'amount' => $installment->amount]) }}" class="btn btn-sm btn-success">Pay Now</a>
                                        @else
                                            <span class="text-muted">Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No installments found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Course:</strong> {{ $paymentPlan->enrollment->course->name ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Student:</strong> {{ auth('student')->user()->full_name }}
                    </div>
                    <div class="mb-2">
                        <strong>Plan Created:</strong> {{ $paymentPlan->created_at ? $paymentPlan->created_at->format('M d, Y') : 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Enrollment ID:</strong> #{{ $paymentPlan->enrollment->id ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection