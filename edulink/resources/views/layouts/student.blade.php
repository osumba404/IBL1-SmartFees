<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Student Portal') - {{ config('app.name', 'Edulink Smart Fees') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
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
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.25rem 0.5rem;
            margin-left: auto;
            order: 2;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .navbar-brand {
            order: 1;
        }

        .main-content {
            min-height: calc(100vh - var(--navbar-height));
            padding: 2rem 0;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--secondary-color);
            font-size: 0.95rem;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--dark-color);
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
            background-color: #f8fafc;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #ef4444 100%);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .payment-status.paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .payment-status.pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .payment-status.overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .quick-action-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .quick-action-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            text-decoration: none;
            color: inherit;
        }

        .quick-action-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem 0;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-value {
                font-size: 1.5rem;
            }
            
            .navbar-collapse {
                background: rgba(37, 99, 235, 0.95);
                margin-top: 0.5rem;
                border-radius: 0.5rem;
                padding: 1rem;
                backdrop-filter: blur(10px);
                order: 3;
                width: 100%;
            }
            
            .navbar > .container {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }
            
            .btn-group-vertical .btn {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-value {
                font-size: 1.25rem;
            }
        }

        .loading-spinner {
            display: none;
        }

        .loading .loading-spinner {
            display: inline-block;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Edulink Student Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                           href="{{ route('student.dashboard') }}">
                            <i class="bi bi-house me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.courses.*') ? 'active' : '' }}" 
                           href="{{ route('student.courses.index') }}">
                            <i class="bi bi-book me-1"></i>My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}" 
                           href="{{ route('student.fees.index') }}">
                            <i class="bi bi-currency-dollar me-1"></i>Fees & Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.payments.*') ? 'active' : '' }}" 
                           href="{{ route('student.payments.history') }}">
                            <i class="bi bi-credit-card me-1"></i>Payment History
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            @if($student->unreadNotifications->count() > 0)
                                <span class="notification-badge">{{ $student->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            @forelse($student->unreadNotifications->take(5) as $notification)
                            <li>
                                <a class="dropdown-item" href="{{ $notification->data['url'] ?? '#' }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-{{ $notification->data['icon'] ?? 'info-circle' }} text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-medium">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li><span class="dropdown-item-text text-muted">No new notifications</span></li>
                            @endforelse
                            @if($student->unreadNotifications->count() > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="{{ route('student.notifications.index') }}">View All Notifications</a></li>
                            @endif
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline">{{ $student->first_name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ $student->first_name }} {{ $student->last_name }}</h6></li>
                            <li><small class="dropdown-item-text text-muted">{{ $student->student_id }}</small></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('student.profile') }}">
                                    <i class="bi bi-person me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('student.settings') }}">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('student.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @if(isset($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Add loading state to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                });
            });

            // Mark notifications as read when clicked
            const notificationLinks = document.querySelectorAll('.dropdown-menu a[href*="notification"]');
            notificationLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    // Mark as read via AJAX
                    const notificationId = this.dataset.notificationId;
                    if (notificationId) {
                        fetch(`/student/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        });
                    }
                });
            });
        });

        // Global AJAX setup for CSRF token
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    </script>

    @stack('scripts')
</body>
</html>
