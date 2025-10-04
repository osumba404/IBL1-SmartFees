<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Edulink SmartFees')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-vh-100 bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Edulink SmartFees
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.login') }}">Student Portal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.login') }}">Admin Portal</a>
                        </li>
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
                        <p class="mb-0">Streamlining education fee management for the digital age.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Quick Links</h6>
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('student.login') }}" class="text-light text-decoration-none">Student Portal</a></li>
                                    <li><a href="{{ route('admin.login') }}" class="text-light text-decoration-none">Admin Portal</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Legal</h6>
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('privacy-policy') }}" class="text-light text-decoration-none">Privacy Policy</a></li>
                                    <li><a href="{{ route('terms-of-service') }}" class="text-light text-decoration-none">Terms of Service</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; {{ date('Y') }} Edulink International College Nairobi. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">
                            <i class="bi bi-envelope me-2"></i>support@edulink.ac.ke
                            <i class="bi bi-telephone ms-3 me-2"></i>+254700000000
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>