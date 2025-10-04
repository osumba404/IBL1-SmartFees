@extends('layouts.student')

@section('title', 'Fee Statements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Fee Statements</h1>
        </div>
    </div>

    @if($enrollments->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Available Statements</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Total Fees</th>
                                    <th>Amount Paid</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->course->name }}</td>
                                    <td>{{ $enrollment->semester->name ?? 'N/A' }}</td>
                                    <td>KSh {{ number_format($enrollment->total_fees_due, 2) }}</td>
                                    <td class="text-success">KSh {{ number_format($enrollment->fees_paid, 2) }}</td>
                                    <td class="text-danger">KSh {{ number_format($enrollment->outstanding_balance, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($enrollment->payments->count() > 0)
                                        <a href="{{ route('student.statements.download', ['enrollment_id' => $enrollment->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        @else
                                        <span class="text-muted">No payments</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($statementPeriods->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Payment Activity</h5>
                </div>
                <div class="card-body">
                    @foreach($statementPeriods as $enrollment)
                        @if($enrollment->payments->count() > 0)
                        <div class="mb-3">
                            <h6>{{ $enrollment->course->name }} - {{ $enrollment->semester->name ?? 'N/A' }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enrollment->payments->take(5) as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>KSh {{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h5>No Statements Available</h5>
                <p>You don't have any enrollments with payment history to generate statements.</p>
                <a href="{{ route('student.enroll') }}" class="btn btn-primary">Enroll in a Course</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection