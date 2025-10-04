@extends('layouts.student')

@section('title', 'Make Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Make Payment</h1>
        </div>
    </div>

    @if($enrollment)
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Details</h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" action="{{ route('student.payments.initiate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" value="{{ $enrollment->course->name }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <input type="text" class="form-control" value="{{ $enrollment->semester->name ?? 'N/A' }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (KSh) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" max="{{ $enrollment->outstanding_balance }}" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $enrollment->outstanding_balance) }}" required>
                            <div class="form-text">Outstanding balance: KSh {{ number_format($enrollment->outstanding_balance, 2) }}</div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('mpesa')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-phone display-6 text-success"></i>
                                            <h6 class="mt-2">M-Pesa</h6>
                                            <small class="text-muted">Pay via M-Pesa STK Push</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('stripe')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-credit-card display-6 text-primary"></i>
                                            <h6 class="mt-2">Card Payment</h6>
                                            <small class="text-muted">Pay with Credit/Debit Card</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('bank_transfer')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-bank display-6 text-info"></i>
                                            <h6 class="mt-2">Bank Transfer</h6>
                                            <small class="text-muted">Transfer to college account</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('cash')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-cash display-6 text-warning"></i>
                                            <h6 class="mt-2">Cash Payment</h6>
                                            <small class="text-muted">Pay at finance office</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" required>
                            @error('payment_method')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- M-Pesa Phone Number Field -->
                        <div class="mb-3" id="mpesa-phone" style="display: none;">
                            <label for="phone" class="form-label">M-Pesa Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" placeholder="254700000000" value="{{ old('phone') }}">
                            <div class="form-text">Enter phone number in format: 254700000000</div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="payButton" disabled>
                                <i class="bi bi-credit-card me-2"></i>
                                Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>Payment Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Fees:</span>
                        <span>KSh {{ number_format($enrollment->total_fees_due, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Paid:</span>
                        <span class="text-success">KSh {{ number_format($enrollment->fees_paid, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Outstanding:</strong>
                        <strong class="text-danger">KSh {{ number_format($enrollment->outstanding_balance, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <h5>No Active Enrollment</h5>
        <p>You don't have any active enrollments to make payments for.</p>
        <a href="{{ route('student.enroll') }}" class="btn btn-primary">Enroll in a Course</a>
    </div>
    @endif
</div>

<style>
.payment-method {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-method:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.payment-method.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
}
</style>

<script>
function selectPaymentMethod(method) {
    // Remove selected class from all methods
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(m => m.classList.remove('selected'));
    
    // Add selected class to clicked method
    event.target.closest('.payment-method').classList.add('selected');
    
    // Set payment method value
    document.getElementById('payment_method').value = method;
    
    // Show/hide M-Pesa phone field
    const mpesaPhoneField = document.getElementById('mpesa-phone');
    const phoneInput = document.getElementById('phone');
    
    if (method === 'mpesa') {
        mpesaPhoneField.style.display = 'block';
        phoneInput.required = true;
    } else {
        mpesaPhoneField.style.display = 'none';
        phoneInput.required = false;
    }
    
    // Enable pay button
    document.getElementById('payButton').disabled = false;
}
</script>
@endsection