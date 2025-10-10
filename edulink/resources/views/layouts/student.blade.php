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
            
            /* Light theme variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --sidebar-bg: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            --header-bg: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
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
            --header-bg: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
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
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }

        .top-header {
            background: var(--header-bg);
            height: 60px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            color: white;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }
        
        .sidebar-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .sidebar-close:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
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
            border-left-color: rgba(255, 255, 255, 0.5);
        }
        
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: white;
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
        
        .quick-action {
            width: 40px;
            height: 40px;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            margin: 0 0.25rem !important;
        }
        
        .quick-action:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            margin-right: 1rem;
        }
        
        .header-brand {
            color: white;
            text-decoration: none;
            margin-left: 0.5rem;
        }
        
        .header-brand:hover {
            color: white;
            text-decoration: none;
        }
        
        .header-brand-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .header-brand-text {
            line-height: 1.1;
        }
        
        .header-brand-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }
        
        .header-brand-subtitle {
            font-size: 0.7rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .header-right {
            display: flex;
            align-items: center;
        }

        .content-wrapper {
            padding: 2rem;
            min-height: calc(100vh - 60px);
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
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
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
        }

        .table td {
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        /* Theme toggle button */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Dark theme specific styles */
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

        [data-theme="dark"] .alert {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        [data-theme="dark"] .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white !important;
        }

        [data-theme="dark"] .form-control {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .form-control:focus {
            background-color: var(--card-bg) !important;
            border-color: var(--primary-color) !important;
            color: var(--text-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
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

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--bg-secondary) !important;
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

        [data-theme="dark"] .nav-link {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .nav-link:hover {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .nav-link.active {
            color: var(--primary-color) !important;
        }

        [data-theme="dark"] .badge {
            color: white !important;
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

        [data-theme="dark"] .close {
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
        }rk-color);
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
            border-radius: 8px;
            padding: 0.5rem 0;
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

        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--bg-secondary) !important;
            transform: translateX(4px);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .user-profile {
            padding: 0.5rem !important;
            border-radius: 12px !important;
        }
        
        .user-avatar-container {
            position: relative;
            margin-right: 0.75rem;
        }
        
        .user-avatar-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .user-avatar-initials {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .user-status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid white;
            border-radius: 50%;
        }
        
        .user-info {
            line-height: 1.2;
        }
        
        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0;
        }
        
        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
            margin: 0;
        }

        .notification-bell {
            width: 40px;
            height: 40px;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }
        
        .notification-bell:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .notification-dropdown {
            width: 350px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
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
            max-height: 300px;
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
        
        .notification-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
        }
        
        .notification-empty i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .notification-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .user-dropdown {
            width: 280px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
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
            margin-right: 0.75rem;
        }
        
        .user-dropdown-avatar img,
        .user-dropdown-initials {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }
        
        .user-dropdown-initials {
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-dropdown-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
        }
        
        .user-dropdown-id {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0.125rem;
        }
        
        .user-dropdown-email {
            font-size: 0.75rem;
            opacity: 0.8;
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

        .notification-center {
            position: relative;
        }
        
        .notification-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.2s;
        }
        
        .notification-btn:hover {
            background: rgba(255, 255, 255, 0.2);
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
            color: white;
        }
        
        .user-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
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
            color: white;
            margin: 0;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar-close {
                display: block !important;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-value {
                font-size: 1.5rem;
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

        /* Force all table elements to be visible in dark mode */
        [data-theme="dark"] table,
        [data-theme="dark"] table *,
        [data-theme="dark"] .table *,
        [data-theme="dark"] .table-responsive *,
        [data-theme="dark"] tbody *,
        [data-theme="dark"] thead *,
        [data-theme="dark"] tr *,
        [data-theme="dark"] td,
        [data-theme="dark"] th {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        /* Override Bootstrap table defaults in dark mode */
        [data-theme="dark"] .table > :not(caption) > * > * {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
            border-bottom-color: var(--border-color) !important;
        }

        /* Force table visibility with highest specificity */
        [data-theme="dark"] .table-responsive .table tbody tr td,
        [data-theme="dark"] .table-responsive .table thead tr th {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        /* Target all table content in cards */
        [data-theme="dark"] .card .table td,
        [data-theme="dark"] .card .table th,
        [data-theme="dark"] .card table td,
        [data-theme="dark"] .card table th {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        /* Additional table fixes for Recent Payments and similar tables */
        [data-theme="dark"] .recent-payments-table,
        [data-theme="dark"] .recent-payments-table *,
        [data-theme="dark"] .payment-history-table,
        [data-theme="dark"] .payment-history-table * {
            background-color: var(--card-bg) !important;
            color: var(--text-primary) !important;
        }

        /* Force all Bootstrap grid columns */
        [data-theme="dark"] [class*="col-"] {
            color: var(--text-primary) !important;
        }

        /* Override any inline styles or specific table styling */
        [data-theme="dark"] .table-striped tbody tr:nth-of-type(odd) td,
        [data-theme="dark"] .table-striped tbody tr:nth-of-type(odd) th {
            background-color: var(--bg-secondary) !important;
        }

        [data-theme="dark"] .table-striped tbody tr:nth-of-type(even) td,
        [data-theme="dark"] .table-striped tbody tr:nth-of-type(even) th {
            background-color: var(--card-bg) !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('student.dashboard') }}" class="sidebar-brand d-flex align-items-center">
                <div class="brand-icon me-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-title">Edulink</div>
                    <div class="brand-subtitle">Student Portal</div>
                </div>
            </a>
            <button class="sidebar-close d-md-none" id="sidebarClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                       href="{{ route('student.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                
                <div class="nav-section">Academic</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.courses.*') ? 'active' : '' }}" 
                       href="{{ route('student.courses.index') }}">
                        <i class="bi bi-book"></i>
                        My Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.enrollments.*') ? 'active' : '' }}" 
                       href="{{ route('student.enrollments.index') }}">
                        <i class="bi bi-person-check"></i>
                        Enrollments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.enroll') ? 'active' : '' }}" 
                       href="{{ route('student.enroll') }}">
                        <i class="bi bi-plus-circle"></i>
                        Enroll in Course
                    </a>
                </li>
                
                <div class="nav-section">Financial</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}" 
                       href="{{ route('student.fees.index') }}">
                        <i class="bi bi-receipt"></i>
                        Fee Structure
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.payments.*') ? 'active' : '' }}" 
                       href="{{ route('student.payments.history') }}">
                        <i class="bi bi-credit-card"></i>
                        Payment History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.statements.*') ? 'active' : '' }}" 
                       href="{{ route('student.statements.index') }}">
                        <i class="bi bi-file-text"></i>
                        Statements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('payment.create') ? 'active' : '' }}" 
                       href="{{ route('payment.create') }}">
                        <i class="bi bi-plus-circle"></i>
                        Make Payment
                    </a>
                </li>
                
                <div class="nav-section">Account</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" 
                       href="{{ route('student.profile') }}">
                        <i class="bi bi-person"></i>
                        My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}" 
                       href="{{ route('student.notifications.index') }}">
                        <i class="bi bi-bell"></i>
                        Notifications
                        @php
                            $unreadCount = \App\Models\PaymentNotification::where('student_id', $student->id)->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.settings') ? 'active' : '' }}" 
                       href="{{ route('student.settings') }}">
                        <i class="bi bi-gear"></i>
                        Settings
                    </a>
                </li>
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
                <a href="{{ route('student.dashboard') }}" class="header-brand d-flex align-items-center">
                    <div class="header-brand-icon me-1">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="header-brand-text">
                        <div class="header-brand-title">Edulink</div>
                        <div class="header-brand-subtitle">Student Portal</div>
                    </div>
                </a>
            </div>
            
            <div class="header-right">
                <!-- Notifications -->
                <div class="notification-center me-3">
                    <button class="notification-btn" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        @php
                            $unreadCount = \App\Models\PaymentNotification::where('student_id', $student->id)->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="notification-count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <div class="notification-header">
                            <h6 class="mb-0">Notifications</h6>
                            @if($unreadCount > 0)
                                <small class="text-muted">{{ $unreadCount }} unread</small>
                            @endif
                        </div>
                        <div class="notification-list">
                            @php
                                $notifications = \App\Models\PaymentNotification::where('student_id', $student->id)
                                    ->latest()->take(5)->get();
                            @endphp
                            @forelse($notifications as $notification)
                            <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                <div class="notification-icon">
                                    @switch($notification->notification_type)
                                        @case('payment_success')
                                        @case('payment_confirmed')
                                            <i class="bi bi-check-circle text-success"></i>
                                            @break
                                        @case('payment_reminder')
                                        @case('payment_overdue')
                                            <i class="bi bi-alarm text-warning"></i>
                                            @break
                                        @case('enrollment')
                                            <i class="bi bi-person-check text-info"></i>
                                            @break
                                        @default
                                            <i class="bi bi-bell text-primary"></i>
                                    @endswitch
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ $notification->title }}</div>
                                    <div class="notification-message">{{ Str::limit($notification->message, 60) }}</div>
                                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="notification-empty">
                                <i class="bi bi-bell-slash"></i>
                                <p>No notifications yet</p>
                            </div>
                            @endforelse
                        </div>
                        @if($notifications->count() > 0)
                        <div class="notification-footer">
                            <a href="{{ route('student.notifications.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                View All Notifications
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="user-menu">
                    <div class="dropdown">
                        <button class="user-menu-btn d-flex align-items-center" 
                                type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                @if($student->profile_picture)
                                    <img src="{{ asset('storage/profile-pictures/' . $student->profile_picture) }}" 
                                         alt="Profile" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                @else
                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                @endif
                            </div>
                            <div class="user-info d-none d-md-block">
                                <div class="user-name">{{ $student->first_name }}</div>
                                <div class="user-role">Student</div>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end user-dropdown">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-avatar">
                                    @if($student->profile_picture)
                                        <img src="{{ asset('storage/profile-pictures/' . $student->profile_picture) }}" 
                                             alt="Profile" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div class="user-dropdown-initials">
                                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="user-dropdown-info">
                                    <div class="user-dropdown-name">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div class="user-dropdown-id">{{ $student->student_id }}</div>
                                    <div class="user-dropdown-email">{{ $student->email }}</div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('student.profile') }}">
                                <i class="bi bi-person me-2"></i>My Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('student.settings') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="bi bi-moon me-2"></i>Dark Mode
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('student.logout') }}">
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
            <div class="container-fluid">
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
    </div>

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
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });

            // Sidebar close functionality
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                });
            }

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
    
    <!-- Additional JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize theme first
            loadTheme();
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // Initialize theme
            loadTheme();

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

        // Theme toggle function
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const themeIcon = document.querySelector('.dropdown-item i.bi-moon');
            const themeText = document.querySelector('.dropdown-item i.bi-moon')?.parentElement;
            const headerIcon = document.getElementById('theme-icon');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('student-theme', 'light');
                if (themeIcon && themeText) {
                    themeIcon.className = 'bi bi-moon me-2';
                    themeText.innerHTML = '<i class="bi bi-moon me-2"></i>Dark Mode';
                }
                if (headerIcon) headerIcon.className = 'bi bi-moon-fill';
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('student-theme', 'dark');
                if (themeIcon && themeText) {
                    themeIcon.className = 'bi bi-sun me-2';
                    themeText.innerHTML = '<i class="bi bi-sun me-2"></i>Light Mode';
                }
                if (headerIcon) headerIcon.className = 'bi bi-sun-fill';
            }
        }
        
        // Load saved theme
        function loadTheme() {
            const savedTheme = localStorage.getItem('student-theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.querySelector('.dropdown-item i.bi-moon');
            const themeText = document.querySelector('.dropdown-item i.bi-moon')?.parentElement;
            
            html.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                if (themeIcon && themeText) {
                    themeIcon.className = 'bi bi-sun me-2';
                    themeText.innerHTML = '<i class="bi bi-sun me-2"></i>Light Mode';
                }
            } else {
                if (themeIcon && themeText) {
                    themeIcon.className = 'bi bi-moon me-2';
                    themeText.innerHTML = '<i class="bi bi-moon me-2"></i>Dark Mode';
                }
            }
        }
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
            
            // Add theme toggle button to header
            const headerRight = document.querySelector('.header-right');
            if (headerRight) {
                const themeToggle = document.createElement('button');
                themeToggle.className = 'theme-toggle me-2';
                themeToggle.onclick = toggleTheme;
                themeToggle.title = 'Toggle Theme';
                themeToggle.innerHTML = '<i class="bi bi-moon-fill" id="theme-icon"></i>';
                
                const notificationCenter = headerRight.querySelector('.notification-center');
                headerRight.insertBefore(themeToggle, notificationCenter);
                
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const headerIcon = document.getElementById('theme-icon');
                if (currentTheme === 'dark' && headerIcon) {
                    headerIcon.className = 'bi bi-sun-fill';
                }
            }
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
