@extends('layouts.student')

@section('title', 'Create Payment Plan')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Create Payment Plan</h2>
            <p class="page-subtitle">Set up flexible installment schedule for {{ $enrollment->course->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('student.payment-plans.store') }}" id="payment-plan-form">
                        @csrf
                        <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select name="enrollment_id" class="form-select" id="enrollment-select" required>
                                @foreach($enrollments as $enroll)
                                    <option value="{{ $enroll->id }}" 
                                            data-course="{{ $enroll->course->name }}"
                                            data-total-fee="{{ $enroll->total_fees_due > 0 ? $enroll->total_fees_due : ($enroll->course->total_fee ?? 100000) }}"
                                            data-paid="{{ $enroll->fees_paid ?? 0 }}"
                                            {{ $enrollment && $enrollment->id == $enroll->id ? 'selected' : '' }}>
                                        {{ $enroll->course->name }} ({{ $enroll->course->course_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="plan_name" class="form-control" id="plan-name"
                                   value="{{ old('plan_name', $enrollment->course->name . ' Payment Plan') }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Number of Installments</label>
                                <select name="total_installments" class="form-select" id="installment-count" required>
                                    <option value="2">2 Installments</option>
                                    <option value="3" selected>3 Installments</option>
                                    <option value="4">4 Installments</option>
                                    <option value="6">6 Installments</option>
                                    <option value="12">12 Installments</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="number" name="total_amount" class="form-control" 
                                       value="{{ $enrollment->outstanding_balance }}" 
                                       id="total-amount" step="0.01" min="1" required>
                            </div>
                        </div>

                        <div id="installments-container">
                            <!-- Installments will be generated here -->
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Payment Plan</button>
                            <a href="{{ route('student.enrollments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Enrollment Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Course:</strong> <span id="selected-course-name">{{ $enrollment->course->name }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Outstanding Balance:</strong> 
                        <span class="text-danger" id="outstanding-balance">KES {{ number_format($enrollment->outstanding_balance, 2) }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Already Paid:</strong> 
                        <span class="text-success" id="already-paid">KES {{ number_format($enrollment->fees_paid, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollmentSelect = document.getElementById('enrollment-select');
    const planName = document.getElementById('plan-name');
    const installmentCount = document.getElementById('installment-count');
    const totalAmount = document.getElementById('total-amount');
    const container = document.getElementById('installments-container');
    const selectedCourseName = document.getElementById('selected-course-name');
    const outstandingBalance = document.getElementById('outstanding-balance');
    const alreadyPaid = document.getElementById('already-paid');
    
    function updateEnrollmentDetails() {
        const selectedOption = enrollmentSelect.options[enrollmentSelect.selectedIndex];
        const courseName = selectedOption.dataset.course;
        const totalFee = parseFloat(selectedOption.dataset.totalFee);
        const paidAmount = parseFloat(selectedOption.dataset.paid);
        const outstanding = Math.max(0, totalFee - paidAmount);
        
        selectedCourseName.textContent = courseName;
        outstandingBalance.textContent = 'KES ' + new Intl.NumberFormat().format(outstanding.toFixed(2));
        alreadyPaid.textContent = 'KES ' + new Intl.NumberFormat().format(paidAmount.toFixed(2));
        planName.value = courseName + ' Payment Plan';
        totalAmount.value = outstanding;
        
        generateInstallments();
    }
    
    enrollmentSelect.addEventListener('change', updateEnrollmentDetails);
    
    function generateInstallments() {
        const count = parseInt(installmentCount.value);
        const amount = parseFloat(totalAmount.value);
        const installmentAmount = (amount / count).toFixed(2);
        
        container.innerHTML = '';
        
        for (let i = 1; i <= count; i++) {
            const dueDate = new Date();
            dueDate.setMonth(dueDate.getMonth() + i);
            
            container.innerHTML += `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Installment ${i} Amount</label>
                        <input type="number" name="installment_amounts[]" class="form-control installment-amount" 
                               value="${installmentAmount}" step="0.01" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="installment_dates[]" class="form-control" 
                               value="${dueDate.toISOString().split('T')[0]}" required>
                    </div>
                </div>
            `;
        }
    }
    
    installmentCount.addEventListener('change', generateInstallments);
    totalAmount.addEventListener('input', generateInstallments);
    
    generateInstallments();
});
</script>
@endsection