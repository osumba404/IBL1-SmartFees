@extends('layouts.admin')

@section('title', 'Create Semester')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create New Semester</h1>
            <p class="text-muted mb-0">Add a new academic semester to the system</p>
        </div>
        <div>
            <a href="{{ route('admin.semesters.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Semesters
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Semester Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.semesters.store') }}" method="POST" id="semesterForm">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Semester Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Fall 2024"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="semester_code" class="form-label">Semester Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('semester_code') is-invalid @enderror" 
                                       id="semester_code" 
                                       name="semester_code" 
                                       value="{{ old('semester_code') }}"
                                       placeholder="e.g., FALL2024"
                                       required>
                                @error('semester_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('academic_year') is-invalid @enderror" 
                                       id="academic_year" 
                                       name="academic_year" 
                                       value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}"
                                       placeholder="e.g., 2024-2025"
                                       required>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="semester_type" class="form-label">Semester Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('semester_type') is-invalid @enderror" 
                                        id="semester_type" 
                                        name="semester_type" 
                                        required>
                                    <option value="">Select Semester Type</option>
                                    <option value="fall" {{ old('semester_type') === 'fall' ? 'selected' : '' }}>Fall</option>
                                    <option value="spring" {{ old('semester_type') === 'spring' ? 'selected' : '' }}>Spring</option>
                                    <option value="summer" {{ old('semester_type') === 'summer' ? 'selected' : '' }}>Summer</option>
                                    <option value="winter" {{ old('semester_type') === 'winter' ? 'selected' : '' }}>Winter</option>
                                </select>
                                @error('semester_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date Information -->
                        <h6 class="mb-3 text-primary">Semester Dates</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ old('start_date') }}"
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ old('end_date') }}"
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Registration Dates -->
                        <h6 class="mb-3 text-primary">Registration Period</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="registration_start" class="form-label">Registration Start <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('registration_start') is-invalid @enderror" 
                                       id="registration_start" 
                                       name="registration_start" 
                                       value="{{ old('registration_start') }}"
                                       required>
                                @error('registration_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="registration_end" class="form-label">Registration End <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('registration_end') is-invalid @enderror" 
                                       id="registration_end" 
                                       name="registration_end" 
                                       value="{{ old('registration_end') }}"
                                       required>
                                @error('registration_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fee Information -->
                        <h6 class="mb-3 text-primary">Fee Management</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="fee_due_date" class="form-label">Fee Due Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('fee_due_date') is-invalid @enderror" 
                                       id="fee_due_date" 
                                       name="fee_due_date" 
                                       value="{{ old('fee_due_date') }}"
                                       required>
                                @error('fee_due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="late_fee_start_date" class="form-label">Late Fee Start Date</label>
                                <input type="date" 
                                       class="form-control @error('late_fee_start_date') is-invalid @enderror" 
                                       id="late_fee_start_date" 
                                       name="late_fee_start_date" 
                                       value="{{ old('late_fee_start_date') }}">
                                @error('late_fee_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Date when late fees begin to apply</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="grace_period_days" class="form-label">Grace Period (Days)</label>
                                <input type="number" 
                                       class="form-control @error('grace_period_days') is-invalid @enderror" 
                                       id="grace_period_days" 
                                       name="grace_period_days" 
                                       value="{{ old('grace_period_days', 0) }}"
                                       min="0" 
                                       max="30">
                                @error('grace_period_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Number of days after due date before late fees apply</div>
                            </div>
                        </div>

                        <!-- Credit Limits -->
                        <h6 class="mb-3 text-primary">Credit Limits</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="min_credits" class="form-label">Minimum Credits</label>
                                <input type="number" 
                                       class="form-control @error('min_credits') is-invalid @enderror" 
                                       id="min_credits" 
                                       name="min_credits" 
                                       value="{{ old('min_credits') }}"
                                       min="1">
                                @error('min_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum credits required for enrollment</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_credits" class="form-label">Maximum Credits</label>
                                <input type="number" 
                                       class="form-control @error('max_credits') is-invalid @enderror" 
                                       id="max_credits" 
                                       name="max_credits" 
                                       value="{{ old('max_credits') }}"
                                       min="1">
                                @error('max_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum credits allowed for enrollment</div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">New semesters are typically created as inactive and activated when ready</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.semesters.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Semester
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Semester Code</h6>
                        <p class="small text-muted">Use a unique, uppercase code like "FALL2024" or "SPR2025"</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">Date Guidelines</h6>
                        <ul class="small text-muted">
                            <li>Registration must start before semester start</li>
                            <li>Registration must end before or on semester start</li>
                            <li>Fee due date should be during registration period</li>
                            <li>Late fee date should be after fee due date</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">Credit Limits</h6>
                        <p class="small text-muted">Set minimum and maximum credit limits to control student enrollment. Leave blank if no limits apply.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">Status</h6>
                        <p class="small text-muted">Create semesters as "Inactive" and activate them when registration should open.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="fillCurrentYear()">
                        <i class="bi bi-calendar me-2"></i>Use Current Academic Year
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="generateCode()">
                        <i class="bi bi-code me-2"></i>Generate Code from Name
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate code from name
function generateCode() {
    const name = document.getElementById('name').value;
    if (name) {
        const code = name.toUpperCase().replace(/\s+/g, '').replace(/[^A-Z0-9]/g, '');
        document.getElementById('semester_code').value = code;
    }
}

// Fill current academic year
function fillCurrentYear() {
    const currentYear = new Date().getFullYear();
    const nextYear = currentYear + 1;
    document.getElementById('academic_year').value = `${currentYear}-${nextYear}`;
}

// Auto-generate code when name changes
document.getElementById('name').addEventListener('input', function() {
    if (!document.getElementById('semester_code').value) {
        generateCode();
    }
});

// Date validation
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    if (startDate) {
        // Set minimum end date to start date
        document.getElementById('end_date').min = startDate;
        
        // Set maximum registration end to start date
        document.getElementById('registration_end').max = startDate;
        
        // Set maximum registration start to start date
        document.getElementById('registration_start').max = startDate;
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    const endDate = this.value;
    const startDate = document.getElementById('start_date').value;
    
    if (startDate && endDate && endDate <= startDate) {
        alert('End date must be after start date');
        this.value = '';
    }
});

document.getElementById('registration_start').addEventListener('change', function() {
    const regStart = this.value;
    if (regStart) {
        // Set minimum registration end to registration start
        document.getElementById('registration_end').min = regStart;
        
        // Set minimum fee due date to registration start
        document.getElementById('fee_due_date').min = regStart;
    }
});

document.getElementById('fee_due_date').addEventListener('change', function() {
    const feeDue = this.value;
    if (feeDue) {
        // Set minimum late fee start to fee due date
        document.getElementById('late_fee_start_date').min = feeDue;
    }
});

// Credit validation
document.getElementById('min_credits').addEventListener('change', function() {
    const minCredits = parseInt(this.value);
    const maxCredits = parseInt(document.getElementById('max_credits').value);
    
    if (minCredits && maxCredits && minCredits > maxCredits) {
        alert('Minimum credits cannot be greater than maximum credits');
        this.value = '';
    }
});

document.getElementById('max_credits').addEventListener('change', function() {
    const maxCredits = parseInt(this.value);
    const minCredits = parseInt(document.getElementById('min_credits').value);
    
    if (minCredits && maxCredits && maxCredits < minCredits) {
        alert('Maximum credits cannot be less than minimum credits');
        this.value = '';
    }
});

// Form submission validation
document.getElementById('semesterForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const regStart = new Date(document.getElementById('registration_start').value);
    const regEnd = new Date(document.getElementById('registration_end').value);
    
    if (endDate <= startDate) {
        e.preventDefault();
        alert('End date must be after start date');
        return;
    }
    
    if (regStart >= startDate) {
        e.preventDefault();
        alert('Registration must start before semester start date');
        return;
    }
    
    if (regEnd > startDate) {
        e.preventDefault();
        alert('Registration must end before or on semester start date');
        return;
    }
});
</script>
@endpush
