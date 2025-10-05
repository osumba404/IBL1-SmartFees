@extends('layouts.student')

@section('title', 'Enrollment Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Enrollment Details</h1>
                <a href="{{ route('student.enrollments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Enrollments
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Enrollment Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Enrollment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Enrollment Number:</strong><br>
                                <span class="text-muted">{{ $enrollment->enrollment_number ?? 'N/A' }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Course:</strong><br>
                                <span class="text-muted">{{ $enrollment->course->name ?? 'N/A' }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Semester:</strong><br>
                                <span class="text-muted">{{ $enrollment->semester->name ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Enrollment Date:</strong><br>
                                <span class="text-muted">{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : 'N/A' }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'warning') }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </p>
                            @if($enrollment->status_change_reason)
                            <p class="mb-2">
                                <strong>Status Reason:</strong><br>
                                <span class="text-muted">{{ $enrollment->status_change_reason }}</span>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Fee Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Fee Type</th>
                                    <th class="text-end">Amount (KSh)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($enrollment->feeStructure)
                                    @foreach($enrollment->feeStructure->getAttributes() as $key => $value)
                                        @if(str_ends_with($key, '_fee') && is_numeric($value) && $value > 0)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', substr($key, 0, -4))) }}</td>
                                                <td class="text-end">{{ number_format($value, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="table-active">
                                        <th>Total Fees</th>
                                        <th class="text-end">{{ number_format($enrollment->feeStructure->total_amount, 2) }}</th>
                                    </tr>
                                @else
                                    @php
                                        $courseFee = $enrollment->course->total_fee ?? 50000;
                                    @endphp
                                    <tr>
                                        <td>Course Fee</td>
                                        <td class="text-end">{{ number_format($courseFee, 2) }}</td>
                                    </tr>
                                    <tr class="table-active">
                                        <th>Total Fees</th>
                                        <th class="text-end">{{ number_format($courseFee, 2) }}</th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment History</h5>
                    @if(($enrollment->outstanding_balance ?? 0) > 0)
                        <button class="btn btn-light btn-sm" onclick="initiatePayment({{ $enrollment->id }})">
                            <i class="bi bi-credit-card me-1"></i> Make Payment
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($enrollment->payments && $enrollment->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th class="text-end">Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollment->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>{{ $payment->reference_number }}</td>
                                            <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('student.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="bi bi-receipt-cutoff fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No payment history found for this enrollment.</p>
                            @if(($enrollment->outstanding_balance ?? 0) > 0)
                                <button class="btn btn-primary" onclick="initiatePayment({{ $enrollment->id }})">
                                    <i class="bi bi-credit-card me-1"></i> Make First Payment
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalFees = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 50000);
                        $paidAmount = $enrollment->fees_paid ?? 0;
                        $outstanding = $totalFees - $paidAmount;
                    @endphp
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Fees:</span>
                        <strong>KSh {{ number_format($totalFees, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Amount Paid:</span>
                        <strong class="text-success">KSh {{ number_format($paidAmount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Outstanding Balance:</span>
                        <strong class="{{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">
                            KSh {{ number_format(max(0, $outstanding), 2) }}
                        </strong>
                    </div>
                    @if($enrollment->payment_plan === 'installment' && $enrollment->installment_amount > 0)
                        <div class="d-flex justify-content-between mb-3">
                            <span>Next Installment:</span>
                            <strong>KSh {{ number_format($enrollment->installment_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Due Date:</span>
                            <strong>{{ $enrollment->next_payment_due ? $enrollment->next_payment_due->format('M d, Y') : 'N/A' }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    @if(($enrollment->outstanding_balance ?? 0) > 0)
                        <button class="list-group-item list-group-item-action" onclick="initiatePayment({{ $enrollment->id }})">
                            <i class="bi bi-credit-card me-2"></i> Make Payment
                        </button>
                    @endif
                    <a href="{{ route('student.statements.download', ['enrollment_id' => $enrollment->id]) }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-download me-2"></i> Download Fee Statement
                    </a>
                    @if($enrollment->status === 'active' && $enrollment->is_deferred === false)
                        <button class="list-group-item list-group-item-action text-danger" data-bs-toggle="modal" data-bs-target="#deferEnrollmentModal">
                            <i class="bi bi-pause-circle me-2"></i> Request Deferment
                        </button>
                    @endif
                    @if($enrollment->status === 'deferred')
                        <button class="list-group-item list-group-item-action text-primary" data-bs-toggle="modal" data-bs-target="#resumeEnrollmentModal">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Resume Studies
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deferment Request Modal -->
<div class="modal fade" id="deferEnrollmentModal" tabindex="-1" aria-labelledby="deferEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('student.enrollments.defer', $enrollment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deferEnrollmentModalLabel">Request Enrollment Deferment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="defermentReason" class="form-label">Reason for Deferment</label>
                        <textarea class="form-control" id="defermentReason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="defermentStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="defermentStartDate" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="defermentEndDate" class="form-label">Expected Return Date</label>
                            <input type="date" class="form-control" id="defermentEndDate" name="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initiatePayment(enrollmentId) {
        // Implement your payment initiation logic here
        // This could open a payment modal or redirect to payment page
        console.log('Initiating payment for enrollment:', enrollmentId);
        // Example: window.location.href = '/student/payments/create?enrollment=' + enrollmentId;
    }

    // Initialize date pickers
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const startDateInput = document.getElementById('defermentStartDate');
        const endDateInput = document.getElementById('defermentEndDate');
        
        if (startDateInput) {
            startDateInput.min = today.toISOString().split('T')[0];
            startDateInput.addEventListener('change', function() {
                if (endDateInput) {
                    endDateInput.min = this.value;
                    if (endDateInput.value && new Date(endDateInput.value) < new Date(this.value)) {
                        endDateInput.value = this.value;
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection