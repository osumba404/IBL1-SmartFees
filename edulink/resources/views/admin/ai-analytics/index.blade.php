@extends('layouts.admin')

@section('title', 'AI Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">AI Analytics Dashboard</h1>
            <p class="text-muted">AI-powered insights and predictions</p>
        </div>
    </div>

    <!-- Interactive Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white cursor-pointer" onclick="showTrendDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Payment Trend</h6>
                            <h4 id="trend-value">{{ ucfirst($analytics['payment_predictions']['trend']) }}</h4>
                            <small>Next month prediction</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>Growth: <span id="growth-rate">{{ number_format($detailedMetrics['payment_trends']['growth_rate'] ?? 0, 1) }}%</span></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white cursor-pointer" onclick="showRevenueDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Revenue Forecast</h6>
                            <h4 id="revenue-value">KES {{ number_format($analytics['revenue_projection']['next_quarter']) }}</h4>
                            <small>Next quarter</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>Confidence: <span class="badge bg-light text-dark">{{ ucfirst($detailedMetrics['revenue_forecast']['confidence'] ?? 'medium') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white cursor-pointer" onclick="showRiskDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>At-Risk Students</h6>
                            <h4 id="risk-count">{{ $analytics['at_risk_students']->count() }}</h4>
                            <small>Require attention</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>High Risk: <span id="high-risk-count">{{ $detailedMetrics['risk_analysis']['categories']['high_risk'] ?? 0 }}</span></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white cursor-pointer" onclick="showMethodDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Best Method</h6>
                            <h4 id="best-method">{{ ucfirst($detailedMetrics['payment_methods']['recommended_method'] ?? 'M-Pesa') }}</h4>
                            <small>Highest success rate</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-credit-card fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>Success: <span id="success-rate">{{ number_format(($detailedMetrics['payment_methods']['method_stats'][$detailedMetrics['payment_methods']['recommended_method'] ?? 'mpesa']['success_rate'] ?? 95), 1) }}%</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Enhanced Payment Behavior Analysis -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Payment Behavior Insights</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="updatePeriod(30)">30D</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updatePeriod(90)">90D</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updatePeriod(365)">1Y</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Payment Methods Performance</h6>
                        @foreach($paymentBehavior['preferred_methods'] as $method => $count)
                            @php
                                $percentage = $paymentBehavior['preferred_methods']->sum() > 0 ? ($count / $paymentBehavior['preferred_methods']->sum()) * 100 : 0;
                                $successRate = $detailedMetrics['payment_methods']['method_stats'][$method]['success_rate'] ?? 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-bold">{{ ucfirst($method) }}</span>
                                    <small class="text-muted">({{ $count }} transactions)</small>
                                </div>
                                <div class="text-end">
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-success">{{ number_format($successRate, 1) }}% success</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ number_format($paymentBehavior['payment_frequency'], 1) }}</h4>
                                <small class="text-muted">Payments/Day</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success mb-0">KES {{ number_format($paymentBehavior['amount_patterns']['average']) }}</h4>
                                <small class="text-muted">Avg Amount</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info mb-0">{{ number_format(($detailedMetrics['payment_methods']['method_stats']->avg('success_rate') ?? 0), 1) }}%</h4>
                            <small class="text-muted">Overall Success</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>At-Risk Students</h5>
                    <a href="{{ route('admin.ai-analytics.fraud-detection') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($analytics['at_risk_students']->take(5) as $student)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                <br><small class="text-muted">{{ $student->email }}</small>
                            </div>
                            <span class="badge bg-warning">At Risk</span>
                        </div>
                    @empty
                        <p class="text-muted">No at-risk students identified</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- AI Recommendations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>AI Recommendations</h5>
                </div>
                <div class="card-body">
                    @forelse($recommendations as $recommendation)
                        <div class="alert alert-{{ $recommendation['type'] }}">
                            <i class="{{ $recommendation['icon'] }} me-2"></i>
                            <strong>{{ $recommendation['title'] }}:</strong> {{ $recommendation['message'] }}
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>No recommendations available:</strong> Insufficient data to generate AI recommendations. More data will be available as the system is used.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTrendDetails() {
    // Show detailed trend modal or expand card
    alert('Payment trend details: Click to see 30-day trend analysis');
}

function showRevenueDetails() {
    // Show revenue forecast details
    alert('Revenue forecast: Based on historical data and growth patterns');
}

function showRiskDetails() {
    // Show risk analysis details
    alert('Risk analysis: Students with payment delays or failures');
}

function showMethodDetails() {
    // Show payment method performance
    alert('Payment methods: Success rates and processing times');
}

function updatePeriod(days) {
    // Update analytics for selected period
    fetch(`{{ route('admin.ai-analytics.index') }}/data?type=payment_methods&period=${days}`)
        .then(response => response.json())
        .then(data => {
            // Update the UI with new data
            console.log('Updated data for', days, 'days:', data);
        })
        .catch(error => console.error('Error:', error));
    
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

// Auto-refresh data every 5 minutes
setInterval(() => {
    // Refresh key metrics
    fetch(`{{ route('admin.ai-analytics.index') }}/data?type=payment_trends`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('growth-rate').textContent = data.growth_rate.toFixed(1) + '%';
        });
}, 300000);

// Add hover effects and tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Add cursor pointer style
    const style = document.createElement('style');
    style.textContent = '.cursor-pointer { cursor: pointer; transition: transform 0.2s; } .cursor-pointer:hover { transform: translateY(-2px); }';
    document.head.appendChild(style);
});
</script>
@endpush