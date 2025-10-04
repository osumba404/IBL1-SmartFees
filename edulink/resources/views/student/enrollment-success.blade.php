@extends('layouts.student')

@section('title', 'Enrollment Successful')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="success-icon mx-auto mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="text-success mb-2">Enrollment Successful!</h1>
                        <p class="text-muted lead">Your course enrollment has been submitted successfully.</p>
                    </div>

                    <!-- Enrollment Details -->
                    <div class="enrollment-details bg-light rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-md-6 text-start">
                                <h6 class="text-primary mb-3">Enrollment Information</h6>
                                <p><strong>Enrollment Number:</strong> {{ $enrollment->enrollment_number }}</p>
                                <p><strong>Course:</strong> {{ $enrollment->course->name }}</p>
                                <p><strong>Course Code:</strong> {{ $enrollment->course->course_code }}</p>
                                <p><strong>Semester:</strong> {{ $enrollment->semester->name }} {{ $enrollment->semester->academic_year }}</p>
                                <p><strong>Enrollment Date:</strong> {{ $enrollment->enrollment_date->format('F j, Y') }}</p>
                            </div>
                            <div class="col-md-6 text-start">
                                <h6 class="text-primary mb-3">Payment Information</h6>
                                <p><strong>Total Fees:</strong> KSh {{ number_format($enrollment->total_fees_due, 2) }}</p>
                                <p><strong>Payment Plan:</strong> {{ $enrollment->payment_plan === 'installments' ? 'Installment Plan' : 'Full Payment' }}</p>
                                @if($enrollment->payment_plan === 'installments')
                                    <p><strong>Next Payment:</strong> KSh {{ number_format($enrollment->installment_amount, 2) }}</p>
                                    <p><strong>Due Date:</strong> {{ $enrollment->next_payment_due->format('F j, Y') }}</p>
                                @else
                                    <p><strong>Amount Due:</strong> KSh {{ number_format($enrollment->outstanding_balance, 2) }}</p>
                                    <p><strong>Due Date:</strong> {{ $enrollment->next_payment_due->format('F j, Y') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="alert alert-info text-start mb-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Next Steps:</h6>
                        <ol class="mb-0">
                            <li>Complete your payment to secure your enrollment</li>
                            <li>Check your email for enrollment confirmation</li>
                            <li>Access your course materials once payment is confirmed</li>
                            <li>Contact the academic office if you have any questions</li>
                        </ol>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('student.payments.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Make Payment Now
                        </a>
                        <a href="{{ route('student.enrollments.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-list me-2"></i>View My Enrollments
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>Back to Dashboard
                        </a>
                    </div>

                    <!-- Contact Information -->
                    <div class="mt-4 pt-4 border-top">
                        <p class="text-muted mb-0">
                            <small>
                                Need help? Contact our support team at 
                                <a href="mailto:support@edulink.ac.ke">support@edulink.ac.ke</a> 
                                or call <a href="tel:+254700000000">+254 700 000 000</a>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.enrollment-details {
    border-left: 4px solid #28a745;
}
</style>
@endsection