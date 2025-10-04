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
                    <form action="{{ route('student.payments.initiate') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" value="{{ $enrollment->course->name ?? 'N/A' }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (KSh)</label>
                            <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" value="1000" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('mpesa')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-phone display-6 text-success"></i>
                                            <h6 class="mt-2">M-Pesa</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('stripe')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-credit-card display-6 text-primary"></i>
                                            <h6 class="mt-2">Stripe</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="card payment-method" onclick="selectPaymentMethod('paypal')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-paypal display-6 text-info"></i>
                                            <h6 class="mt-2">PayPal</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" required>
                        </div>
                        
                        <div class="mb-3" id="mpesa-phone" style="display: none;">
                            <label for="phone" class="form-label">M-Pesa Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="254700000000">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="payButton" disabled>
                                Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <h5>No Enrollment Found</h5>
        <p>You need to enroll in a course first.</p>
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
}

.payment-method.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
}
</style>

<script>
function selectPaymentMethod(method) {
    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
    event.target.closest('.payment-method').classList.add('selected');
    
    document.getElementById('payment_method').value = method;
    
    const mpesaPhoneField = document.getElementById('mpesa-phone');
    if (method === 'mpesa') {
        mpesaPhoneField.style.display = 'block';
        document.getElementById('phone').required = true;
    } else {
        mpesaPhoneField.style.display = 'none';
        document.getElementById('phone').required = false;
    }
    
    document.getElementById('payButton').disabled = false;
}
</script>
@endsection