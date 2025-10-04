@extends('layouts.student')

@section('title', 'Enrollment Error')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <!-- Error Icon -->
                    <div class="mb-4">
                        <div class="error-icon mx-auto mb-3">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="text-danger mb-2">Enrollment Error</h1>
                        <p class="text-muted lead">There was an issue processing your enrollment.</p>
                    </div>

                    <!-- Error Message -->
                    <div class="alert alert-danger text-start mb-4">
                        <h6><i class="fas fa-exclamation-circle me-2"></i>Error Details:</h6>
                        <p class="mb-0">{{ $error_message ?? 'An unexpected error occurred while processing your enrollment. Please try again.' }}</p>
                    </div>

                    <!-- Common Issues -->
                    <div class="bg-light rounded p-4 mb-4 text-start">
                        <h6 class="text-primary mb-3">Common Issues & Solutions:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Course Full:</strong> The course may have reached maximum capacity. Try enrolling in a different semester or contact admissions.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Already Enrolled:</strong> You may already be enrolled in this course. Check your enrollments page.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Registration Closed:</strong> The registration period may have ended. Contact the registrar's office.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Prerequisites:</strong> Ensure you meet all course prerequisites and requirements.
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Technical Issue:</strong> Clear your browser cache and try again, or use a different browser.
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('student.enroll') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                        <a href="{{ route('student.courses.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-book me-2"></i>Browse Courses
                        </a>
                        <a href="{{ route('student.enrollments.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-list me-2"></i>My Enrollments
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>

                    <!-- Contact Information -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Academic Support</h6>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:registrar@edulink.ac.ke">registrar@edulink.ac.ke</a>
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-phone me-2"></i>
                                    <a href="tel:+254700000001">+254 700 000 001</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Technical Support</h6>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:support@edulink.ac.ke">support@edulink.ac.ke</a>
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-phone me-2"></i>
                                    <a href="tel:+254700000000">+254 700 000 000</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-icon {
    animation: errorShake 0.5s ease-in-out;
}

@keyframes errorShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
@endsection