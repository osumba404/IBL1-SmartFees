@extends('layouts.admin')

@section('title', 'Edit Semester - ' . $semester->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Semester</h1>
            <p class="text-muted mb-0">Update semester information and settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.semesters.show', $semester) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Details
            </a>
            <a href="{{ route('admin.semesters.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list me-2"></i>All Semesters
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Semester Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.semesters.update', $semester) }}" method="POST" id="semesterForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Semester Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $semester->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">e.g., "Fall 2024", "Spring 2025"</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="semester_code" class="form-label">Semester Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('semester_code') is-invalid @enderror" 
                                       id="semester_code" name="semester_code" value="{{ old('semester_code', $semester->semester_code) }}" required>
                                @error('semester_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Unique identifier (e.g., "FALL2024", "SPR2025")</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('academic_year') is-invalid @enderror" 
                                       id="academic_year" name="academic_year" 
                                       value="{{ old('academic_year', $semester->academic_year . '-' . ($semester->academic_year + 1)) }}" 
                                       placeholder="2024-2025" required>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: YYYY-YYYY (e.g., 2024-2025)</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="semester_type" class="form-label">Semester Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('semester_type') is-invalid @enderror" 
                                        id="semester_type" name="semester_type" required>
                                    <option value="">Select semester type</option>
                                    @php
                                        $currentType = match($semester->period) {
                                            'semester_1' => 'fall',
                                            'semester_2' => 'spring',
                                            'summer' => 'summer',
                                            'winter' => 'winter',
                                            default => ''
                                        };
                                    @endphp
                                    <option value="fall" {{ old('semester_type', $currentType) === 'fall' ? 'selected' : '' }}>Fall Semester</option>
                                    <option value="spring" {{ old('semester_type', $currentType) === 'spring' ? 'selected' : '' }}>Spring Semester</option>
                                    <option value="summer" {{ old('semester_type', $currentType) === 'summer' ? 'selected' : '' }}>Summer Session</option>
                                    <option value="winter" {{ old('semester_type', $currentType) === 'winter' ? 'selected' : '' }}>Winter Session</option>
                                </select>
                                @error('semester_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Semester Dates -->
                        <h6 class="mb-3 text-primary">Semester Duration</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $semester->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $semester->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Registration Period -->
                        <h6 class="mb-3 text-primary">Registration Period</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="registration_start" class="form-label">Registration Start <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('registration_start') is-invalid @enderror" 
                                       id="registration_start" name="registration_start" 
                                       value="{{ old('registration_start', $semester->registration_start_date->format('Y-m-d')) }}" required>
                                @error('registration_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="registration_end" class="form-label">Registration End <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('registration_end') is-invalid @enderror" 
                                       id="registration_end" name="registration_end" 
                                       value="{{ old('registration_end', $semester->registration_end_date->format('Y-m-d')) }}" required>
                                @error('registration_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fee Payment Deadlines -->
                        <h6 class="mb-3 text-primary">Fee Payment Settings</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="fee_due_date" class="form-label">Fee Payment Deadline <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fee_due_date') is-invalid @enderror" 
                                       id="fee_due_date" name="fee_due_date" 
                                       value="{{ old('fee_due_date', $semester->fee_payment_deadline->format('Y-m-d')) }}" required>
                                @error('fee_due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Last date for fee payment without penalty</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="late_fee_start_date" class="form-label">Late Fee Start Date</label>
                                <input type="date" class="form-control @error('late_fee_start_date') is-invalid @enderror" 
                                       id="late_fee_start_date" name="late_fee_start_date" 
                                       value="{{ old('late_fee_start_date', $semester->late_payment_deadline?->format('Y-m-d')) }}">
                                @error('late_fee_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When late fees begin to apply</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="grace_period_days" class="form-label">Grace Period (Days)</label>
                                <input type="number" class="form-control @error('grace_period_days') is-invalid @enderror" 
                                       id="grace_period_days" name="grace_period_days" min="0" max="30"
                                       value="{{ old('grace_period_days', $semester->grace_period_days ?? 7) }}">
                                @error('grace_period_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Days after deadline before late fees apply</div>
                            </div>
                        </div>

                        <!-- Credit Limits -->
                        <h6 class="mb-3 text-primary">Credit Limits</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="min_credits" class="form-label">Minimum Credits</label>
                                <input type="number" class="form-control @error('min_credits') is-invalid @enderror" 
                                       id="min_credits" name="min_credits" min="1"
                                       value="{{ old('min_credits', $semester->min_credits_per_student) }}">
                                @error('min_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum credits a student must enroll in</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_credits" class="form-label">Maximum Credits</label>
                                <input type="number" class="form-control @error('max_credits') is-invalid @enderror" 
                                       id="max_credits" name="max_credits" min="1"
                                       value="{{ old('max_credits', $semester->max_credits_per_student) }}">
                                @error('max_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum credits a student can enroll in</div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select status</option>
                                    <option value="active" {{ old('status', $semester->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $semester->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="upcoming" {{ old('status', $semester->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="completed" {{ old('status', $semester->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $semester->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-check-lg me-2"></i>Update Semester
                                </button>
                                <a href="{{ route('admin.semesters.show', $semester) }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                            <div>
                                @if($semester->enrollments()->count() === 0 && $semester->feeStructures()->count() === 0)
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash me-2"></i>Delete Semester
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Editing Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Changing dates may affect existing enrollments and fee structures.
                    </div>
                    
                    <h6 class="mt-3">Date Validation Rules:</h6>
                    <ul class="small">
                        <li>End date must be after start date</li>
                        <li>Registration must end before or on semester start</li>
                        <li>Fee deadline should be after registration start</li>
                        <li>Late fee date should be after fee deadline</li>
                    </ul>
                    
                    <h6 class="mt-3">Status Guidelines:</h6>
                    <ul class="small">
                        <li><strong>Active:</strong> Currently accepting enrollments</li>
                        <li><strong>Upcoming:</strong> Future semester, not yet active</li>
                        <li><strong>Inactive:</strong> Temporarily disabled</li>
                        <li><strong>Completed:</strong> Semester has ended</li>
                        <li><strong>Cancelled:</strong> Semester was cancelled</li>
                    </ul>

                    <h6 class="mt-3">Current Statistics:</h6>
                    <ul class="small">
                        <li>Enrollments: {{ $semester->enrollments()->count() }}</li>
                        <li>Fee Structures: {{ $semester->feeStructures()->count() }}</li>
                        <li>Created: {{ $semester->created_at->format('M d, Y') }}</li>
                        <li>Last Updated: {{ $semester->updated_at->format('M d, Y') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($semester->enrollments()->count() === 0 && $semester->feeStructures()->count() === 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the semester <strong>{{ $semester->name }}</strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.semesters.destroy', $semester) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Semester</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate code from name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('semester_code');
    
    nameInput.addEventListener('input', function() {
        if (!codeInput.dataset.userModified) {
            const name = this.value;
            const code = name.toUpperCase().replace(/\s+/g, '').replace(/[^A-Z0-9]/g, '');
            codeInput.value = code;
        }
    });
    
    codeInput.addEventListener('input', function() {
        this.dataset.userModified = 'true';
    });

    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const regStart = document.getElementById('registration_start');
    const regEnd = document.getElementById('registration_end');
    const feeDate = document.getElementById('fee_due_date');
    const lateDate = document.getElementById('late_fee_start_date');

    function validateDates() {
        if (startDate.value) {
            endDate.min = startDate.value;
            regEnd.max = startDate.value;
        }
        
        if (endDate.value && startDate.value && endDate.value <= startDate.value) {
            endDate.setCustomValidity('End date must be after start date');
        } else {
            endDate.setCustomValidity('');
        }
        
        if (regStart.value) {
            regEnd.min = regStart.value;
            feeDate.min = regStart.value;
        }
        
        if (regEnd.value && regStart.value && regEnd.value < regStart.value) {
            regEnd.setCustomValidity('Registration end must be after start');
        } else {
            regEnd.setCustomValidity('');
        }
        
        if (feeDate.value) {
            lateDate.min = feeDate.value;
        }
        
        if (lateDate.value && feeDate.value && lateDate.value <= feeDate.value) {
            lateDate.setCustomValidity('Late fee date must be after fee deadline');
        } else {
            lateDate.setCustomValidity('');
        }
    }

    // Add event listeners for date validation
    [startDate, endDate, regStart, regEnd, feeDate, lateDate].forEach(input => {
        input.addEventListener('change', validateDates);
    });

    // Credit validation
    const minCredits = document.getElementById('min_credits');
    const maxCredits = document.getElementById('max_credits');

    function validateCredits() {
        if (minCredits.value && maxCredits.value) {
            if (parseInt(minCredits.value) >= parseInt(maxCredits.value)) {
                maxCredits.setCustomValidity('Maximum credits must be greater than minimum');
            } else {
                maxCredits.setCustomValidity('');
            }
        }
    }

    minCredits.addEventListener('input', validateCredits);
    maxCredits.addEventListener('input', validateCredits);

    // Initial validation
    validateDates();
    validateCredits();
});
</script>
@endpush
