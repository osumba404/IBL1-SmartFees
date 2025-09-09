@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ $admin->name }}! Here's what's happening at Edulink International College.</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-value">{{ number_format($stats['total_students']) }}</div>
            <div class="stats-label">Total Students</div>
            <div class="mt-2">
                <small>
                    <i class="bi bi-arrow-up"></i>
                    +{{ $stats['new_students_this_month'] }} this month
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="stats-value">KSh {{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stats-label">Total Revenue</div>
            <div class="mt-2">
                <small>
                    <i class="bi bi-arrow-up"></i>
                    +{{ number_format($stats['revenue_growth_percentage'], 1) }}% from last month
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="stats-value">{{ number_format($stats['pending_payments']) }}</div>
            <div class="stats-label">Pending Payments</div>
            <div class="mt-2">
                <small>
                    KSh {{ number_format($stats['pending_amount'], 2) }} total
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card danger">
            <div class="stats-value">{{ number_format($stats['overdue_payments']) }}</div>
            <div class="stats-label">Overdue Payments</div>
            <div class="mt-2">
                <small>
                    KSh {{ number_format($stats['overdue_amount'], 2) }} total
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Revenue Trends</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue7days" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="revenue7days">7 Days</label>
                    
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue30days" autocomplete="off">
                    <label class="btn btn-outline-primary" for="revenue30days">30 Days</label>
                    
                    <input type="radio" class="btn-check" name="revenueChart" id="revenue12months" autocomplete="off">
                    <label class="btn btn-outline-primary" for="revenue12months">12 Months</label>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Methods</h5>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity and Quick Actions -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Payments</h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($payment->student->first_name, 0, 1) }}{{ substr($payment->student->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</div>
                                            <small class="text-muted">{{ $payment->student->student_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">KSh {{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        @switch($payment->payment_method)
                                            @case('mpesa')
                                                <i class="bi bi-phone me-1"></i>M-Pesa
                                                @break
                                            @case('stripe')
                                                <i class="bi bi-credit-card me-1"></i>Card
                                                @break
                                            @case('bank_transfer')
                                                <i class="bi bi-bank me-1"></i>Bank
                                                @break
                                            @case('cash')
                                                <i class="bi bi-cash me-1"></i>Cash
                                                @break
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    @switch($payment->status)
                                        @case('completed')
                                            <span class="badge bg-success">Completed</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger">Failed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-secondary">Cancelled</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    No recent payments found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add New Student
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-book me-2"></i>Create Course
                    </a>
                    <a href="{{ route('admin.semesters.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add Semester
                    </a>
                    <a href="{{ route('admin.fee-structures.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-currency-dollar me-2"></i>Set Fee Structure
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>M-Pesa Integration</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Stripe Integration</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Email Notifications</span>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>SMS Notifications</span>
                    <span class="badge bg-warning">Limited</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts and Notifications -->
@if($alerts->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Alerts</h5>
            </div>
            <div class="card-body">
                @foreach($alerts as $alert)
                <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-{{ $alert['icon'] }} me-2"></i>
                    <strong>{{ $alert['title'] }}</strong> {{ $alert['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    let revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['revenue']['labels']),
            datasets: [{
                label: 'Revenue (KSh)',
                data: @json($chartData['revenue']['data']),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + value.toLocaleString();
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(paymentMethodsCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['paymentMethods']['labels']),
            datasets: [{
                data: @json($chartData['paymentMethods']['data']),
                backgroundColor: [
                    '#2563eb',
                    '#059669',
                    '#d97706',
                    '#dc2626'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Revenue chart period toggle
    document.querySelectorAll('input[name="revenueChart"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const period = this.id.replace('revenue', '');
            updateRevenueChart(period);
        });
    });

    function updateRevenueChart(period) {
        // Show loading state
        revenueChart.data.labels = ['Loading...'];
        revenueChart.data.datasets[0].data = [0];
        revenueChart.update();

        // Fetch new data
        fetch(`{{ route('admin.dashboard.revenue-data') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.data;
                revenueChart.update();
            })
            .catch(error => {
                console.error('Error fetching revenue data:', error);
            });
    }

    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
}

.stats-card {
    position: relative;
    overflow: hidden;
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

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
}

.table tbody tr:hover {
    background-color: rgba(37, 99, 235, 0.05);
}

.btn-group .btn-check:checked + .btn-outline-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}
</style>
@endpush
