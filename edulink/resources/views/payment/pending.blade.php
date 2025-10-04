@extends('layouts.app')

@section('title', 'Payment Pending')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Loading Animation -->
                    <div class="mb-4">
                        <div class="spinner-border text-warning" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <h2 class="text-warning mb-3">
                        <i class="bi bi-clock-history me-2"></i>
                        Payment Pending
                    </h2>
                    
                    <p class="lead text-muted mb-4">
                        Please complete the payment on your phone
                    </p>
                    
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="bi bi-phone me-2"></i>
                            Check Your Phone
                        </h5>
                        <p class="mb-0">
                            An M-Pesa payment request has been sent to your phone. 
                            Please enter your M-Pesa PIN to complete the payment.
                        </p>
                    </div>
                    
                    <!-- Payment Details -->
                    <div class="row text-start mb-4">
                        <div class="col-md-6">
                            <strong>Amount:</strong> KSh {{ number_format($payment->amount, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Method:</strong> M-Pesa
                        </div>
                        <div class="col-md-6 mt-2">
                            <strong>Reference:</strong> {{ $payment->transaction_reference }}
                        </div>
                        <div class="col-md-6 mt-2">
                            <strong>Status:</strong> 
                            <span class="badge bg-warning">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                    
                    <!-- Status Check -->
                    <div id="status-message" class="alert alert-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Checking payment status...
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="button" class="btn btn-outline-primary" onclick="checkStatus()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Check Status
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="simulatePayment()">
                            <i class="bi bi-check-circle me-2"></i>
                            Simulate Payment (Test)
                        </button>
                        <a href="{{ route('payment.create') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkInterval;
let checkCount = 0;
const maxChecks = 60; // Check for 5 minutes (60 * 5 seconds)

function checkStatus() {
    fetch(`/payment/status/{{ $payment->id }}`)
        .then(response => response.json())
        .then(data => {
            const statusMessage = document.getElementById('status-message');
            
            if (data.success) {
                if (data.status === 'completed') {
                    statusMessage.className = 'alert alert-success';
                    statusMessage.innerHTML = '<i class="bi bi-check-circle me-2"></i>Payment completed successfully! Redirecting...';
                    
                    // Store payment data for success page
                    sessionStorage.setItem('completed_payment', JSON.stringify({
                        transaction_id: data.transaction_id,
                        amount: data.amount,
                        method: 'M-Pesa',
                        date: new Date().toLocaleString()
                    }));
                    
                    // Redirect to success page after 2 seconds
                    setTimeout(() => {
                        window.location.href = '/payment/success';
                    }, 2000);
                    
                    clearInterval(checkInterval);
                } else if (data.status === 'failed') {
                    statusMessage.className = 'alert alert-danger';
                    statusMessage.innerHTML = '<i class="bi bi-x-circle me-2"></i>Payment failed. Please try again.';
                    clearInterval(checkInterval);
                } else {
                    statusMessage.className = 'alert alert-warning';
                    statusMessage.innerHTML = '<i class="bi bi-clock me-2"></i>Payment is still pending...';
                }
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
        });
}

// Auto-check status every 5 seconds
checkInterval = setInterval(() => {
    checkCount++;
    checkStatus();
    
    if (checkCount >= maxChecks) {
        clearInterval(checkInterval);
        const statusMessage = document.getElementById('status-message');
        statusMessage.className = 'alert alert-warning';
        statusMessage.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Payment verification timeout. Please check manually.';
    }
}, 5000);

// Initial status check
checkStatus();

// Simulate payment for testing
function simulatePayment() {
    fetch(`/simulate-payment/{{ $payment->id }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusMessage = document.getElementById('status-message');
                statusMessage.className = 'alert alert-success';
                statusMessage.innerHTML = '<i class="bi bi-check-circle me-2"></i>Payment simulated successfully!';
                
                setTimeout(() => {
                    window.location.href = '/payment/success';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error simulating payment:', error);
        });
}
</script>
@endsection