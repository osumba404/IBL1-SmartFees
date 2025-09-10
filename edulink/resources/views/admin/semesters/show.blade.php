@extends('layouts.admin')

@section('title', 'Semester Details - ' . $semester->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $semester->name }}</h1>
            <p class="text-muted mb-0">{{ $semester->semester_code }} • Academic Year {{ $semester->academic_year }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('admin.semesters.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Semesters
            </a>
        </div>
    </div>

    <!-- Status and Quick Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="text-muted mb-1">Status</h6>
                                    @switch($semester->status)
                                        @case('active')
                                            <span class="badge bg-success fs-6">Active</span>
                                            @break
                                        @case('upcoming')
                                            <span class="badge bg-info fs-6">Upcoming</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-secondary fs-6">Completed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger fs-6">Cancelled</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark fs-6">{{ ucfirst($semester->status) }}</span>
                                    @endswitch
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted mb-1">Period</h6>
                                    <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $semester->period)) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted mb-1">Duration</h6>
                                    <p class="mb-0">{{ $semester->start_date->format('M d') }} - {{ $semester->end_date->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted mb-1">Registration</h6>
                                    <p class="mb-0">{{ $semester->registration_start_date->format('M d') }} - {{ $semester->registration_end_date->format('M d') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.semesters.enrollments', $semester) }}">
                                            <i class="bi bi-people me-2"></i>View Enrollments
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.fee-structures.index', ['semester_id' => $semester->id]) }}">
                                            <i class="bi bi-currency-dollar me-2"></i>Fee Structures
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.semesters.toggle-status', $semester) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-toggle-{{ $semester->status === 'active' ? 'off' : 'on' }} me-2"></i>
                                                {{ $semester->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                                            <i class="bi bi-copy me-2"></i>Duplicate
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Enrollments</h6>
                            <h3 class="mb-0">{{ $stats['total_enrollments'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people-fill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Students</h6>
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check-fill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h3 class="mb-0">KSh {{ number_format($stats['total_revenue'], 2) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Fee Structures</h6>
                            <h3 class="mb-0">{{ $feeStructures->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Status Breakdown -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Enrollment Status</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="border-end">
                                <h4 class="text-success">{{ $stats['active_enrollments'] }}</h4>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $stats['pending_enrollments'] }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border-end">
                                <h4 class="text-secondary">{{ $stats['completed_enrollments'] }}</h4>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <h4 class="text-info">{{ $stats['total_enrollments'] - $stats['active_enrollments'] - $stats['pending_enrollments'] - $stats['completed_enrollments'] }}</h4>
                            <small class="text-muted">Other</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Courses by Enrollment</h5>
                </div>
                <div class="card-body">
                    @if($enrollmentsByCourse->count() > 0)
                        @foreach($enrollmentsByCourse->take(5) as $enrollment)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-medium">{{ $enrollment->course->name }}</span>
                                    <small class="text-muted d-block">{{ $enrollment->course->course_code }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $enrollment->count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No enrollments yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments and Fee Structures -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Enrollments</h5>
                    <a href="{{ route('admin.semesters.enrollments', $semester) }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEnrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</div>
                                                    <small class="text-muted">{{ $enrollment->student->student_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $enrollment->course->name }}</div>
                                                    <small class="text-muted">{{ $enrollment->course->course_code }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($enrollment->status)
                                                    @case('enrolled')
                                                        <span class="badge bg-success">Enrolled</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-secondary">Completed</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ ucfirst($enrollment->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $enrollment->created_at->format('M d, Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people display-4 text-muted mb-3"></i>
                            <p class="text-muted">No enrollments yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Fee Structures</h5>
                    <a href="{{ route('admin.fee-structures.create', ['semester_id' => $semester->id]) }}" class="btn btn-sm btn-outline-primary">Add New</a>
                </div>
                <div class="card-body p-0">
                    @if($feeStructures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeStructures as $feeStructure)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $feeStructure->course->name }}</div>
                                                    <small class="text-muted">{{ $feeStructure->course->course_code }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">KSh {{ number_format($feeStructure->total_amount, 2) }}</span>
                                            </td>
                                            <td>
                                                @switch($feeStructure->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Active</span>
                                                        @break
                                                    @case('inactive')
                                                        <span class="badge bg-warning">Inactive</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ ucfirst($feeStructure->status) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt display-4 text-muted mb-3"></i>
                            <p class="text-muted">No fee structures defined</p>
                            <a href="{{ route('admin.fee-structures.create', ['semester_id' => $semester->id]) }}" class="btn btn-primary">Create First Fee Structure</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Semester Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.semesters.duplicate', $semester) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Duplicate Semester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="duplicate_name" class="form-label">New Semester Name</label>
                        <input type="text" class="form-control" id="duplicate_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="duplicate_code" class="form-label">New Semester Code</label>
                        <input type="text" class="form-control" id="duplicate_code" name="semester_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="duplicate_year" class="form-label">Academic Year</label>
                        <input type="text" class="form-control" id="duplicate_year" name="academic_year" 
                               value="{{ date('Y') . '-' . (date('Y') + 1) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="duplicate_start" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="duplicate_start" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duplicate_end" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="duplicate_end" name="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Duplicate Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate code from name in duplicate modal
document.getElementById('duplicate_name').addEventListener('input', function() {
    const name = this.value;
    if (name && !document.getElementById('duplicate_code').value) {
        const code = name.toUpperCase().replace(/\s+/g, '').replace(/[^A-Z0-9]/g, '');
        document.getElementById('duplicate_code').value = code;
    }
});

// Date validation in duplicate modal
document.getElementById('duplicate_start').addEventListener('change', function() {
    const startDate = this.value;
    if (startDate) {
        document.getElementById('duplicate_end').min = startDate;
    }
});
</script>
@endpush
