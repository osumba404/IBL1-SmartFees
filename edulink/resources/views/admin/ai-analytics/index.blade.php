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

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Payment Trend</h6>
                    <h4>{{ ucfirst($analytics['payment_predictions']['trend']) }}</h4>
                    <small>Next month prediction</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Revenue Forecast</h6>
                    <h4>KES {{ number_format($analytics['revenue_projection']['next_quarter']) }}</h4>
                    <small>Next quarter</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>At-Risk Students</h6>
                    <h4>{{ $analytics['at_risk_students']->count() }}</h4>
                    <small>Require attention</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Avg Risk Score</h6>
                    <h4>{{ number_format($paymentBehavior['risk_score'], 1) }}%</h4>
                    <small>System-wide</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Behavior Analysis -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Behavior Insights</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Preferred Payment Methods:</strong>
                        @foreach($paymentBehavior['preferred_methods'] as $method => $count)
                            <span class="badge bg-secondary me-1">{{ ucfirst($method) }}: {{ $count }}</span>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <strong>Payment Frequency:</strong> 
                        {{ number_format($paymentBehavior['payment_frequency'], 2) }} payments/day
                    </div>
                    <div class="mb-3">
                        <strong>Average Amount:</strong> 
                        KES {{ number_format($paymentBehavior['amount_patterns']['average']) }}
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
                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Recommendation:</strong> Based on payment trends, consider implementing automated payment reminders for students with overdue balances.
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-graph-up me-2"></i>
                        <strong>Insight:</strong> M-Pesa payments have a 95% success rate. Promote this method to reduce failed transactions.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection