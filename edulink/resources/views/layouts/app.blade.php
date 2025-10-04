<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Edulink SmartFees') - {{ config('app.name', 'Edulink Smart Fees') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --navbar-height: 70px;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-color);
            padding-top: var(--navbar-height);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            min-height: var(--navbar-height);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white !important;
            padding-right: 4rem;
        }
        
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
                padding-right: 3rem;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
                padding-right: 3rem;
            }
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white !important;
            padding: 0.3rem 0.5rem;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            z-index: 1050;
        }
        
        .navbar-toggler i {
            color: white !important;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .navbar-toggler-icon {
            width: 20px;
            height: 20px;
        }
        
        .navbar > .container {
            position: relative;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.5rem;
        }

        footer {
            background-color: var(--dark-color) !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-vh-100 bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Edulink SmartFees
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth('student')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.fees.index') }}">Fees</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::guard('student')->user()->first_name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('student.profile') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('student.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @elseauth('admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::guard('admin')->user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.login') }}">Student Portal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.login') }}">Admin Portal</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Edulink International College Nairobi</h5>
                        <p class="mb-0">Excellence in Education, Innovation in Learning</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">
                            <i class="bi bi-envelope me-2"></i>support@edulink.ac.ke<br>
                            <i class="bi bi-telephone me-2"></i>+254 700 000 000
                        </p>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-6">
                        <small>&copy; {{ date('Y') }} Edulink International College. All rights reserved.</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small>
                            <a href="{{ route('privacy-policy') }}" class="text-light me-3">Privacy Policy</a>
                            <a href="{{ route('terms-of-service') }}" class="text-light">Terms of Service</a>
                        </small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('#navbarNav');
            
            if (navbarToggler && navbarCollapse) {
                navbarCollapse.addEventListener('shown.bs.collapse', function() {
                    navbarToggler.innerHTML = '<i class="bi bi-x-lg" style="color: white !important; font-size: 1.2rem;"></i>';
                });
                
                navbarCollapse.addEventListener('hidden.bs.collapse', function() {
                    navbarToggler.innerHTML = '<span class="navbar-toggler-icon"></span>';
                });
            }
        });
    </script>
</body>
</html>