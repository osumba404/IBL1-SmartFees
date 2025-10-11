@extends('layouts.admin')

@section('title', 'Payment Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Payment Reports</h2>
            <p class="page-subtitle">View and analyze payment transactions</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Total Amount</h5>
                    <h3 class="text-primary">KES {{ number_format($stats['total_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Completed</h5>
                    <h3 class="text-success">KES {{ number_format($stats['completed_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Pending</h5>
                    <h3 class="text-warning">KES {{ number_format($stats['pending_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Total Transactions</h5>
                    <h3 class="text-info">{{ number_format($stats['total_count']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Payment Transactions</h5>
            <a href="{{ route('admin.reports.export.payments') }}" class="btn btn-sm btn-success">Export CSV</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                            <td>KES {{ number_format($payment->amount, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span></td>
                            <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($payment->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No payments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Dark Mode Fixes for Payment Reports */
[data-theme="dark"] .page-title,
[data-theme="dark"] .page-subtitle {
    color: var(--bs-light) !important;
}

[data-theme="dark"] .card {
    background-color: var(--bs-dark) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .card-header {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .card-body {
    color: var(--bs-light) !important;
}

[data-theme="dark"] .card-body h5,
[data-theme="dark"] .card-body h3 {
    color: var(--bs-light) !important;
}

[data-theme="dark"] .text-primary {
    color: var(--bs-primary) !important;
}

[data-theme="dark"] .text-success {
    color: var(--bs-success) !important;
}

[data-theme="dark"] .text-warning {
    color: var(--bs-warning) !important;
}

[data-theme="dark"] .text-info {
    color: var(--bs-info) !important;
}

/* Table styling in dark mode */
[data-theme="dark"] .table {
    --bs-table-bg: var(--bs-dark) !important;
    --bs-table-color: var(--bs-light) !important;
    color: var(--bs-light) !important;
    background-color: var(--bs-dark) !important;
}

[data-theme="dark"] .table th,
[data-theme="dark"] .table td {
    color: var(--bs-light) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

[data-theme="dark"] .table thead th {
    background-color: rgba(255, 255, 255, 0.05) !important;
    color: var(--bs-light) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

[data-theme="dark"] .table-hover tbody tr:hover {
    --bs-table-hover-bg: rgba(255, 255, 255, 0.075) !important;
    background-color: rgba(255, 255, 255, 0.075) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .table-hover tbody tr:hover td {
    color: var(--bs-light) !important;
}

/* Badge styling in dark mode */
[data-theme="dark"] .badge.bg-light {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .badge.bg-success {
    background-color: var(--bs-success) !important;
    color: white !important;
}

[data-theme="dark"] .badge.bg-warning {
    background-color: var(--bs-warning) !important;
    color: black !important;
}

[data-theme="dark"] .badge.bg-danger {
    background-color: var(--bs-danger) !important;
    color: white !important;
}

/* Pagination in dark mode */
[data-theme="dark"] .pagination .page-link {
    background-color: var(--bs-dark) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .pagination .page-link:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    color: var(--bs-light) !important;
}

[data-theme="dark"] .pagination .page-item.active .page-link {
    background-color: var(--bs-primary) !important;
    border-color: var(--bs-primary) !important;
    color: white !important;
}

[data-theme="dark"] .pagination .page-item.disabled .page-link {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
    color: rgba(255, 255, 255, 0.3) !important;
}

/* Empty state text */
[data-theme="dark"] .text-center {
    color: rgba(255, 255, 255, 0.7) !important;
}

/* Export button styling */
[data-theme="dark"] .btn-success {
    background-color: var(--bs-success) !important;
    border-color: var(--bs-success) !important;
    color: white !important;
}

[data-theme="dark"] .btn-success:hover {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: white !important;
}
</style>
@endpush