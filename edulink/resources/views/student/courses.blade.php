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
                <form id="enrollmentForm">
                    @csrf
                    <input type="hidden" id="courseId" name="course_id">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Course enrollment requires approval from the admissions office. 
                        You will be notified once your enrollment is processed.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Enrollment Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="enrollment_type" required>
                            <option value="">Select enrollment type</option>
                            <option value="new">New Student</option>
                            <option value="continuing">Continuing Student</option>
                            <option value="transfer">Transfer Student</option>
                            <option value="readmission">Readmission</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Plan <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_plan" required>
                            <option value="">Select payment plan</option>
                            <option value="full_payment">Full Payment</option>
                            <option value="installments">Installments (4 payments)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Semester (Optional)</label>
                        <select class="form-select" name="semester_id">
                            <option value="">Select semester (if applicable)</option>
                            <!-- Add semester options here if needed -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Preferred Start Date</label>
                        <input type="date" class="form-control" name="preferred_start_date" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Additional Comments (Optional)</label>
                        <textarea class="form-control" name="comments" rows="3" placeholder="Any additional information or special requests..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEnrollment()">Submit Enrollment Request</button>
            </div>
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
    
    document.getElementById('courseId').value = courseId;
    
    const courseDetails = `
        <div class="mb-3">
            <h6>${course.name}</h6>
            <p class="text-muted mb-1">Course Code: ${course.course_code}</p>
            <p class="text-muted mb-1">Duration: ${course.duration_months} months</p>
            <p class="text-muted mb-1">Fee: KES ${new Intl.NumberFormat().format(course.total_fee)}</p>
            ${course.department ? `<p class="text-muted mb-1">Department: ${course.department}</p>` : ''}
        </div>
    `;
    
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

function submitEnrollment() {
    const form = document.getElementById('enrollmentForm');
    const formData = new FormData(form);
    
    // Submit enrollment request via AJAX
    fetch('/student/enrollments', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('enrollmentModal')).hide();
            alert('Enrollment request submitted successfully! You will be notified once it is processed.');
            location.reload();
        } else {
            alert('Enrollment request failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting enrollment request');
    });
}
</script>
@endpush
