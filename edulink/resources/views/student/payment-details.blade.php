@extends('layouts.student')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Payment Details</h2>
                    <p class="page-subtitle">View detailed information about your payment</p>
                </div>
                <a href="{{ route('student.payments.history') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i>Back to History
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Payment Reference:</strong></td>
                                    <td>{{ $payment->payment_reference }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction ID:</strong></td>
                                    <td>{{ $payment->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><span class="h5 text-success">KES {{ number_format($payment->amount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td>{{ ucfirst($payment->payment_type) }} Fee</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($payment->status)
                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">Failed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ ucfirst($payment->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date:</strong></td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Processed Date:</strong></td>
                                    <td>{{ $payment->processed_at ? $payment->processed_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Verified:</strong></td>
                                    <td>
                                        @if($payment->is_verified)
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Yes</span>
                                        @else
                                            <span class="text-warning"><i class="bi bi-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Information -->
            @if($payment->enrollment)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Course:</strong> {{ $payment->enrollment->course->name ?? 'N/A' }}</p>
                            <p><strong>Course Code:</strong> {{ $payment->enrollment->course->course_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Semester:</strong> {{ $payment->enrollment->semester->name ?? 'N/A' }}</p>
                            <p><strong>Academic Year:</strong> {{ $payment->enrollment->semester->academic_year ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Method Details -->
            @if($payment->payment_method === 'mpesa' && $payment->mpesa_receipt_number)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">M-Pesa Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Receipt Number:</strong> {{ $payment->mpesa_receipt_number }}</p>
                            <p><strong>Phone Number:</strong> {{ $payment->mpesa_phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Transaction Date:</strong> {{ $payment->mpesa_transaction_date ?? 'N/A' }}</p>
                            <p><strong>Transaction Cost:</strong> KES {{ number_format($payment->mpesa_transaction_cost ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($payment->payment_method === 'bank_transfer' && $payment->bank_reference)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bank Transfer Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Bank Name:</strong> {{ $payment->bank_name ?? 'N/A' }}</p>
                            <p><strong>Reference Number:</strong> {{ $payment->bank_reference }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Transaction Date:</strong> {{ $payment->bank_transaction_date ? $payment->bank_transaction_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-6 fw-bold text-primary">KES {{ number_format($payment->amount, 2) }}</div>
                        <div class="text-muted">Total Amount</div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Payment Method:</span>
                        <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status:</span>
                        <span class="fw-bold text-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    
                    @if($payment->outstanding_balance_before > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Balance Before:</span>
                        <span>KES {{ number_format($payment->outstanding_balance_before, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($payment->outstanding_balance_after >= 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Balance After:</span>
                        <span>KES {{ number_format($payment->outstanding_balance_after, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($payment->status === 'completed')
                        <button class="btn btn-outline-primary" onclick="printReceipt()">
                            <i class="bi bi-printer me-1"></i>Print Receipt
                        </button>
                        @endif
                        
                        <a href="{{ route('student.payments.history') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-1"></i>All Payments
                        </a>
                        
                        @if($payment->enrollment)
                        <a href="{{ route('student.enrollments.show', $payment->enrollment->id) }}" class="btn btn-outline-info">
                            <i class="bi bi-book me-1"></i>View Enrollment
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($payment->payment_notes || $payment->admin_notes)
    <!-- Notes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    @if($payment->payment_notes)
                    <div class="mb-3">
                        <h6>Payment Notes:</h6>
                        <p class="text-muted">{{ $payment->payment_notes }}</p>
                    </div>
                    @endif
                    
                    @if($payment->admin_notes)
                    <div>
                        <h6>Administrative Notes:</h6>
                        <p class="text-muted">{{ $payment->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function printReceipt() {
    window.print();
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header, nav, footer {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .page-title {
        text-align: center;
        margin-bottom: 2rem;
    }
}
</style>
@endpush
@endsection