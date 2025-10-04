@extends('layouts.admin')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Financial Reports</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Payment History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $payment->student->first_name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</td>
                                <td>KSh {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference_number }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No payments found</td>
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