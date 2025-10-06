@extends('layouts.admin')

@section('title', 'Payment Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Payment Management</h1>
            <p class="mb-0 text-muted">Manage student payments, transactions, and financial records</p>
        </div>
        <div>
            <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Payment
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payments Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ config('services.college.currency_symbol', 'KSh') }} {{ number_format($todayPayments ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $failedCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Payments</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="">All Methods</option>
                        <option value="mpesa" {{ request('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Credit Card</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments ?? [] as $payment)
                        <tr>
                            <td>
                                <span class="font-weight-bold">#{{ $payment->id }}</span>
                                @if($payment->payment_reference)
                                <br><small class="text-muted">{{ $payment->payment_reference }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $payment->student->full_name ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $payment->student->student_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $enrollment = $payment->enrollment ?? $payment->studentEnrollment;
                                    $course = $enrollment ? $enrollment->course : null;
                                @endphp
                                @if($course)
                                    <div class="font-weight-bold">{{ $course->name }}</div>
                                    <div class="text-muted small">{{ $course->code }}</div>
                                @else
                                    <span class="text-muted">No Course</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ config('services.college.currency_symbol', 'KSh') }} {{ number_format($payment->amount, 2) }}</span>
                                @if($payment->currency && $payment->currency !== 'KES')
                                <br><small class="text-muted">{{ $payment->currency }}</small>
                                @endif
                            </td>
                            <td>
                                @if($payment->payment_method)
                                    <span class="badge badge-info">{{ $payment->payment_method_display }}</span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $payment->status_color }}">{{ ucfirst($payment->status) }}</span>
                            </td>
                            <td>
                                <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($payment->status === 'pending' && (auth('admin')->user()->canManagePayments() || auth('admin')->user()->isSuperAdmin()))
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="verifyPayment({{ $payment->id }})">
                                        <i class="fas fa-check"></i> Verify
                                    </button>
                                    @endif
                                </div>tEnrollment;
                                    $course = $enrollment->course ?? null;
                                    $semester = $enrollment->semester ?? null;
                                @endphp
                                @if($course)
                                    {{ $course->name }}
                                    @if($semester)
                                    <br><small class="text-muted">{{ $semester->name }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">No Course</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-weight-bold">
                                    {{ config('services.college.currency_symbol', 'KSh') }} {{ number_format($payment->amount, 2) }}
                                </span>
                                @if($payment->payment_type)
                                <br><small class="text-muted">{{ ucfirst($payment->payment_type) }} Fee</small>
                                @endif
                            </td>
                            <td class="text-dark">
                                {{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : 'Unknown' }}
                            </td>
                            <td class="text-dark">
                                {{ $payment->status ? ucfirst($payment->status) : 'Unknown' }}
                            </td>
                            <td>
                                <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm" role="group" style="min-width: 120px;">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline-primary btn-sm mb-1" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($payment->status === 'pending' && !$payment->is_verified)
                                    <button type="button" class="btn btn-outline-success btn-sm mb-1" onclick="verifyPayment({{ $payment->id }})" title="Verify Payment">
                                        <i class="fas fa-check"></i> Verify
                                    </button>
                                    @endif
                                    @if($payment->status === 'completed')
                                    <button type="button" class="btn btn-outline-warning btn-sm mb-1" onclick="showRefundModal({{ $payment->id }}, {{ $payment->amount }})" title="Process Refund">
                                        <i class="fas fa-undo"></i> Refund
                                    </button>
                                    @endif
                                    @if(in_array($payment->status, ['pending', 'failed']))
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="cancelPayment({{ $payment->id }})" title="Cancel Payment">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-credit-card fa-3x mb-3"></i>
                                    <p>No payments found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($payments) && method_exists($payments, 'links'))
            <div class="d-flex justify-content-center">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refund_amount">Refund Amount</label>
                        <input type="number" class="form-control" id="refund_amount" name="refund_amount" 
                               step="0.01" required>
                        <small class="form-text text-muted" id="maxRefundText"></small>
                    </div>
                    <div class="form-group">
                        <label for="refund_reason">Refund Reason</label>
                        <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// DataTable initialization removed to avoid jQuery dependency

function verifyPayment(paymentId) {
    if (confirm('Are you sure you want to verify this payment?')) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/admin/payments/${paymentId}/verify`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Verify response:', response);
            location.reload();
        })
        .catch(error => {
            console.log('Verify error:', error);
            alert('Error verifying payment');
        });
    }
}

function showRefundModal(paymentId, amount) {
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('refund_amount').max = amount;
    document.getElementById('refund_amount').value = '';
    document.getElementById('refund_reason').value = '';
    document.getElementById('maxRefundText').textContent = `Maximum refund: KSh ${amount.toLocaleString()}`;
    
    // Show modal using Bootstrap's modal method
    const modal = new bootstrap.Modal(document.getElementById('refundModal'));
    modal.show();
}

function cancelPayment(paymentId) {
    if (confirm('Are you sure you want to cancel this payment?')) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('payment_ids[]', paymentId);
        formData.append('status', 'cancelled');
        
        fetch('/admin/payments/bulk-update', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Cancel response:', response);
            location.reload();
        })
        .catch(error => {
            console.log('Cancel error:', error);
            alert('Error cancelling payment');
        });
    }
}

// Handle refund form submission
document.addEventListener('DOMContentLoaded', function() {
    const refundForm = document.getElementById('refundForm');
    if (refundForm) {
        refundForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Refund response:', response);
                const modal = bootstrap.Modal.getInstance(document.getElementById('refundModal'));
                modal.hide();
                location.reload();
            })
            .catch(error => {
                console.log('Refund error:', error);
                alert('Error processing refund');
            });
        });
    }
})
</script>
@endpush
@endsection
