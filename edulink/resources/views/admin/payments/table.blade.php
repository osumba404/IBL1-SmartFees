<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ $payment->transaction_id }}</td>
                <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                <td>KES {{ number_format($payment->amount, 2) }}</td>
                <td>
                    <span class="badge bg-info">{{ ucfirst($payment->payment_method) }}</span>
                </td>
                <td>
                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No payments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($payments->hasPages())
<div class="d-flex justify-content-center">
    {{ $payments->links() }}
</div>
@endif