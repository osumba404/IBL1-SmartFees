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
                            <td><span class="badge bg-light text-dark">{{ ucfirst($payment->payment_method) }}</span></td>
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