@extends('layouts.admin')

@section('title', 'Create Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Fee Structure</h1>
        <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Fee Structures
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.fee-structures.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Fee Structure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('academic_year') is-invalid @enderror" 
                                id="academic_year" name="academic_year" required>
                            <option value="">Select Academic Year</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}/{{ $year + 1 }}" 
                                    {{ old('academic_year') == "$year/" . ($year + 1) ? 'selected' : '' }}>
                                    {{ $year }}/{{ $year + 1 }}
                                </option>
                            @endfor
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_id') is-invalid @enderror" 
                                id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }} ({{ $course->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester_id') is-invalid @enderror" 
                                id="semester_id" name="semester_id" required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" 
                                    {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }} ({{ \Carbon\Carbon::parse($semester->start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($semester->end_date)->format('M Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Core Fees -->
                <h6 class="mb-3 text-primary">Core Fees</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tuition_fee" class="form-label">Tuition Fee <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('tuition_fee') is-invalid @enderror" 
                                   id="tuition_fee" name="tuition_fee" value="{{ old('tuition_fee') }}" required>
                        </div>
                        @error('tuition_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="registration_fee" class="form-label">Registration Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('registration_fee') is-invalid @enderror" 
                                   id="registration_fee" name="registration_fee" value="{{ old('registration_fee', 0) }}">
                        </div>
                        @error('registration_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Academic Fees -->
                <h6 class="mb-3 text-primary">Academic Fees</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="library_fee" class="form-label">Library Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('library_fee') is-invalid @enderror" 
                                   id="library_fee" name="library_fee" value="{{ old('library_fee', 0) }}">
                        </div>
                        @error('library_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="lab_fee" class="form-label">Laboratory Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('lab_fee') is-invalid @enderror" 
                                   id="lab_fee" name="lab_fee" value="{{ old('lab_fee', 0) }}">
                        </div>
                        @error('lab_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="examination_fee" class="form-label">Examination Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('examination_fee') is-invalid @enderror" 
                                   id="examination_fee" name="examination_fee" value="{{ old('examination_fee', 0) }}">
                        </div>
                        @error('examination_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="activity_fee" class="form-label">Activity Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('activity_fee') is-invalid @enderror" 
                                   id="activity_fee" name="activity_fee" value="{{ old('activity_fee', 0) }}">
                        </div>
                        @error('activity_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Service Fees -->
                <h6 class="mb-3 text-primary">Service Fees</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="technology_fee" class="form-label">Technology Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('technology_fee') is-invalid @enderror" 
                                   id="technology_fee" name="technology_fee" value="{{ old('technology_fee', 0) }}">
                        </div>
                        @error('technology_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="student_services_fee" class="form-label">Student Services Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('student_services_fee') is-invalid @enderror" 
                                   id="student_services_fee" name="student_services_fee" value="{{ old('student_services_fee', 0) }}">
                        </div>
                        @error('student_services_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="graduation_fee" class="form-label">Graduation Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('graduation_fee') is-invalid @enderror" 
                                   id="graduation_fee" name="graduation_fee" value="{{ old('graduation_fee', 0) }}">
                        </div>
                        @error('graduation_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="id_card_fee" class="form-label">ID Card Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('id_card_fee') is-invalid @enderror" 
                                   id="id_card_fee" name="id_card_fee" value="{{ old('id_card_fee', 0) }}">
                        </div>
                        @error('id_card_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Insurance & Accommodation -->
                <h6 class="mb-3 text-primary">Insurance & Accommodation</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="medical_insurance_fee" class="form-label">Medical Insurance Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('medical_insurance_fee') is-invalid @enderror" 
                                   id="medical_insurance_fee" name="medical_insurance_fee" value="{{ old('medical_insurance_fee', 0) }}">
                        </div>
                        @error('medical_insurance_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="accident_insurance_fee" class="form-label">Accident Insurance Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('accident_insurance_fee') is-invalid @enderror" 
                                   id="accident_insurance_fee" name="accident_insurance_fee" value="{{ old('accident_insurance_fee', 0) }}">
                        </div>
                        @error('accident_insurance_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="accommodation_fee" class="form-label">Accommodation Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('accommodation_fee') is-invalid @enderror" 
                                   id="accommodation_fee" name="accommodation_fee" value="{{ old('accommodation_fee', 0) }}">
                        </div>
                        @error('accommodation_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="meal_plan_fee" class="form-label">Meal Plan Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('meal_plan_fee') is-invalid @enderror" 
                                   id="meal_plan_fee" name="meal_plan_fee" value="{{ old('meal_plan_fee', 0) }}">
                        </div>
                        @error('meal_plan_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Discount -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="discount_amount" class="form-label">Discount Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('discount_amount') is-invalid @enderror" 
                                   id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}">
                        </div>
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Amount to subtract from total fees</div>
                    </div>
                </div>

                <!-- Effective Dates -->
                <h6 class="mb-3 text-primary">Effective Period</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                               id="effective_from" name="effective_from" value="{{ old('effective_from') }}" required>
                        @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="effective_until" class="form-label">Effective Until</label>
                        <input type="date" class="form-control @error('effective_until') is-invalid @enderror" 
                               id="effective_until" name="effective_until" value="{{ old('effective_until') }}">
                        @error('effective_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave blank for no end date</div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <h6 class="mb-3 text-primary">Payment Settings</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="late_payment_penalty_rate" class="form-label">Late Payment Penalty Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('late_payment_penalty_rate') is-invalid @enderror" 
                               id="late_payment_penalty_rate" name="late_payment_penalty_rate" value="{{ old('late_payment_penalty_rate', 0) }}">
                        @error('late_payment_penalty_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="late_payment_fixed_penalty" class="form-label">Late Payment Fixed Penalty</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('late_payment_fixed_penalty') is-invalid @enderror" 
                                   id="late_payment_fixed_penalty" name="late_payment_fixed_penalty" value="{{ old('late_payment_fixed_penalty', 0) }}">
                        </div>
                        @error('late_payment_fixed_penalty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="grace_period_days" class="form-label">Grace Period (Days)</label>
                        <input type="number" min="0" class="form-control @error('grace_period_days') is-invalid @enderror" 
                               id="grace_period_days" name="grace_period_days" value="{{ old('grace_period_days', 0) }}">
                        @error('grace_period_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Days after deadline before penalties apply</div>
                    </div>
                </div>

                <!-- Installment Settings -->
                <h6 class="mb-3 text-primary">Installment Settings</h6>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allows_installments" name="allows_installments" 
                                   value="1" {{ old('allows_installments') ? 'checked' : '' }}>
                            <label class="form-check-label" for="allows_installments">
                                Allow Installment Payments
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="installment-settings" style="display: none;">
                    <div class="col-md-4">
                        <label for="max_installments" class="form-label">Maximum Installments</label>
                        <input type="number" min="2" max="12" class="form-control @error('max_installments') is-invalid @enderror" 
                               id="max_installments" name="max_installments" value="{{ old('max_installments') }}">
                        @error('max_installments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="minimum_deposit_percentage" class="form-label">Minimum Deposit (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('minimum_deposit_percentage') is-invalid @enderror" 
                               id="minimum_deposit_percentage" name="minimum_deposit_percentage" value="{{ old('minimum_deposit_percentage') }}">
                        @error('minimum_deposit_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="minimum_deposit_amount" class="form-label">Minimum Deposit Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('minimum_deposit_amount') is-invalid @enderror" 
                                   id="minimum_deposit_amount" name="minimum_deposit_amount" value="{{ old('minimum_deposit_amount') }}">
                        </div>
                        @error('minimum_deposit_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle installment settings
    const allowsInstallments = document.getElementById('allows_installments');
    const installmentSettings = document.getElementById('installment-settings');
    
    allowsInstallments.addEventListener('change', function() {
        installmentSettings.style.display = this.checked ? 'block' : 'none';
    });
    
    // Show installment settings if checkbox is checked on page load
    if (allowsInstallments.checked) {
        installmentSettings.style.display = 'block';
    }

    // Calculate total fees
    function calculateTotal() {
        const feeInputs = [
            'tuition_fee', 'registration_fee', 'library_fee', 'lab_fee', 
            'examination_fee', 'activity_fee', 'technology_fee', 'student_services_fee',
            'graduation_fee', 'id_card_fee', 'medical_insurance_fee', 'accident_insurance_fee',
            'accommodation_fee', 'meal_plan_fee'
        ];
        
        let tuitionFee = parseFloat(document.getElementById('tuition_fee').value) || 0;
        let otherFees = 0;
        let discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        
        feeInputs.slice(1).forEach(function(feeId) {
            otherFees += parseFloat(document.getElementById(feeId).value) || 0;
        });
        
        let total = tuitionFee + otherFees - discount;
        
        // Update summary
        document.getElementById('summary-tuition').textContent = 'KSh ' + tuitionFee.toLocaleString('en-KE', {minimumFractionDigits: 2});
        document.getElementById('summary-other').textContent = 'KSh ' + otherFees.toLocaleString('en-KE', {minimumFractionDigits: 2});
        document.getElementById('summary-discount').textContent = '-KSh ' + discount.toLocaleString('en-KE', {minimumFractionDigits: 2});
        document.getElementById('summary-total').textContent = 'KSh ' + total.toLocaleString('en-KE', {minimumFractionDigits: 2});
    }
    
    // Add event listeners to all fee inputs
    const allFeeInputs = document.querySelectorAll('input[type="number"]');
    allFeeInputs.forEach(function(input) {
        input.addEventListener('input', calculateTotal);
    });
    
    // Calculate total button
    document.getElementById('calculate-total').addEventListener('click', calculateTotal);
    
    // Date validation
    const effectiveFrom = document.getElementById('effective_from');
    const effectiveUntil = document.getElementById('effective_until');
    
    effectiveFrom.addEventListener('change', function() {
        if (this.value) {
            effectiveUntil.min = this.value;
        }
    });
    
    effectiveUntil.addEventListener('change', function() {
        if (this.value && effectiveFrom.value && this.value <= effectiveFrom.value) {
            this.setCustomValidity('End date must be after start date');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Initial calculation
    calculateTotal();
});
</script>
@endpush
