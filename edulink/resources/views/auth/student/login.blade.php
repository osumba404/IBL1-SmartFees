<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Login - {{ config('app.name', 'Edulink Smart Fees') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 50%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
        }

        .login-left {
            background: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .login-right {
            padding: 3rem;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .brand-logo i {
            margin-right: 0.75rem;
            font-size: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .admin-portal-link {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            margin-top: 2rem;
        }

        .admin-portal-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .admin-portal-link a:hover {
            text-decoration: underline;
        }

        .register-link {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-left {
                padding: 2rem;
                text-align: center;
            }
            
            .login-right {
                padding: 2rem;
            }
            
            .brand-logo {
                font-size: 1.5rem;
                justify-content: center;
            }
        }

        .loading-spinner {
            display: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading .loading-spinner {
            display: inline-block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .feature-list li i {
            margin-right: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <!-- Left Side - Branding -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="login-left">
                        <div class="brand-logo">
                            <i class="bi bi-mortarboard-fill"></i>
                            Edulink Student
                        </div>
                        <h3 class="mb-3">Welcome Back!</h3>
                        <p class="mb-4">Access your student portal to view courses, check fees, make payments, and track your academic progress at Edulink International College Nairobi.</p>
                        
                        <ul class="feature-list">
                            <li><i class="bi bi-check-circle"></i>View Course Enrollments</li>
                            <li><i class="bi bi-check-circle"></i>Check Fee Balances</li>
                            <li><i class="bi bi-check-circle"></i>Make Secure Payments</li>
                            <li><i class="bi bi-check-circle"></i>Download Statements</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Right Side - Login Form -->
                <div class="col-lg-7">
                    <div class="login-right">
                        <div class="d-lg-none text-center mb-4">
                            <div class="brand-logo text-primary">
                                <i class="bi bi-mortarboard-fill"></i>
                                Edulink Student
                            </div>
                        </div>
                        
                        <h2 class="mb-1">Student Login</h2>
                        <p class="text-muted mb-4">Enter your student credentials to access your portal</p>

                        @if(session('error'))
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.login') }}" id="loginForm">
                            @csrf
                            
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control @error('login') is-invalid @enderror" 
                                       id="login" 
                                       name="login" 
                                       placeholder="Student ID or Email"
                                       value="{{ old('login') }}" 
                                       required 
                                       autofocus>
                                <label for="login">Student ID or Email</label>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Password"
                                       required>
                                <label for="password">Password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                    <span class="btn-text">Sign In</span>
                                    <span class="loading-spinner ms-2"></span>
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('student.password.request') }}" class="forgot-password">
                                Forgot your password?
                            </a>
                        </div>

                        <div class="register-link">
                            <i class="bi bi-person-plus me-2"></i>
                            New student? 
                            <a href="{{ route('student.register') }}">Create your account</a>
                        </div>

                        <div class="divider">
                            <span>or</span>
                        </div>

                        <div class="admin-portal-link">
                            <i class="bi bi-shield-check me-2"></i>
                            Are you an administrator? 
                            <a href="{{ route('admin.login') }}">Access Admin Portal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            loginForm.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
