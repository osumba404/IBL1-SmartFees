@extends('layouts.student')

@section('title', 'Fees & Payments')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Fees & Payments</h2>
            <p class="page-subtitle">Manage your course fees and make payments</p>
        </div>
    </div>

    <!-- Fee Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($feeSummary['total_fees'], 2) }}</div>
                    <div class="stats-label">Total Fees</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($feeSummary['total_paid'], 2) }}</div>
                    <div class="stats-label">Amount Paid</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($feeSummary['total_pending'], 2) }}</div>
                    <div class="stats-label">Pending Amount</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($feeSummary['total_overdue'], 2) }}</div>
                    <div class="stats-label">Overdue Amount</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Enrollment Fee Details -->
    @if($currentEnrollment)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Enrollment - {{ $currentEnrollment->course->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Course Information</h6>
                            <p><strong>Course:</strong> {{ $currentEnrollment->course->name }}</p>
                            <p><strong>Course Code:</strong> {{ $currentEnrollment->course->course_code }}</p>
                            <p><strong>Duration:</strong> {{ $currentEnrollment->course->duration_months }} months</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $currentEnrollment->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($currentEnrollment->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Fee Information</h6>
                            @if($currentEnrollment->feeStructure)
                                <p><strong>Total Fee:</strong> KES {{ number_format($currentEnrollment->feeStructure->total_amount, 2) }}</p>
                                <p><strong>Amount Paid:</strong> KES {{ number_format($currentEnrollment->fees_paid ?? 0, 2) }}</p>
                                <p><strong>Outstanding:</strong> 
                                    <span class="text-{{ $currentEnrollment->outstanding_balance > 0 ? 'danger' : 'success' }}">
                                        KES {{ number_format($currentEnrollment->outstanding_balance, 2) }}
                                    </span>
                                </p>
                                @if($currentEnrollment->feeStructure->due_date)
                                    <p><strong>Due Date:</strong> {{ $currentEnrollment->feeStructure->due_date->format('M d, Y') }}</p>
                                @endif
                            @else
                                <p class="text-muted">No fee structure assigned</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($currentEnrollment->outstanding_balance > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="initiatePayment({{ $currentEnrollment->id }})">
                                    <i class="bi bi-credit-card me-1"></i>Make Payment
                                </button>
                                <button class="btn btn-outline-primary" onclick="viewPaymentPlan({{ $currentEnrollment->id }})">
                                    <i class="bi bi-calendar-check me-1"></i>Payment Plan
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- All Enrollments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Course Enrollments</h5>
                    <span class="badge bg-primary">{{ $enrollments->count() }} Enrollments</span>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Semester</th>
                                        <th>Total Fee</th>
                                        <th>Paid</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $enrollment->course->name }}</strong><br>
                                                    <small class="text-muted">{{ $enrollment->course->course_code }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($enrollment->semester)
                                                    {{ $enrollment->semester->name }}<br>
                                                    <small class="text-muted">{{ $enrollment->semester->start_date->format('M Y') }}</small>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $totalFees = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 50000);
                                                    $paidAmount = $enrollment->fees_paid;
                                                    $outstanding = $totalFees - $paidAmount;
                                                @endphp
                                                KES {{ number_format($totalFees, 2) }}
                                            </td>
                                            <td>
                                                <span class="text-success">
                                                    KES {{ number_format($paidAmount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-{{ $outstanding > 0 ? 'danger' : 'success' }}">
                                                    KES {{ number_format(max(0, $outstanding), 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'warning') }}">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($enrollment->outstanding_balance > 0)
                                                        <button class="btn btn-primary btn-sm" onclick="initiatePayment({{ $enrollment->id }})">
                                                            Pay Now
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            onclick="window.location.href='{{ route('student.enrollments.show', $enrollment->id) }}'">
                                                        Details
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-md-none">
                            @foreach($enrollments as $enrollment)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $enrollment->course->name }}</h6>
                                            <small class="text-muted">{{ $enrollment->course->course_code }}</small>
                                        </div>
                                        <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                    
                                    @if($enrollment->semester)
                                    <p class="card-text mb-2">
                                        <small class="text-muted">{{ $enrollment->semester->name }} - {{ $enrollment->semester->start_date->format('M Y') }}</small>
                                    </p>
                                    @endif
                                    
                                    @php
                                        $totalFees = $enrollment->total_fees_due > 0 ? $enrollment->total_fees_due : ($enrollment->course->total_fee ?? 50000);
                                        $paidAmount = $enrollment->fees_paid;
                                        $outstanding = $totalFees - $paidAmount;
                                    @endphp
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <div class="small text-muted">Total Fee</div>
                                            <div class="fw-bold">KES {{ number_format($totalFees, 2) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small text-muted">Paid</div>
                                            <div class="fw-bold text-success">KES {{ number_format($paidAmount, 2) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small text-muted">Outstanding</div>
                                            <div class="fw-bold text-{{ $outstanding > 0 ? 'danger' : 'success' }}">
                                                KES {{ number_format(max(0, $outstanding), 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        @if($enrollment->outstanding_balance > 0)
                                        <button class="btn btn-primary btn-sm" onclick="initiatePayment({{ $enrollment->id }})">
                                            <i class="bi bi-credit-card me-1"></i>Pay Now
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                onclick="window.location.href='{{ route('student.enrollments.show', $enrollment->id) }}'">
                                            <i class="bi bi-eye me-1"></i>Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No course enrollments found.</p>
                            <a href="{{ route('student.courses.index') }}" class="btn btn-primary">Browse Courses</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-sm-6 mb-3">
                            <div class="p-3 border rounded payment-method-card" onclick="window.location.href='{{ route('payment.create') }}'" style="cursor: pointer;">
                                <i class="bi bi-phone fs-1 text-success mb-2"></i>
                                <div class="fw-bold">M-Pesa</div>
                                <div class="text-muted small">Mobile Money</div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="p-3 border rounded payment-method-card" onclick="window.location.href='{{ route('payment.create') }}'" style="cursor: pointer;">
                                <i class="bi bi-credit-card fs-1 text-primary mb-2"></i>
                                <div class="fw-bold">Card Payment</div>
                                <div class="text-muted small">Visa, Mastercard</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.payments.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock-history me-1"></i>View Payment History
                        </a>
                        <a href="{{ route('student.statements.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text me-1"></i>Download Statements
                        </a>
                        <button class="btn btn-outline-primary" onclick="contactSupport()">
                            <i class="bi bi-headset me-1"></i>Contact Support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    @csrf
                    <input type="hidden" id="enrollmentId" name="enrollment_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" class="form-control" id="paymentAmount" name="amount" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="card">Credit/Debit Card</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="mpesaFields" style="display: none;">
                        <label class="form-label">M-Pesa Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" placeholder="254XXXXXXXXX">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processPayment()">Proceed to Pay</button>
            </div>
        </div>
    </div>
</div>

<!-- Fee Details Modal -->
<div class="modal fade" id="feeDetailsModal" tabindex="-1" aria-labelledby="feeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feeDetailsModalLabel">Fee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Enrollment Status:</strong> <span id="enrollmentStatus">-</span></p>
                        <p><strong>Payment Plan:</strong> <span id="paymentPlan">-</span></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Next Payment Due:</strong> <span id="nextPaymentDue">-</span></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Type</th>
                                <th class="text-end">Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody id="feeBreakdownBody">
                            <!-- Fee breakdown will be populated here by JavaScript -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total Amount</th>
                                <th class="text-end" id="totalAmount">-</th>
                            </tr>
                            <tr>
                                <th>Total Paid</th>
                                <th class="text-end" id="totalPaid">-</th>
                            </tr>
                            <tr class="table-active">
                                <th>Outstanding Balance</th>
                                <th class="text-end" id="outstandingBalance">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

@push('scripts')
<script>
// function initiatePayment(enrollmentId) {
//     document.getElementById('enrollmentId').value = enrollmentId;
function initiatePayment(enrollmentId) {
    window.location.href = '{{ route("payment.create") }}?enrollment=' + enrollmentId;
}
//     new bootstrap.Modal(document.getElementById('paymentModal')).show();
// }

function viewDetails(enrollmentId) {
    // Redirect to enrollment details or show details modal
    window.location.href = `/student/enrollments/${enrollmentId}`;
}

function viewPaymentPlan(enrollmentId) {
    // Show payment plan modal or redirect
    alert('Payment plan feature coming soon!');
}

function contactSupport() {
    // Open support contact modal or redirect
    alert('Contact support at support@edulink.ac.ke or +254 700 000 000');
}

function processPayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    
    // Process payment via AJAX
    fetch('{{ route("student.payments.initiate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert('Payment initiation failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing payment');
    });
}

// Show/hide payment method specific fields
document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
    const mpesaFields = document.getElementById('mpesaFields');
    if (this.value === 'mpesa') {
        mpesaFields.style.display = 'block';
    } else {
        mpesaFields.style.display = 'none';
    }
});







document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded'); // Debug log

    // Handle "Details" button click
    document.querySelectorAll('.btn-details').forEach(button => {
        console.log('Found button:', button); // Debug log
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Stop event bubbling
            console.log('Button clicked, enrollment ID:', this.dataset.enrollmentId); // Debug log

            const enrollmentId = this.dataset.enrollmentId;
            const url = `/student/enrollments/${enrollmentId}/fee-details`;
            console.log('Fetching URL:', url); // Debug log

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug log
                if (data.success) {
                    // Populate the modal with the fee details
                    document.getElementById('feeDetailsModalLabel').textContent = `Fee Details - ${data.course_name}`;
                    
                    // Build the fee breakdown HTML
                    let breakdownHtml = '';
                    for (const [key, value] of Object.entries(data.breakdown)) {
                        breakdownHtml += `
                            <tr>
                                <td>${key}</td>
                                <td class="text-end">KES ${value}</td>
                            </tr>
                        `;
                    }

                    // Update the modal body
                    document.getElementById('feeBreakdownBody').innerHTML = breakdownHtml;
                    
                    // Update the summary
                    document.getElementById('enrollmentStatus').textContent = data.enrollment_status;
                    document.getElementById('paymentPlan').textContent = data.payment_plan;
                    document.getElementById('totalAmount').textContent = `KES ${data.total_amount}`;
                    document.getElementById('totalPaid').textContent = `KES ${data.total_paid}`;
                    document.getElementById('outstandingBalance').textContent = `KES ${data.outstanding_balance}`;
                    document.getElementById('nextPaymentDue').textContent = data.next_payment_due;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('feeDetailsModal'));
                    modal.show();
                } else {
                    console.error('API error:', data.message); // Debug log
                    alert(data.message || 'Failed to load fee details. Please try again.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error); // Debug log
                alert('An error occurred while fetching fee details. Check the console for details.');
            });
        });
    });
});
</script>
@endpush
