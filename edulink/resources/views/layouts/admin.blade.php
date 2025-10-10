<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Edulink Smart Fees') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 60px;
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            
            /* Light theme variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #334155 100%);
            --header-bg: #ffffff;
        }

        [data-theme="dark"] {
            /* Dark theme variables */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --card-bg: #1e293b;
            --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --header-bg: #1e293b;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            color: white;
            text-decoration: none;
        }
        
        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .brand-text {
            line-height: 1.2;
        }
        
        .brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }
        
        .brand-subtitle {
            font-size: 0.75rem;
            opacity: 0.9;
            margin: 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary-color);
        }

        .nav-link.active {
            color: white;
            background-color: rgba(37, 99, 235, 0.2);
            border-left-color: var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .nav-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 1.5rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-header {
            background: var(--header-bg);
            height: var(--header-height);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .header-right {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-container {
            margin-left: 1rem;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item {
            font-size: 0.875rem;
        }
        
        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--dark-color);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.25rem;
            cursor: pointer;
            margin-right: 1rem;
        }
        
        .sidebar-toggle i {
            color: var(--text-secondary);
            font-size: 1.25rem;
        }

        .quick-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .quick-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f3f4f6;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .quick-action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }
        
        .notification-center {
            position: relative;
        }
        
        .notification-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f3f4f6;
            border: none;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.2s;
        }
        
        .notification-btn:hover {
            background: #e5e7eb;
        }
        
        .notification-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger-color);
            color: white;
            border-radius: 10px;
            min-width: 16px;
            height: 16px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        
        .user-menu-btn {
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .user-menu-btn:hover {
            background: #f3f4f6;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .user-info {
            line-height: 1.2;
        }
        
        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin: 0;
        }

        .content-wrapper {
            padding: 2rem;
            background-color: var(--bg-secondary);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item a {
            color: var(--text-secondary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-primary);
        }

        .card {
            border: 1px solid var(--border-color);
            background-color: var(--card-bg);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
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

        .table {
            margin-bottom: 0;
            background-color: var(--card-bg);
            color: var(--text-primary);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--text-primary);
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }

        .table td {
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        /* Theme toggle button */
        .theme-toggle {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f3f4f6;
            border: none;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .theme-toggle:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        [data-theme="dark"] .theme-toggle {
            background: var(--border-color);
            color: var(--text-secondary);
        }

        /* Dark theme specific styles */
        [data-theme="dark"] .alert {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        [data-theme="dark"] .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        [data-theme="dark"] .form-control {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control:focus {
            background-color: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-primary);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }

        [data-theme="dark"] .quick-action-btn {
            background: var(--border-color);
            color: var(--text-secondary);
        }

        [data-theme="dark"] .notification-btn {
            background: var(--border-color);
            color: var(--text-secondary);
        }

        [data-theme="dark"] .user-menu-btn {
            color: var(--text-primary);
        }

        [data-theme="dark"] .user-menu-btn:hover {
            background: var(--border-color);
        }

        [data-theme="dark"] .user-name {
            color: var(--text-primary);
        }

        [data-theme="dark"] .user-role {
            color: var(--text-secondary);
        }

        /* Additional dark mode text visibility fixes */
        [data-theme="dark"] * {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-dark,
        [data-theme="dark"] .text-black,
        [data-theme="dark"] .text-muted {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-white {
            color: white !important;
        }

        [data-theme="dark"] .form-control {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .form-select {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .form-label {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .table {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .table th {
            color: var(--text-primary) !important;
            background-color: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .table td {
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: var(--bg-secondary) !important;
        }

        [data-theme="dark"] .list-group-item {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .modal-content {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .modal-header {
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .modal-footer {
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .modal-title {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .pagination .page-link {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .pagination .page-link:hover {
            background-color: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        [data-theme="dark"] .breadcrumb-item {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .breadcrumb-item.active {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .breadcrumb-item a {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .breadcrumb-item a:hover {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] small {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-success {
            color: #10b981 !important;
        }

        [data-theme="dark"] .text-danger {
            color: #ef4444 !important;
        }

        [data-theme="dark"] .text-warning {
            color: #f59e0b !important;
        }

        [data-theme="dark"] .text-info {
            color: #06b6d4 !important;
        }

        [data-theme="dark"] .text-primary {
            color: var(--primary-color) !important;
        }

        [data-theme="dark"] .text-secondary {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, 
        [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] p {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] span {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] div {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] label {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .card-title {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .card-text {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .card-subtitle {
            color: var(--text-secondary) !important;
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

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 1rem;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 8px;
            padding: 0.5rem 0;
        }
        
        .notification-dropdown {
            width: 320px;
            padding: 0;
        }
        
        .notification-header {
            padding: 1rem 1.25rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-list {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: flex-start;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f9fafb;
        }
        
        .notification-item.unread {
            background-color: #eff6ff;
            border-left: 3px solid var(--primary-color);
        }
        
        .notification-icon {
            margin-right: 0.75rem;
            margin-top: 0.125rem;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .notification-message {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        .notification-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .user-dropdown {
            width: 260px;
            padding: 0;
        }
        
        .user-dropdown-header {
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            display: flex;
            align-items: center;
        }
        
        .user-dropdown-avatar {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 0.75rem;
        }
        
        .user-dropdown-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
        }
        
        .user-dropdown-email {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0.125rem;
        }
        
        .user-dropdown-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
            transform: translateX(4px);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .loading-spinner {
            display: none;
        }

        .loading .loading-spinner {
            display: inline-block;
        }

        /* Footer dark mode fix */
        [data-theme="dark"] .footer {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .footer h5 {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .footer p {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .footer a {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .footer a:hover {
            color: var(--text-primary) !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand d-flex align-items-center">
                <div class="brand-icon me-3">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-title">Edulink</div>
                    <div class="brand-subtitle">Admin Portal</div>
                </div>
            </a>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                
                @if($admin->canManageStudents())
                <div class="nav-section">Student Management</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" 
                       href="{{ route('admin.students.index') }}">
                        <i class="bi bi-people"></i>
                        Students
                    </a>
                </li>
                @endif
                
                @if($admin->canManageCourses())
                <div class="nav-section">Academic Management</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" 
                       href="{{ route('admin.courses.index') }}">
                        <i class="bi bi-book"></i>
                        Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.semesters.*') ? 'active' : '' }}" 
                       href="{{ route('admin.semesters.index') }}">
                        <i class="bi bi-calendar3"></i>
                        Semesters
                    </a>
                </li>
                @endif
                
                @if($admin->canManageFees() || $admin->canManagePayments())
                <div class="nav-section">Financial Management</div>
                @if($admin->canManageFees())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.fee-structures.*') ? 'active' : '' }}" 
                       href="{{ route('admin.fee-structures.index') }}">
                        <i class="bi bi-currency-dollar"></i>
                        Fee Structures
                    </a>
                </li>
                @endif
                @if($admin->canManagePayments())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" 
                       href="{{ route('admin.payments.index') }}">
                        <i class="bi bi-credit-card"></i>
                        Payments
                    </a>
                </li>
                @endif
                @endif
                
                @if($admin->canViewReports())
                <div class="nav-section">Reports & Analytics</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.index') }}">
                        <i class="bi bi-graph-up"></i>
                        Reports
                    </a>
                </li>
                @endif
                
                @if($admin->isSuperAdmin())
                <div class="nav-section">System Administration</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" 
                       href="{{ route('admin.admins.index') }}">
                        <i class="bi bi-shield-check"></i>
                        Admin Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i>
                        Settings
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="breadcrumb-container">
                    @if(isset($breadcrumbs))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
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
                </div>
            </div>
            
            <div class="header-right">
                <!-- Quick Actions -->
                <div class="quick-actions me-3">
                    <button class="theme-toggle" onclick="toggleAdminTheme()" title="Toggle Theme">
                        <i class="bi bi-moon-fill" id="admin-theme-icon"></i>
                    </button>
                    @if($admin->canManageStudents())
                    <a href="{{ route('admin.students.create') }}" class="quick-action-btn" title="Add Student">
                        <i class="bi bi-person-plus"></i>
                    </a>
                    @endif
                    @if($admin->canManageCourses())
                    <a href="{{ route('admin.courses.create') }}" class="quick-action-btn" title="Add Course">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                    @endif
                    @if($admin->canViewReports())
                    <a href="{{ route('admin.reports.index') }}" class="quick-action-btn" title="Reports">
                        <i class="bi bi-graph-up"></i>
                    </a>
                    @endif
                </div>
                
                <!-- Notifications -->
                <div class="notification-center me-3">
                    <button class="notification-btn" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="notification-count">3</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <div class="notification-header">
                            <h6 class="mb-0">Notifications</h6>
                            <small class="text-muted">3 unread</small>
                        </div>
                        <div class="notification-list">
                            <div class="notification-item unread">
                                <div class="notification-icon">
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">Payment Verification Required</div>
                                    <div class="notification-message">5 payments pending verification</div>
                                    <div class="notification-time">2 minutes ago</div>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div class="notification-icon">
                                    <i class="bi bi-person-plus text-success"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">New Student Registration</div>
                                    <div class="notification-message">John Doe has registered</div>
                                    <div class="notification-time">1 hour ago</div>
                                </div>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="#" class="btn btn-sm btn-outline-primary w-100">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="user-menu">
                    <div class="dropdown">
                        <button class="user-menu-btn d-flex align-items-center" 
                                type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                {{ substr($admin->name, 0, 1) }}
                            </div>
                            <div class="user-info d-none d-md-block">
                                <div class="user-name">{{ $admin->name }}</div>
                                <div class="user-role">{{ ucwords(str_replace('_', ' ', $admin->role)) }}</div>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end user-dropdown">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-avatar">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div class="user-dropdown-info">
                                    <div class="user-dropdown-name">{{ $admin->name }}</div>
                                    <div class="user-dropdown-email">{{ $admin->email }}</div>
                                    <div class="user-dropdown-role">{{ ucwords(str_replace('_', ' ', $admin->role)) }}</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="bi bi-person me-2"></i>My Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.settings.account') }}">
                                <i class="bi bi-gear me-2"></i>Account Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="toggleAdminTheme()">
                                <i class="bi bi-moon me-2" id="admin-dropdown-theme-icon"></i><span id="admin-theme-text">Dark Mode</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-wrapper">
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
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 mt-4">
            <div class="container-fluid px-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted small">&copy; {{ date('Y') }} Edulink International College Nairobi. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('privacy-policy') }}" class="text-muted text-decoration-none small me-3">Privacy Policy</a>
                        <a href="{{ route('terms-of-service') }}" class="text-muted text-decoration-none small">Terms of Service</a>
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
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    // Toggle icon for mobile
                    const icon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('show')) {
                        icon.className = 'bi bi-x-lg';
                    } else {
                        icon.className = 'bi bi-list';
                    }
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    // Toggle icon for desktop
                    const icon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.className = 'bi bi-list';
                    } else {
                        icon.className = 'bi bi-list';
                    }
                }
            });

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                        // Reset icon when closing
                        const icon = sidebarToggle.querySelector('i');
                        icon.className = 'bi bi-list';
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    // Reset icon on resize
                    const icon = sidebarToggle.querySelector('i');
                    icon.className = 'bi bi-list';
                }
            });

            // Initialize theme
            loadAdminTheme();
            
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
        });

        // Theme toggle function for admin
        function toggleAdminTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const headerIcon = document.getElementById('admin-theme-icon');
            const dropdownIcon = document.getElementById('admin-dropdown-theme-icon');
            const themeText = document.getElementById('admin-theme-text');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('admin-theme', 'light');
                if (headerIcon) headerIcon.className = 'bi bi-moon-fill';
                if (dropdownIcon) dropdownIcon.className = 'bi bi-moon me-2';
                if (themeText) themeText.textContent = 'Dark Mode';
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('admin-theme', 'dark');
                if (headerIcon) headerIcon.className = 'bi bi-sun-fill';
                if (dropdownIcon) dropdownIcon.className = 'bi bi-sun me-2';
                if (themeText) themeText.textContent = 'Light Mode';
            }
        }
        
        // Load saved theme for admin
        function loadAdminTheme() {
            const savedTheme = localStorage.getItem('admin-theme') || 'light';
            const html = document.documentElement;
            const headerIcon = document.getElementById('admin-theme-icon');
            const dropdownIcon = document.getElementById('admin-dropdown-theme-icon');
            const themeText = document.getElementById('admin-theme-text');
            
            html.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                if (headerIcon) headerIcon.className = 'bi bi-sun-fill';
                if (dropdownIcon) dropdownIcon.className = 'bi bi-sun me-2';
                if (themeText) themeText.textContent = 'Light Mode';
            }
        }
        
        // Initialize admin theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAdminTheme();
        });

        // Global AJAX setup for CSRF token
        if (typeof axios !== 'undefined') {
            window.axios = axios;
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    </script>

    @stack('scripts')
</body>
</html>
