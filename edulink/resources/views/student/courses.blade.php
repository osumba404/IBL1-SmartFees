@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">My Courses</h2>
            <p class="page-subtitle">View your enrolled courses and explore available programs</p>
        </div>
    </div>

    <!-- Enrolled Courses -->
    @if($student->enrollments()->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Enrolled Courses</h5>
                    <span class="badge bg-primary">{{ $student->enrollments()->count() }} Enrolled</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($student->enrollments as $enrollment)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title mb-0">{{ $enrollment->course->name }}</h6>
                                            <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : 'warning' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-muted small mb-2">{{ $enrollment->course->course_code }}</p>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">Duration:</small>
                                            <span class="fw-medium">{{ $enrollment->course->duration_months }} months</span>
                                        </div>
                                        
                                        @if($enrollment->course->description)
                                            <p class="card-text small text-muted">
                                                {{ Str::limit($enrollment->course->description, 100) }}
                                            </p>
                                        @endif
                                        
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    Enrolled: {{ $enrollment->created_at->format('M Y') }}
                                                </small>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('student.fees.index') }}" class="btn btn-outline-primary btn-sm">
                                                        View Fees
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Available Courses -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Available Courses</h5>
                    <span class="badge bg-info">{{ $availableCourses->count() }} Available</span>
                </div>
                <div class="card-body">
                    @if($availableCourses->count() > 0)
                        <div class="row">
                            @foreach($availableCourses as $course)
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card border h-100 {{ in_array($course->id, $enrolledCourses) ? 'border-success' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="card-title mb-0">{{ $course->name }}</h6>
                                                @if(in_array($course->id, $enrolledCourses))
                                                    <span class="badge bg-success">Enrolled</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Available</span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-muted small mb-2">{{ $course->course_code }}</p>
                                            
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <small class="text-muted">Duration:</small><br>
                                                    <span class="fw-medium">{{ $course->duration_months }} months</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Fee:</small><br>
                                                    <span class="fw-medium text-primary">KES {{ number_format($course->total_fee, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            @if($course->department)
                                                <div class="mb-2">
                                                    <small class="text-muted">Department:</small>
                                                    <span class="fw-medium">{{ $course->department }}</span>
                                                </div>
                                            @endif
                                            
                                            @if($course->level)
                                                <div class="mb-3">
                                                    <small class="text-muted">Level:</small>
                                                    <span class="fw-medium">{{ ucfirst($course->level) }}</span>
                                                </div>
                                            @endif
                                            
                                            @if($course->description)
                                                <p class="card-text small text-muted mb-3">
                                                    {{ Str::limit($course->description, 120) }}
                                                </p>
                                            @endif
                                            
                                            <div class="mt-auto">
                                                @if(in_array($course->id, $enrolledCourses))
                                                    <div class="d-grid">
                                                        <button class="btn btn-success btn-sm" disabled>
                                                            <i class="bi bi-check-circle me-1"></i>Already Enrolled
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-primary btn-sm" onclick="showEnrollmentModal({{ $course->id }})">
                                                            <i class="bi bi-plus-circle me-1"></i>Enroll Now
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm" onclick="viewCourseDetails({{ $course->id }})">
                                                            <i class="bi bi-info-circle me-1"></i>View Details
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Courses Available</h5>
                            <p class="text-muted">There are currently no active courses available for enrollment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Course Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="courseDetails"></div>
                
                <form id="enrollmentForm" action="{{ route('student.enrollments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="course_id" id="enrollmentCourseId">
                        
                        <div class="alert alert-info" role="alert">
                        You are enrolling for the current active semester: <strong>{{ $currentSemester?->name ?? 'N/A' }}</strong>
                        </div>

                        <div class="mb-3">
                            <label for="enrollment_type" class="form-label">Enrollment Type</label>
                            <select class="form-select" id="enrollment_type" name="enrollment_type" required>
                                <option value="new" selected>New Student</option>
                                <option value="continuing">Continuing Student</option>
                                <option value="transfer">Transfer</option>
                                <option value="readmission">Readmission</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_plan" class="form-label">Payment Plan</label>
                            <select class="form-select" id="payment_plan" name="payment_plan" required>
                                <option value="full_payment">Full Payment (5% Discount)</option>
                                <option value="installments">Installments</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="stripe">Credit/Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash Payment</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="program_fees" class="form-label">Program Fees (KES)</label>
                            <input type="number" class="form-control" id="program_fees" name="program_fees" 
                                   step="0.01" min="0" placeholder="Enter program fees" required readonly>
                            <div class="form-text">This will be automatically filled based on the selected course</div>
                        </div>

                        <div class="mb-3" id="initial_payment_section" style="display: none;">
                            <label for="initial_payment" class="form-label">Initial Payment Amount (KES)</label>
                            <input type="number" class="form-control" id="initial_payment" name="initial_payment" 
                                   step="0.01" min="0" placeholder="Enter initial payment amount">
                            <div class="form-text">For installment plans, enter the amount you want to pay now</div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submitEnrollment()">Submit & Pay Now</button>
                        </div>
                    </form>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEnrollment()">Submit Enrollment Request</button>
            </div> -->
        </div>
    </div>
</div>

<!-- Course Details Modal -->
<div class="modal fade" id="courseDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fullCourseDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="enrollFromDetails">Enroll in Course</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedCourse = null;

function showEnrollmentModal(courseId) {
    selectedCourse = @json($availableCourses->keyBy('id'));
    const course = selectedCourse[courseId];
    
    document.getElementById('enrollmentCourseId').value = courseId;
    
    const courseDetails = `
        <div class="mb-3">
            <h6>${course.name}</h6>
            <p class="text-muted mb-1">Course Code: ${course.course_code}</p>
            <p class="text-muted mb-1">Duration: ${course.duration_months} months</p>
            <p class="text-muted mb-1">Fee: KES ${new Intl.NumberFormat().format(course.total_fee)}</p>
            ${course.department ? `<p class="text-muted mb-1">Department: ${course.department}</p>` : ''}
        </div>
    `;
    
    // Set program fees automatically
    document.getElementById('program_fees').value = course.total_fee;
    
    // Reset payment plan to trigger fee calculation
    document.getElementById('payment_plan').value = 'full_payment';
    document.getElementById('payment_plan').dispatchEvent(new Event('change'));
    
    document.getElementById('courseDetails').innerHTML = courseDetails;
    new bootstrap.Modal(document.getElementById('enrollmentModal')).show();
}

function viewCourseDetails(courseId) {
    selectedCourse = @json($availableCourses->keyBy('id'));
    const course = selectedCourse[courseId];
    
    const fullDetails = `
        <div class="row">
            <div class="col-md-8">
                <h5>${course.name}</h5>
                <p class="text-muted">${course.course_code}</p>
                ${course.description ? `<p>${course.description}</p>` : ''}
                
                <h6 class="mt-4">Course Information</h6>
                <ul class="list-unstyled">
                    <li><strong>Duration:</strong> ${course.duration_months} months</li>
                    ${course.department ? `<li><strong>Department:</strong> ${course.department}</li>` : ''}
                    ${course.level ? `<li><strong>Level:</strong> ${course.level}</li>` : ''}
                    <li><strong>Status:</strong> <span class="badge bg-success">${course.status}</span></li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary">KES ${new Intl.NumberFormat().format(course.total_fee)}</h4>
                        <p class="text-muted mb-0">Total Course Fee</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('fullCourseDetails').innerHTML = fullDetails;
    document.getElementById('enrollFromDetails').onclick = () => {
        bootstrap.Modal.getInstance(document.getElementById('courseDetailsModal')).hide();
        showEnrollmentModal(courseId);
    };
    
    new bootstrap.Modal(document.getElementById('courseDetailsModal')).show();
}

// Handle payment plan changes
document.addEventListener('DOMContentLoaded', function() {
    const paymentPlanSelect = document.getElementById('payment_plan');
    const initialPaymentSection = document.getElementById('initial_payment_section');
    const programFeesInput = document.getElementById('program_fees');
    const initialPaymentInput = document.getElementById('initial_payment');
    
    if (paymentPlanSelect) {
        paymentPlanSelect.addEventListener('change', function() {
            const selectedPlan = this.value;
            const courseFee = parseFloat(programFeesInput.value) || 0;
            
            if (selectedPlan === 'full_payment') {
                // Apply 5% discount for full payment
                const discountedFee = courseFee * 0.95;
                programFeesInput.value = discountedFee.toFixed(2);
                initialPaymentSection.style.display = 'none';
                initialPaymentInput.required = false;
            } else if (selectedPlan === 'installments') {
                // Reset to original fee for installments
                if (selectedCourse && document.getElementById('enrollmentCourseId').value) {
                    const courseId = document.getElementById('enrollmentCourseId').value;
                    const course = selectedCourse[courseId];
                    programFeesInput.value = course.total_fee;
                }
                initialPaymentSection.style.display = 'block';
                initialPaymentInput.required = true;
                
                // Set minimum initial payment (25% of total fee)
                const minPayment = courseFee * 0.25;
                initialPaymentInput.min = minPayment.toFixed(2);
                initialPaymentInput.placeholder = `Minimum: KES ${minPayment.toFixed(2)}`;
            }
        });
    }
});

function submitEnrollment() {
    const form = document.getElementById('enrollmentForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const paymentMethod = formData.get('payment_method');
    const programFees = formData.get('program_fees');
    const paymentPlan = formData.get('payment_plan');
    const initialPayment = formData.get('initial_payment');
    
    if (!paymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    if (!programFees || programFees <= 0) {
        alert('Invalid program fees');
        return;
    }
    
    if (paymentPlan === 'installments' && (!initialPayment || initialPayment <= 0)) {
        alert('Please enter initial payment amount for installment plan');
        return;
    }
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Submit enrollment request via AJAX
    fetch('{{ route('student.enrollments.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => {
            if (!response.ok) {
                console.log('Error response data:', data);
                throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
            }
            return data;
        });
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('enrollmentModal')).hide();
            
            if (data.redirect_url) {
                // Show success message and redirect to payment
                alert('Enrollment submitted successfully! Redirecting to payment...');
                window.location.href = data.redirect_url;
            } else {
                alert('Enrollment request submitted successfully!');
                location.reload();
            }
        } else {
            console.error('Enrollment failed:', data);
            alert('Enrollment request failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while submitting enrollment request: ' + error.message);
    });
}
</script>
@endpush
