@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Payment Details</h1>
            <p class="mb-0 text-muted">View payment information and transaction details</p>
        </div>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Payment ID:</strong></td>
                                    <td>#{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reference:</strong></td>
                                    <td>{{ $payment->payment_reference }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transaction ID:</strong></td>
                                    <td>{{ $payment->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><span class="h5 text-success">KSh {{ number_format($payment->amount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>
                                        <span class="badge badge-secondary">
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
                                                <span class="badge badge-success">Completed</span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">Failed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-secondary">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge badge-light">{{ ucfirst($payment->status) }}</span>
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
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>
                                        @else
                                            <span class="text-warning"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $payment->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    @if($payment->student)
                    <div class="text-center mb-3">
                        <h5>{{ $payment->student->full_name }}</h5>
                        <p class="text-muted">{{ $payment->student->student_id }}</p>
                    </div>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $payment->student->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $payment->student->phone ?? 'N/A' }}</td>
                        </tr>
                        @if($payment->enrollment)
                        <tr>
                            <td><strong>Course:</strong></td>
                            <td>{{ $payment->enrollment->course->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Semester:</strong></td>
                            <td>{{ $payment->enrollment->semester->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                    </table>
                    @else
                    <p class="text-muted">Student information not available</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    @if($payment->status === 'pending' && !$payment->is_verified)
                    <button type="button" class="btn btn-success btn-block mb-2" onclick="verifyPayment({{ $payment->id }})">
                        <i class="fas fa-check"></i> Verify Payment
                    </button>
                    @endif
                    
                    @if($payment->status === 'completed')
                    <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#refundModal">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                    @endif
                    
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> All Payments
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($payment->payment_notes || $payment->admin_notes)
    <!-- Notes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                </div>
                <div class="card-body">
                    @if($payment->payment_notes)
                    <div class="mb-3">
                        <h6>Payment Notes:</h6>
                        <p>{{ $payment->payment_notes }}</p>
                    </div>
                    @endif
                    
                    @if($payment->admin_notes)
                    <div>
                        <h6>Admin Notes:</h6>
                        <p>{{ $payment->admin_notes }}</p>
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
function verifyPayment(paymentId) {
    if (confirm('Are you sure you want to verify this payment?')) {
        fetch(`/admin/payments/${paymentId}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while verifying the payment.');
        });
    }
}
</script>
@endpush
@endsection