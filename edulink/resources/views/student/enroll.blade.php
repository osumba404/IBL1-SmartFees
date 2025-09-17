@extends('layouts.student')

@section('title', 'Enroll in a Course')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .course-card {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
        height: 100%;
    }
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .course-header {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: white;
        padding: 15px;
    }
    .course-body {
        padding: 20px;
    }
    .enrollment-form {
        background: #f9f9f9;
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #eee;
    }
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        padding: 5px 10px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .enrollment-steps {
        position: relative;
        padding-bottom: 30px;
        margin-bottom: 30px;
    }
    .enrollment-steps::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    .step {
        position: relative;
        text-align: center;
        z-index: 2;
    }
    .step-number {
        width: 40px;
        height: 40px;
        line-height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        font-weight: bold;
        margin: 0 auto 10px;
        position: relative;
    }
    .step.active .step-number {
        background: #4e54c8;
        color: white;
    }
    .step.completed .step-number {
        background: #28a745;
        color: white;
    }
    .step-title {
        font-size: 14px;
        color: #6c757d;
    }
    .step.active .step-title {
        color: #4e54c8;
        font-weight: 600;
    }
    .step.completed .step-title {
        color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Enroll in a Course</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enroll</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="enrollment-steps">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="step active" id="step-1">
                    <div class="step-number">1</div>
                    <div class="step-title">Select Course</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="step" id="step-2">
                    <div class="step-number">2</div>
                    <div class="step-title">Enrollment Details</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="step" id="step-3">
                    <div class="step-number">3</div>
                    <div class="step-title">Payment Plan</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="step" id="step-4">
                    <div class="step-number">4</div>
                    <div class="step-title">Confirm & Submit</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Step 1: Select Course -->
            <div id="select-course" class="step-content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Available Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($availableCourses->isEmpty())
                            <div class="alert alert-info">
                                No courses are currently available for enrollment. Please check back later.
                            </div>
                        @else
                            <div class="row">
                                @foreach($availableCourses as $course)
                                    <div class="col-md-6 mb-4">
                                        <div class="course-card">
                                            <div class="course-header">
                                                <h5 class="mb-0">{{ $course->name }}</h5>
                                                <small class="d-block">{{ $course->course_code }} • {{ ucfirst($course->level) }}</small>
                                            </div>
                                            <div class="course-body">
                                                <p class="text-muted">
                                                    <i class="fas fa-info-circle"></i> {{ Str::limit($course->description, 150) }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-clock"></i> {{ $course->duration_months }} months
                                                    </span>
                                                    <span class="text-primary fw-bold">
                                                        KSh {{ number_format($course->total_fee, 2) }}
                                                    </span>
                                                </div>
                                                <button class="btn btn-primary w-100 mt-3 select-course" 
                                                        data-course-id="{{ $course->id }}">
                                                    Select Course
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Step 2: Enrollment Details -->
            <div id="enrollment-details" class="step-content d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Enrollment Details</h5>
                    </div>
                    <div class="card-body">
                        <form id="enrollment-form">
                            @csrf
                            <input type="hidden" name="course_id" id="course_id">
                            <input type="hidden" name="semester_id" id="semester_id" value="{{ $currentSemester?->id }}">
                            
                            @if($currentSemester)
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Current Registration Period:</strong> {{ $currentSemester->name }} ({{ $currentSemester->academic_year }})
                                    <br>
                                    <small>Registration Period: {{ $currentSemester->registration_start_date->format('F j, Y') }} - {{ $currentSemester->registration_end_date->format('F j, Y') }}</small>
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    No active registration period available. Please contact the administration office.
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="enrollment_type" class="form-label">Enrollment Type <span class="text-danger">*</span></label>
                                <select name="enrollment_type" id="enrollment_type" class="form-select" required>
                                    <option value="">-- Select Enrollment Type --</option>
                                    <option value="new">New Student</option>
                                    <option value="continuing">Continuing Student</option>
                                    <option value="transfer">Transfer Student</option>
                                    <option value="readmission">Readmission</option>
                                </select>
                                <div class="invalid-feedback">Please select an enrollment type.</div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Please ensure all details are correct before proceeding.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="back-to-courses">
                                    <i class="fas fa-arrow-left"></i> Back to Courses
                                </button>
                                <button type="button" class="btn btn-primary" id="proceed-to-payment-plan">
                                    Next: Payment Plan <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Step 3: Payment Plan -->
            <div id="payment-plan" class="step-content d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Select Payment Plan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check payment-plan-option">
                                    <input class="form-check-input" type="radio" name="payment_plan" id="full_payment" value="full_payment" checked>
                                    <label class="form-check-label fw-bold" for="full_payment">
                                        Full Payment
                                    </label>
                                    <div class="ms-4 mt-2">
                                        <p class="mb-1">Pay the full course fee at once and get a 5% discount.</p>
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check-circle"></i> Save on total cost
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check payment-plan-option">
                                    <input class="form-check-input" type="radio" name="payment_plan" id="installments" value="installments">
                                    <label class="form-check-label fw-bold" for="installments">
                                        Installment Plan
                                    </label>
                                    <div class="ms-4 mt-2">
                                        <p class="mb-1">Pay in monthly installments over the course duration.</p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-info-circle"></i> Additional 2% processing fee applies
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Installment Details (Shown when installments are selected) -->
                        <div id="installment-details" class="bg-light p-3 rounded mb-4 d-none">
                            <h6 class="mb-3">Installment Plan Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Installment</th>
                                            <th>Due Date</th>
                                            <th class="text-end">Amount (KSh)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="installment-schedule">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Total:</th>
                                            <th class="text-end" id="total-amount">KSh 0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" id="back-to-details">
                                <i class="fas fa-arrow-left"></i> Back to Details
                            </button>
                            <button type="button" class="btn btn-primary" id="review-enrollment">
                                Review Enrollment <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div id="review-enrollment" class="step-content d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Review Your Enrollment</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please review your enrollment details before submitting.
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Course Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Course:</th>
                                        <td id="review-course">-</td>
                                    </tr>
                                    <tr>
                                        <th>Semester:</th>
                                        <td id="review-semester">-</td>
                                    </tr>
                                    <tr>
                                        <th>Enrollment Type:</th>
                                        <td id="review-enrollment-type">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Payment Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Payment Plan:</th>
                                        <td id="review-payment-plan">-</td>
                                    </tr>
                                    <tr id="review-installment-row" class="d-none">
                                        <th>Installments:</th>
                                        <td id="review-installments">-</td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount:</th>
                                        <td class="fw-bold" id="review-total-amount">-</td>
                                    </tr>
                                    <tr id="review-discount-row" class="table-success d-none">
                                        <th>Discount (5%):</th>
                                        <td class="text-success" id="review-discount">-</td>
                                    </tr>
                                    <tr id="review-processing-fee-row" class="table-warning d-none">
                                        <th>Processing Fee (2%):</th>
                                        <td class="text-warning" id="review-processing-fee">-</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Amount Due Now:</th>
                                        <td class="fw-bold text-primary" id="review-amount-due">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms_agreement" required>
                            <label class="form-check-label" for="terms_agreement">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and 
                                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a> of Edulink International College.
                            </label>
                            <div class="invalid-feedback">You must agree to the terms and conditions to continue.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="back-to-payment">
                                <i class="fas fa-arrow-left"></i> Back to Payment
                            </button>
                            <button type="button" class="btn btn-success" id="submit-enrollment" disabled>
                                <i class="fas fa-check-circle"></i> Submit Enrollment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Enrollment Summary</h5>
                </div>
                <div class="card-body">
                    <div id="summary-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Select a course to begin</p>
                    </div>
                    <div id="summary-content" class="d-none">
                        <div class="text-center mb-3">
                            <img src="{{ asset('images/course-placeholder.jpg') }}" alt="Course Image" class="img-fluid rounded mb-2" style="max-height: 150px;">
                            <h5 id="summary-course-name">-</h5>
                            <p class="text-muted mb-2" id="summary-course-code">-</p>
                            <p class="mb-0" id="summary-course-duration">-</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <h6>Fee Breakdown</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Tuition Fee:</td>
                                    <td class="text-end" id="summary-tuition-fee">-</td>
                                </tr>
                                <tr>
                                    <td>Registration Fee:</td>
                                    <td class="text-end" id="summary-registration-fee">-</td>
                                </tr>
                                <tr>
                                    <td>Examination Fee:</td>
                                    <td class="text-end" id="summary-exam-fee">-</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total Fees:</td>
                                    <td class="text-end" id="summary-total-fees">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Payment is required to complete your enrollment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Enrollment Agreement</h6>
                <p>By enrolling in a course at Edulink International College, you agree to abide by the college's rules and regulations as outlined in the student handbook and other official documents.</p>
                
                <h6>2. Payment Terms</h6>
                <p>All fees must be paid according to the selected payment plan. Late payments may incur additional charges as specified in the fee policy.</p>
                
                <h6>3. Refund Policy</h6>
                <p>Refunds are processed according to the college's refund policy. Please refer to the student handbook for details on eligibility and procedures.</p>
                
                <h6>4. Code of Conduct</h6>
                <p>Students are expected to maintain high standards of behavior and academic integrity. Violations may result in disciplinary action.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Information Collection</h6>
                <p>We collect personal information to process your enrollment and provide educational services. This includes contact details, academic records, and payment information.</p>
                
                <h6>2. Use of Information</h6>
                <p>Your information is used for academic administration, communication, and institutional research. We do not sell or share your personal information with third parties except as required by law.</p>
                
                <h6>3. Data Security</h6>
                <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                
                <h6>4. Your Rights</h6>
                <p>You have the right to access, correct, or request deletion of your personal information in accordance with applicable data protection laws.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Enrollment Successful!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4>Thank You for Enrolling!</h4>
                <p>Your enrollment has been successfully submitted. You will receive a confirmation email with further instructions.</p>
                <p>Your Enrollment Number: <strong id="enrollment-number">-</strong></p>
                <div class="alert alert-info text-start">
                    <i class="fas fa-info-circle"></i> Please proceed to make your payment to complete the enrollment process.
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="#" class="btn btn-primary" id="proceed-to-payment-btn">
                    <i class="fas fa-credit-card"></i> Proceed to Payment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Course data from the server
    const courses = {!! json_encode($availableCourses->map(function($course) {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'code' => $course->course_code,
            'description' => $course->description,
            'duration' => $course->duration_months,
            'level' => $course->level,
            'total_fee' => floatval($course->total_fee),
            'registration_fee' => floatval($course->registration_fee ?? 0),
            'examination_fee' => floatval($course->examination_fee ?? 0),
            'library_fee' => floatval($course->library_fee ?? 0),
            'lab_fee' => floatval($course->lab_fee ?? 0),
            'max_installments' => intval($course->max_installments ?? 4)
        ];
    })) !!};

    // Current semester data (auto-selected)
    const currentSemester = {!! json_encode($currentSemester ? [
        'id' => $currentSemester->id,
        'name' => $currentSemester->name,
        'academic_year' => $currentSemester->academic_year,
        'start_date' => $currentSemester->start_date,
        'end_date' => $currentSemester->end_date
    ] : null) !!};

    // Current state
    let currentStep = 1;
    let selectedCourse = null;
    let enrollmentData = {
        course_id: null,
        semester_id: currentSemester ? currentSemester.id : null,
        enrollment_type: null,
        payment_plan: 'full_payment',
        total_fees: 0,
        discount: 0,
        processing_fee: 0,
        amount_due: 0,
        installments: []
    };

    // Format currency
    function formatCurrency(amount) {
        return 'KSh ' + parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Calculate payment plan
    function calculatePaymentPlan() {
        if (!selectedCourse) return;

        const paymentPlan = $('input[name="payment_plan"]:checked').val();
        enrollmentData.payment_plan = paymentPlan;
        enrollmentData.total_fees = selectedCourse.total_fee + 
                                  selectedCourse.registration_fee + 
                                  selectedCourse.examination_fee + 
                                  selectedCourse.library_fee + 
                                  selectedCourse.lab_fee;

        // Reset values
        enrollmentData.discount = 0;
        enrollmentData.processing_fee = 0;
        enrollmentData.installments = [];

        if (paymentPlan === 'full_payment') {
            // 5% discount for full payment
            enrollmentData.discount = enrollmentData.total_fees * 0.05;
            enrollmentData.amount_due = enrollmentData.total_fees - enrollmentData.discount;
        } else {
            // 2% processing fee for installments
            enrollmentData.processing_fee = enrollmentData.total_fees * 0.02;
            const totalWithFee = enrollmentData.total_fees + enrollmentData.processing_fee;
            
            // Calculate installments (up to max_installments or 4, whichever is smaller)
            const numInstallments = Math.min(selectedCourse.max_installments || 4, 12);
            const installmentAmount = totalWithFee / numInstallments;
            
            // Generate installment schedule
            const today = new Date();
            for (let i = 0; i < numInstallments; i++) {
                const dueDate = new Date(today);
                dueDate.setMonth(today.getMonth() + i + 1);
                
                enrollmentData.installments.push({
                    number: i + 1,
                    due_date: dueDate.toISOString().split('T')[0],
                    amount: i === 0 ? installmentAmount + (totalWithFee - (installmentAmount * numInstallments)) : installmentAmount
                });
            }
            
            enrollmentData.amount_due = enrollmentData.installments[0].amount;
        }
        
        updateInstallmentSchedule();
        updateReviewSection();
    }

    // Update installment schedule in the UI
    function updateInstallmentSchedule() {
        const $installmentTable = $('#installment-schedule');
        $installmentTable.empty();
        
        if (enrollmentData.payment_plan === 'installments' && enrollmentData.installments.length > 0) {
            enrollmentData.installments.forEach(installment => {
                $installmentTable.append(`
                    <tr>
                        <td>${installment.number}</td>
                        <td>${new Date(installment.due_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</td>
                        <td class="text-end">${formatCurrency(installment.amount)}</td>
                    </tr>
                `);
            });
            
            $('#total-amount').text(formatCurrency(enrollmentData.total_fees + enrollmentData.processing_fee));
            $('#installment-details').removeClass('d-none');
        } else {
            $('#installment-details').addClass('d-none');
        }
    }

    // Update review section with current data
    function updateReviewSection() {
        if (!selectedCourse) return;
        
        // Course info
        $('#review-course').text(selectedCourse.name);
        if (currentSemester) {
            $('#review-semester').text(currentSemester.name + ' (' + currentSemester.academic_year + ')');
        } else {
            $('#review-semester').text('No active semester');
        }
        $('#review-enrollment-type').text($('#enrollment_type option:selected').text());
        
        // Payment info
        const paymentPlan = $('input[name="payment_plan"]:checked').val();
        $('#review-payment-plan').text(paymentPlan === 'full_payment' ? 'Full Payment' : 'Installment Plan');
        
        if (paymentPlan === 'installments') {
            $('#review-installment-row').removeClass('d-none');
            $('#review-installments').text(enrollmentData.installments.length + ' installments');
            $('#review-processing-fee-row').removeClass('d-none');
            $('#review-discount-row').addClass('d-none');
        } else {
            $('#review-installment-row').addClass('d-none');
            $('#review-discount-row').removeClass('d-none');
            $('#review-processing-fee-row').addClass('d-none');
        }
        
        $('#review-total-amount').text(formatCurrency(enrollmentData.total_fees));
        $('#review-discount').text('-' + formatCurrency(enrollmentData.discount));
        $('#review-processing-fee').text('+' + formatCurrency(enrollmentData.processing_fee));
        $('#review-amount-due').text(formatCurrency(enrollmentData.amount_due));
    }

    // Update summary card
    function updateSummary() {
        if (!selectedCourse) {
            $('#summary-loading').removeClass('d-none');
            $('#summary-content').addClass('d-none');
            return;
        }
        
        $('#summary-course-name').text(selectedCourse.name);
        $('#summary-course-code').text(selectedCourse.code);
        $('#summary-course-duration').html(`
            <i class="fas fa-calendar-alt me-1"></i> ${selectedCourse.duration} months • 
            <i class="fas fa-graduation-cap me-1"></i> ${selectedCourse.level}
        `);
        
        $('#summary-tuition-fee').text(formatCurrency(selectedCourse.total_fee));
        $('#summary-registration-fee').text(formatCurrency(selectedCourse.registration_fee));
        $('#summary-exam-fee').text(formatCurrency(selectedCourse.examination_fee));
        
        const totalFees = selectedCourse.total_fee + 
                         selectedCourse.registration_fee + 
                         selectedCourse.examination_fee + 
                         selectedCourse.library_fee + 
                         selectedCourse.lab_fee;
        
        $('#summary-total-fees').text(formatCurrency(totalFees));
        
        // Hide loading and show content
        $('#summary-loading').addClass('d-none');
        $('#summary-content').removeClass('d-none');
    }

    // Navigate to a specific step
    function goToStep(step) {
        $('.step-content').addClass('d-none');
        $('.step').removeClass('active completed');
        
        // Mark previous steps as completed
        for (let i = 1; i < step; i++) {
            $(`#step-${i}`).addClass('completed');
        }
        
        // Set current step as active
        $(`#step-${step}`).addClass('active');
        
        // Show the corresponding content
        let contentId = '';
        switch(step) {
            case 1:
                contentId = 'select-course';
                break;
            case 2:
                contentId = 'enrollment-details';
                break;
            case 3:
                contentId = 'payment-plan';
                // Recalculate payment plan when showing this step
                calculatePaymentPlan();
                break;
            case 4:
                contentId = 'review-enrollment';
                // Update review section with latest data
                updateReviewSection();
                break;
        }
        
        $(`#${contentId}`).removeClass('d-none');
        currentStep = step;
        
        // Scroll to top of the form
        $('html, body').animate({
            scrollTop: $('.enrollment-steps').offset().top - 20
        }, 300);
    }

    // Event Listeners
    
    // Select course
    $('.select-course').on('click', function() {
        const courseId = $(this).data('course-id');
        selectedCourse = courses.find(c => c.id == courseId);
        
        if (selectedCourse) {
            // Update form with course ID
            $('#course_id').val(selectedCourse.id);
            
            // Update summary
            updateSummary();
            
            // Go to next step
            goToStep(2);
        }
    });
    
    // Proceed to payment plan
    $('#proceed-to-payment-plan').on('click', function(e) {
        e.preventDefault();
        
        // Check if there's an active semester
        if (!currentSemester) {
            alert('No active registration period is available. Please contact the administration office.');
            return;
        }
        
        // Validate form
        const form = document.getElementById('enrollment-form');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        // Update enrollment data
        enrollmentData.course_id = $('#course_id').val();
        enrollmentData.enrollment_type = $('#enrollment_type').val();
        
        // Go to next step
        goToStep(3);
    });
    
    // Payment plan change
    $('input[name="payment_plan"]').on('change', function() {
        calculatePaymentPlan();
    });
    
    // Review enrollment
    $('#review-enrollment-btn').on('click', function() {
        calculatePaymentPlan();
        goToStep(4);
    });
    
    // Back to courses
    $('#back-to-courses').on('click', function() {
        goToStep(1);
    });
    
    // Back to details
    $('#back-to-details').on('click', function() {
        goToStep(2);
    });
    
    // Back to payment plan
    $('#back-to-payment').on('click', function() {
        goToStep(3);
    });
    
    // Submit enrollment
    $('#submit-enrollment').on('click', function() {
        if (!currentSemester) {
            alert('No active registration period is available. Please contact the administration office.');
            return;
        }
        
        if (!$('#terms_agreement').is(':checked')) {
            $('.invalid-feedback').show();
            return;
        }
        
        // Prepare data for submission
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            course_id: enrollmentData.course_id,
            enrollment_type: enrollmentData.enrollment_type,
            payment_plan: enrollmentData.payment_plan
        };
        
        // Show loading state
        const $submitBtn = $(this);
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        
        // Submit form via AJAX
        $.ajax({
            url: '{{ route("student.enrollments.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success modal
                    $('#enrollment-number').text(response.enrollment_number);
                    $('#successModal').modal('show');
                    
                    // Update proceed to payment button
                    $('#proceed-to-payment-btn').attr('href', '/student/payments?enrollment=' + response.enrollment_number);
                } else {
                    // Show error message
                    alert(response.message || 'An error occurred. Please try again.');
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Terms agreement checkbox
    $('#terms_agreement').on('change', function() {
        $('#submit-enrollment').prop('disabled', !$(this).is(':checked'));
        $('.invalid-feedback').hide();
    });
    
    // Initialize the form
    goToStep(1);
});
</script>
@endpush
