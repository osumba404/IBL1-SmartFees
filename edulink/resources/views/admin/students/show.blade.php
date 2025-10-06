@extends('layouts.admin')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Details - {{ $student->full_name }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Profile Picture Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    @if($student->profile_picture)
                                        <img src="{{ asset('storage/profile-pictures/' . $student->profile_picture) }}" 
                                             alt="{{ $student->full_name }}" 
                                             class="rounded-circle" 
                                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 120px; height: 120px; font-size: 2.5rem; border: 3px solid #e9ecef;">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $student->full_name }}</h4>
                                    <p class="text-muted mb-1">Student ID: {{ $student->student_id }}</p>
                                    <span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'suspended' ? 'danger' : 'warning') }} fs-6">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Personal Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Student ID:</strong></td>
                                    <td>{{ $student->student_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $student->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $student->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td>{{ $student->date_of_birth?->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ ucfirst($student->gender) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>National ID:</strong></td>
                                    <td>{{ $student->national_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'suspended' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Contact & Emergency Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $student->address ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Emergency Contact:</strong></td>
                                    <td>{{ $student->emergency_contact_name ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Emergency Phone:</strong></td>
                                    <td>{{ $student->emergency_contact_phone ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Enrollment Date:</strong></td>
                                    <td>{{ $student->enrollment_date?->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>{{ $student->last_login_at?->format('M d, Y H:i') ?: 'Never' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($student->enrollments->count() > 0)
                    <hr>
                    <h6 class="text-primary">Enrollments</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Total Fee</th>
                                    <th>Paid</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->course?->name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->semester?->name ?? 'N/A' }}</td>
                                    <td>KES {{ number_format($enrollment->total_fees_due, 2) }}</td>
                                    <td>KES {{ number_format($enrollment->fees_paid, 2) }}</td>
                                    <td>
                                        <span class="text-{{ $enrollment->outstanding_balance > 0 ? 'danger' : 'success' }}">
                                            KES {{ number_format($enrollment->outstanding_balance, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'enrolled' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.students.enrollments', $student) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if($student->payments->count() > 0)
                    <hr>
                    <h6 class="text-primary">Recent Payments</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->payments->take(5) as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                                    <td>KES {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->reference_number }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($student->payments->count() > 5)
                        <div class="text-center">
                            <a href="{{ route('admin.students.payments', $student) }}" class="btn btn-outline-primary">
                                View All Payments
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection