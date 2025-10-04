@extends('layouts.student')

@section('title', 'My Enrollments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">My Enrollments</h1>
        </div>
    </div>

    @if($enrollments->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Enrollment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Enrollment #</th>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Enrollment Date</th>
                                    <th>Status</th>
                                    <th>Payment Plan</th>
                                    <th>Outstanding</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->enrollment_number }}</td>
                                    <td>{{ $enrollment->course->name }}</td>
                                    <td>{{ $enrollment->semester->name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $enrollment->payment_plan)) }}</td>
                                    <td class="text-danger">KSh {{ number_format($enrollment->outstanding_balance, 2) }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('student.enrollments.show', $enrollment) }}" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($enrollment->outstanding_balance > 0)
                                            <a href="{{ route('student.payments.create') }}" 
                                               class="btn btn-outline-success" title="Make Payment">
                                                <i class="bi bi-credit-card"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $enrollments->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-book display-4 text-primary mb-3"></i>
                    <h5>Total Enrollments</h5>
                    <h2 class="text-primary">{{ $enrollments->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                    <h5>Active Enrollments</h5>
                    <h2 class="text-success">{{ $enrollments->where('status', 'enrolled')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar display-4 text-warning mb-3"></i>
                    <h5>Outstanding Balance</h5>
                    <h2 class="text-warning">KSh {{ number_format($enrollments->sum('outstanding_balance'), 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle display-4 mb-3"></i>
                <h4>No Enrollments Found</h4>
                <p>You haven't enrolled in any courses yet.</p>
                <a href="{{ route('student.enroll') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Enroll in a Course
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection