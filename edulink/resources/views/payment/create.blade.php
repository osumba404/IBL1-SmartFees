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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: 0;
        }
        
        .payment-container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3.5rem 2rem;
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
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        
        .payment-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
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
            padding: 3rem;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 3rem;
        }
        
        .payment-form {
            /* Left column for form */
        }
        
        .payment-summary {
            /* Right column for summary */
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 24px;
            padding: 2.5rem;
            height: fit-content;
            position: sticky;
            top: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .form-section {
            margin-bottom: 2.5rem;
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
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .payment-method {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(226, 232, 240, 0.5);
            border-radius: 24px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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
            border-color: #667eea;
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(102, 126, 234, 0.25), 0 0 0 1px rgba(102, 126, 234, 0.1);
        }
        
        .payment-method.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 25px 50px -12px rgba(102, 126, 234, 0.4), 0 0 0 2px rgba(102, 126, 234, 0.2);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 16px;
            padding: 1.25rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-payment::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }
        
        .btn-payment:hover::before {
            left: 100%;
        }
        
        .btn-payment:hover:not(:disabled) {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
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
        
        .info-card {
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.9) 0%, rgba(241, 245, 249, 0.9) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .summary-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
                border-radius: 20px;
            }
            
            .payment-header {
                padding: 2rem 1.5rem;
            }
            
            .payment-header h1 {
                font-size: 1.75rem;
            }
            
            .payment-body {
                padding: 2rem 1.5rem;
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .payment-summary {
                order: -1;
                position: static;
                padding: 2rem;
            }
            
            .payment-methods {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .payment-method {
                padding: 1.5rem 1rem;
                display: flex;
                align-items: center;
                text-align: left;
                gap: 1rem;
            }
            
            .payment-method .icon {
                font-size: 2rem;
                margin-bottom: 0;
                flex-shrink: 0;
            }
            
            .payment-method .content {
                flex: 1;
            }
            
            .summary-card {
                padding: 1.5rem;
            }
            
            .info-card {
                padding: 1.5rem;
            }
            
            .form-section h3 {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 480px) {
            .payment-header {
                padding: 1.5rem 1rem;
            }
            
            .payment-body {
                padding: 1.5rem 1rem;
            }
            
            .payment-method {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
            
            .payment-method .icon {
                margin-bottom: 0.5rem;
            }
            
            .btn-payment {
                padding: 1rem;
                font-size: 1rem;
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
        
        .support-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2rem 0;
            margin-top: 2rem;
        }
        
        .support-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
            text-align: center;
        }
        
        .support-container h6 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .support-container p {
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        
        .support-contacts {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 500;
        }
        
        .contact-item i {
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .support-section {
                padding: 1.5rem 0;
            }
            
            .support-container {
                padding: 0 1rem;
            }
            
            .support-contacts {
                flex-direction: column;
                gap: 1rem;
            }
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
            <div class="payment-form">
                <form action="{{ route('payment.process.post') }}" method="POST" id="paymentForm">
                    @csrf
                    @if($enrollment)
                        <input type="hidden" name="enrollment_id" id="enrollment_id" value="{{ $enrollment->id }}">
                    @endif
                    @if($existingPayment)
                        <input type="hidden" name="payment_id" value="{{ $existingPayment->id }}">
                    @endif
                    
                    <div class="form-section">
                        <h3><i class="bi bi-currency-dollar"></i>Payment Amount</h3>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #f1f5f9; border: 2px solid #e2e8f0; border-right: none; border-radius: 16px 0 0 16px; font-weight: 600; font-size: 1.1rem;">KSh</span>
                            <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" 
                                   value="{{ $prefilledAmount ?? $paymentData['amount'] ?? $existingPayment->amount ?? 1000 }}" 
                                   {{ $existingPayment ? 'readonly' : '' }} required 
                                   style="border-left: none; border-radius: 0 16px 16px 0; font-size: 1.1rem; font-weight: 600;">
                        </div>
                        @if($existingPayment)
                            <small class="text-muted mt-2 d-block">Amount is fixed for this enrollment payment</small>
                            <input type="hidden" name="payment_id" value="{{ $existingPayment->id }}">
                        @endif
                    </div>
                    
                    <div class="form-section">
                        <h3><i class="bi bi-wallet2"></i>Select Payment Method</h3>
                        <div class="payment-methods">
                            <div class="payment-method" data-method="mpesa">
                                <i class="bi bi-phone icon mpesa"></i>
                                <div class="content">
                                    <div class="name">M-Pesa</div>
                                    <div class="desc">Mobile Money</div>
                                </div>
                            </div>
                            <div class="payment-method" data-method="stripe">
                                <i class="bi bi-credit-card icon card"></i>
                                <div class="content">
                                    <div class="name">Credit Card</div>
                                    <div class="desc">Visa, Mastercard, Amex</div>
                                </div>
                            </div>
                            <div class="payment-method" data-method="paypal">
                                <i class="bi bi-paypal icon paypal"></i>
                                <div class="content">
                                    <div class="name">PayPal</div>
                                    <div class="desc">Digital Wallet</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" 
                               value="{{ $paymentData['payment_method'] ?? $existingPayment->payment_method ?? '' }}" required>
                        
                        <!-- M-Pesa Fields -->
                        <div class="phone-input" id="mpesa-phone">
                            <label for="phone" class="form-label"><i class="bi bi-phone me-2"></i>M-Pesa Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ $student->phone ?? '' }}" placeholder="254700000000">
                            <small class="text-muted mt-2 d-block">Phone number from your profile. You can edit if needed.</small>
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
            
            <div class="payment-summary">
                <h4 class="mb-4"><i class="bi bi-receipt me-2"></i>Payment Summary</h4>
                
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label"><i class="bi bi-person me-2"></i>Student Name</span>
                        <span class="summary-value">{{ $student->first_name }} {{ $student->last_name }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label"><i class="bi bi-hash me-2"></i>Student ID</span>
                        <span class="summary-value">{{ $student->student_id }}</span>
                    </div>
                    @if($enrollment)
                    <div class="summary-row">
                        <span class="summary-label"><i class="bi bi-book me-2"></i>Course</span>
                        <span class="summary-value" id="selected-course">{{ $enrollment->course->name ?? 'N/A' }}</span>
                    </div>
                    @endif
                    <div class="summary-row" style="border-top: 2px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                        <span class="summary-label" style="font-size: 1.1rem; font-weight: 700;"><i class="bi bi-currency-dollar me-2"></i>Total Amount</span>
                        <span class="summary-value" style="font-size: 1.5rem; font-weight: 700; color: #2563eb;" id="total-amount">KSh {{ number_format($prefilledAmount ?? $paymentData['amount'] ?? $existingPayment->amount ?? 1000, 2) }}</span>
                    </div>
                </div>
                
                @if(isset($enrollments) && $enrollments->count() > 1)
                <div class="info-card">
                    <h6 class="mb-3"><i class="bi bi-book me-2"></i>Select Course</h6>
                    <select class="form-select" id="enrollment-selector" onchange="updateCourseSelection()">
                        @foreach($enrollments as $enroll)
                            <option value="{{ $enroll->id }}" 
                                    data-course="{{ $enroll->course->name }}" 
                                    data-fee="{{ $enroll->course->fee ?? 1000 }}"
                                    {{ ($enrollment && $enrollment->id == $enroll->id) ? 'selected' : '' }}>
                                {{ $enroll->course->name }} ({{ $enroll->course->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                

            </div>
        </div>
    </div>
    
    <!-- Support Section -->
    <div class="support-section">
        <div class="support-container">
            <h6><i class="bi bi-headset me-2"></i>Need Help?</h6>
            <p>Contact our support team if you need assistance with your payment.</p>
            <div class="support-contacts">
                <div class="contact-item">
                    <i class="bi bi-envelope me-2"></i>
                    <span>support@edulink.ac.ke</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-telephone me-2"></i>
                    <span>+254 700 000 000</span>
                </div>
            </div>
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
            
            // Pre-select payment method if exists
            const preselectedMethod = paymentMethodInput.value;
            if (preselectedMethod) {
                const methodElement = document.querySelector(`[data-method="${preselectedMethod}"]`);
                if (methodElement) {
                    methodElement.click();
                }
            }
            
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
                        // Auto-populate with student phone if empty
                        if (!phoneInput.value && '{{ $student->phone }}') {
                            phoneInput.value = '{{ $student->phone }}';
                        }
                    } else if (methodType === 'stripe') {
                        cardFields.classList.add('show');
                        initializeStripeElements();
                    } else if (methodType === 'paypal') {
                        paypalFields.classList.add('show');
                        document.getElementById('paypal_email').required = true;
                    }
                    
                    // Enable pay button
                    payButton.disabled = false;
                    
                    // Update button text based on method
                    const btnText = payButton.querySelector('.btn-text');
                    if (methodType === 'mpesa') {
                        btnText.textContent = 'Pay with M-Pesa';
                    } else if (methodType === 'stripe') {
                        btnText.textContent = 'Pay with Card';
                    } else if (methodType === 'paypal') {
                        btnText.textContent = 'Pay with PayPal';
                    }
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
            
            // Amount input formatting and live update
            const amountInput = document.getElementById('amount');
            const totalAmountDisplay = document.getElementById('total-amount');
            
            amountInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                if (value > 0) {
                    this.style.borderColor = '#10b981';
                    totalAmountDisplay.textContent = 'KSh ' + new Intl.NumberFormat().format(value.toFixed(2));
                } else {
                    this.style.borderColor = '#ef4444';
                    totalAmountDisplay.textContent = 'KSh 0.00';
                }
            });
            
            // Course selection update function
            window.updateCourseSelection = function() {
                const selector = document.getElementById('enrollment-selector');
                const selectedOption = selector.options[selector.selectedIndex];
                const enrollmentId = selectedOption.value;
                const courseName = selectedOption.dataset.course;
                const courseFee = selectedOption.dataset.fee;
                
                // Update hidden input
                document.getElementById('enrollment_id').value = enrollmentId;
                
                // Update course name in summary
                document.getElementById('selected-course').textContent = courseName;
                
                // Update amount if not readonly
                if (!amountInput.readOnly) {
                    amountInput.value = courseFee;
                    totalAmountDisplay.textContent = 'KSh ' + new Intl.NumberFormat().format(parseFloat(courseFee).toFixed(2));
                }
            };
            

            
            // Phone number formatting
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    value = '254' + value.substring(1);
                }
                e.target.value = value;
            });
            
            // Initialize Stripe
            const stripe = Stripe('pk_test_51234567890abcdef');
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
    
    <!-- Security Protection -->
    <script>
        // Disable right-click context menu
        document.addEventListener('contextmenu', e => e.preventDefault());
        
        // Disable F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable text selection
        document.onselectstart = () => false;
        document.ondragstart = () => false;
        
        // Clear console
        setInterval(() => console.clear(), 1000);
        
        // Detect DevTools
        let devtools = {open: false, orientation: null};
        const threshold = 160;
        setInterval(() => {
            if (window.outerHeight - window.innerHeight > threshold || 
                window.outerWidth - window.innerWidth > threshold) {
                if (!devtools.open) {
                    devtools.open = true;
                    alert('Developer tools detected. Redirecting for security.');
                    window.location.href = '/student/dashboard';
                }
            } else {
                devtools.open = false;
            }
        }, 500);
    </script>
</body>
</html>