<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Make Payment - Edulink SmartFees</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --light: #f8fafc;
            --dark: #1e293b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .payment-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .payment-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .payment-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .payment-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .payment-body {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .payment-method {
            background: white;
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .payment-method::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .payment-method:hover::before {
            left: 100%;
        }
        
        .payment-method:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(37, 99, 235, 0.1) 100%);
            transform: translateY(-2px);
        }
        
        .payment-method .icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }
        
        .payment-method .name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .payment-method .desc {
            font-size: 0.875rem;
            color: #64748b;
        }
        
        .mpesa { color: var(--success); }
        .card { color: var(--primary); }
        .paypal { color: var(--info); }
        
        .phone-input {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        
        .phone-input.show {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                padding-top: 0;
                padding-bottom: 0;
            }
            to {
                opacity: 1;
                max-height: 200px;
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
        }
        
        .btn-payment {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-payment:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .btn-payment:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
            color: var(--dark);
            text-decoration: none;
        }
        
        .summary-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }
        
        .summary-row:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }
        
        .summary-label {
            font-weight: 500;
            color: #64748b;
        }
        
        .summary-value {
            font-weight: 600;
            color: var(--dark);
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .payment-container {
                border-radius: 16px;
            }
            
            .payment-header {
                padding: 1.5rem;
            }
            
            .payment-header h1 {
                font-size: 1.5rem;
            }
            
            .payment-body {
                padding: 1.5rem;
            }
            
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .payment-method {
                padding: 1rem;
            }
        }
        
        .loading {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .loading.show {
            display: flex;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="bi bi-credit-card me-2"></i>Make Payment</h1>
            <p>Secure and fast payment processing</p>
        </div>
        
        <div class="payment-body">
            <form action="{{ route('payment.process') }}" method="POST" id="paymentForm">
                @csrf
                
                <div class="form-section">
                    <h3><i class="bi bi-person-circle"></i>Student Information</h3>
                    <div class="summary-card">
                        <div class="summary-row">
                            <span class="summary-label">Student Name</span>
                            <span class="summary-value">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </div>
                        @if($enrollment)
                        <div class="summary-row">
                            <span class="summary-label">Course</span>
                            <span class="summary-value">{{ $enrollment->course->name ?? 'N/A' }}</span>
                        </div>
                        @endif
                        <div class="summary-row">
                            <span class="summary-label">Student ID</span>
                            <span class="summary-value">{{ $student->student_id }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="bi bi-currency-dollar"></i>Payment Amount</h3>
                    <div class="input-group">
                        <span class="input-group-text" style="background: #f1f5f9; border: 2px solid var(--border); border-right: none; border-radius: 12px 0 0 12px; font-weight: 600;">KSh</span>
                        <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" value="1000" required style="border-left: none; border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="bi bi-wallet2"></i>Select Payment Method</h3>
                    <div class="payment-methods">
                        <div class="payment-method" data-method="mpesa">
                            <i class="bi bi-phone icon mpesa"></i>
                            <div class="name">M-Pesa</div>
                            <div class="desc">Mobile Money</div>
                        </div>
                        <div class="payment-method" data-method="stripe">
                            <i class="bi bi-credit-card icon card"></i>
                            <div class="name">Credit Card</div>
                            <div class="desc">Visa, Mastercard, Amex</div>
                        </div>
                        <div class="payment-method" data-method="paypal">
                            <i class="bi bi-paypal icon paypal"></i>
                            <div class="name">PayPal</div>
                            <div class="desc">Digital Wallet</div>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="payment_method" required>
                    
                    <!-- M-Pesa Fields -->
                    <div class="phone-input" id="mpesa-phone">
                        <label for="phone" class="form-label"><i class="bi bi-phone me-2"></i>M-Pesa Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="254700000000">
                        <small class="text-muted mt-2 d-block">Enter your M-Pesa registered phone number</small>
                    </div>
                    
                    <!-- Card Fields -->
                    <div class="phone-input" id="card-fields">
                        <label class="form-label"><i class="bi bi-credit-card me-2"></i>Card Details</label>
                        <div id="stripe-card-element" class="form-control" style="padding: 12px;"></div>
                        <small class="text-muted mt-2 d-block">Enter your card details securely</small>
                    </div>
                    
                    <!-- PayPal Fields -->
                    <div class="phone-input" id="paypal-fields">
                        <label for="paypal_email" class="form-label"><i class="bi bi-envelope me-2"></i>PayPal Email</label>
                        <input type="email" class="form-control" id="paypal_email" name="paypal_email" placeholder="your@email.com">
                        <small class="text-muted mt-2 d-block">Enter your PayPal registered email address</small>
                    </div>
                </div>
                
                <div class="d-grid gap-3">
                    <button type="submit" class="btn-payment" id="payButton" disabled>
                        <span class="btn-text">Proceed to Payment</span>
                        <div class="loading">
                            <div class="spinner"></div>
                            <span>Processing...</span>
                        </div>
                    </button>
                    <div class="text-center">
                        <a href="{{ route('student.dashboard') }}" class="btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('.payment-method');
            const paymentMethodInput = document.getElementById('payment_method');
            const mpesaPhoneField = document.getElementById('mpesa-phone');
            const phoneInput = document.getElementById('phone');
            const payButton = document.getElementById('payButton');
            const paymentForm = document.getElementById('paymentForm');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    
                    // Add selected class to clicked method
                    this.classList.add('selected');
                    
                    // Set payment method value
                    const methodType = this.dataset.method;
                    paymentMethodInput.value = methodType;
                    
                    // Show/hide payment method fields
                    const cardFields = document.getElementById('card-fields');
                    const paypalFields = document.getElementById('paypal-fields');
                    
                    // Hide all fields first
                    mpesaPhoneField.classList.remove('show');
                    cardFields.classList.remove('show');
                    paypalFields.classList.remove('show');
                    
                    // Reset required fields
                    phoneInput.required = false;
                    document.getElementById('paypal_email').required = false;
                    
                    // Show relevant fields
                    if (methodType === 'mpesa') {
                        mpesaPhoneField.classList.add('show');
                        phoneInput.required = true;
                    } else if (methodType === 'stripe') {
                        cardFields.classList.add('show');
                        initializeStripeElements();
                    } else if (methodType === 'paypal') {
                        paypalFields.classList.add('show');
                        document.getElementById('paypal_email').required = true;
                    }
                    
                    // Enable pay button
                    payButton.disabled = false;
                });
            });
            
            // Form submission with loading state
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btnText = payButton.querySelector('.btn-text');
                const loading = payButton.querySelector('.loading');
                const formData = new FormData(this);
                
                btnText.style.display = 'none';
                loading.classList.add('show');
                payButton.disabled = true;
                
                // Handle different payment methods
                if (formData.get('payment_method') === 'stripe') {
                    this.handleStripePayment(formData);
                    return;
                } else if (formData.get('payment_method') === 'mpesa' || formData.get('payment_method') === 'paypal') {
                    console.log('Processing payment:', formData.get('payment_method'));
                    console.log('Form action:', this.action);
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            console.log('Redirecting to:', data.redirect_url);
                            // Redirect to appropriate page
                            window.location.href = data.redirect_url;
                        } else {
                            console.error('Payment failed:', data.message);
                            alert(data.message || 'Payment failed. Please try again.');
                            btnText.style.display = 'inline';
                            loading.classList.remove('show');
                            payButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('Payment failed. Please try again. Error: ' + error.message);
                        btnText.style.display = 'inline';
                        loading.classList.remove('show');
                        payButton.disabled = false;
                    });
                } else {
                    // For other payment methods, submit normally
                    this.submit();
                }
            });
            
            // Amount input formatting
            const amountInput = document.getElementById('amount');
            amountInput.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (value > 0) {
                    this.style.borderColor = 'var(--success)';
                } else {
                    this.style.borderColor = 'var(--danger)';
                }
            });
            

            
            // Phone number formatting
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    value = '254' + value.substring(1);
                }
                e.target.value = value;
            });
            
            // Initialize Stripe
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            let elements, cardElement;
            
            // Handle Stripe payment method selection
            function initializeStripeElements() {
                if (elements) return;
                
                elements = stripe.elements();
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#424770',
                            '::placeholder': {
                                color: '#aab7c4',
                            },
                        },
                    },
                });
                
                // Mount card element when stripe is selected
                setTimeout(() => {
                    const cardContainer = document.getElementById('stripe-card-element');
                    if (cardContainer) {
                        cardElement.mount('#stripe-card-element');
                    }
                }, 100);
            }
            
            // Handle Stripe payment
            paymentForm.handleStripePayment = async function(formData) {
                try {
                    // Create payment intent
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Confirm payment with Stripe
                        const {error} = await stripe.confirmCardPayment(data.client_secret, {
                            payment_method: {
                                card: cardElement,
                            }
                        });
                        
                        if (error) {
                            alert('Payment failed: ' + error.message);
                        } else {
                            window.location.href = '{{ route('payment.success') }}';
                        }
                    } else {
                        alert(data.message || 'Payment failed');
                    }
                } catch (error) {
                    console.error('Stripe payment error:', error);
                    alert('Payment failed. Please try again.');
                } finally {
                    const btnText = payButton.querySelector('.btn-text');
                    const loading = payButton.querySelector('.loading');
                    btnText.style.display = 'inline';
                    loading.classList.remove('show');
                    payButton.disabled = false;
                }
            };
        });
    </script>
</body>
</html>