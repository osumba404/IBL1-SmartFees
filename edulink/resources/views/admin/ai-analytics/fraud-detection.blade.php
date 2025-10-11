@extends('layouts.admin')

@section('title', 'Fraud Detection')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Fraud Detection</h1>
            <p class="text-muted">AI-powered fraud detection and risk analysis</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Suspicious Payment Activities</h5>
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
                            <th>Risk Score</th>
                            <th>Risk Factors</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suspiciousPayments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                            <td>KES {{ number_format($payment->amount, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span></td>
                            <td>
                                <span class="badge bg-{{ $payment->fraud_analysis['risk_level'] === 'High' ? 'danger' : ($payment->fraud_analysis['risk_level'] === 'Medium' ? 'warning' : 'info') }}">
                                    {{ $payment->fraud_analysis['risk_score'] }}% ({{ $payment->fraud_analysis['risk_level'] }})
                                </span>
                            </td>
                            <td>
                                @foreach($payment->fraud_analysis['risk_factors'] as $factor)
                                    <small class="d-block text-muted">• {{ $factor }}</small>
                                @endforeach
                            </td>
                            <td>
                                @if($payment->fraud_analysis['requires_review'])
                                    <button class="btn btn-sm btn-warning">Review Required</button>
                                @else
                                    <span class="text-success">✓ Normal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No suspicious activities detected</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection