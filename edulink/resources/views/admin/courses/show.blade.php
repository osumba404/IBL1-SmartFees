@extends('layouts.admin')

@section('title', 'Course Details - ' . $course->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $course->name }}</h1>
            <p class="text-muted mb-0">{{ $course->course_code }} • {{ ucfirst($course->level) }}</p>
        </div>
        <div>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Courses
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit Course
            </a>
        </div>
    </div>

    <!-- Course Status Badge -->
    <div class="mb-4">
        @if($course->status === 'active')
            <span class="badge bg-success fs-6">Active</span>
        @elseif($course->status === 'inactive')
            <span class="badge bg-warning fs-6">Inactive</span>
        @else
            <span class="badge bg-danger fs-6">Discontinued</span>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_students'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Enrollments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_enrollments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Enrollments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_enrollments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Course Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Course Code:</strong></div>
                        <div class="col-sm-8">{{ $course->course_code }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Department:</strong></div>
                        <div class="col-sm-8">{{ $course->department ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Level:</strong></div>
                        <div class="col-sm-8">{{ ucfirst($course->level) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Duration:</strong></div>
                        <div class="col-sm-8">{{ $course->duration_months }} months</div>
                    </div>
                    @if($course->credit_hours)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Credit Hours:</strong></div>
                        <div class="col-sm-8">{{ $course->credit_hours }}</div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Total Fee:</strong></div>
                        <div class="col-sm-8">KSh {{ number_format($course->total_fee, 2) }}</div>
                    </div>
                    @if($course->max_students)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Max Students:</strong></div>
                        <div class="col-sm-8">{{ $course->max_students }}</div>
                    </div>
                    @endif
                    @if($course->description)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Description:</strong></div>
                        <div class="col-sm-8">{{ $course->description }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Enrollments</h6>
                    <a href="{{ route('admin.courses.students', $course) }}" class="btn btn-sm btn-outline-primary">
                        View All Students
                    </a>
                </div>
                <div class="card-body">
                    @if($recentEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEnrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="font-weight-bold">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</div>
                                                    <div class="text-muted small">{{ $enrollment->student->student_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($enrollment->status === 'enrolled')
                                                <span class="badge bg-success">Enrolled</span>
                                            @elseif($enrollment->status === 'completed')
                                                <span class="badge bg-primary">Completed</span>
                                            @elseif($enrollment->status === 'withdrawn')
                                                <span class="badge bg-danger">Withdrawn</span>
                                            @elseif($enrollment->status === 'deferred')
                                                <span class="badge bg-warning">Deferred</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $enrollment->semester->name ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No enrollments found for this course.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Course
                        </a>
                        <a href="{{ route('admin.courses.students', $course) }}" class="btn btn-outline-primary">
                            <i class="fas fa-users me-1"></i> View Students
                        </a>
                        <a href="{{ route('admin.courses.fee-structures', $course) }}" class="btn btn-outline-primary">
                            <i class="fas fa-money-bill me-1"></i> Fee Structures
                        </a>
                        <button type="button" class="btn btn-outline-warning" onclick="toggleStatus({{ $course->id }})">
                            <i class="fas fa-toggle-{{ $course->status === 'active' ? 'on' : 'off' }} me-1"></i>
                            {{ $course->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fee Structures -->
            @if($feeStructures->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Fee Structures</h6>
                </div>
                <div class="card-body">
                    @foreach($feeStructures as $feeStructure)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="font-weight-bold">{{ $feeStructure->semester->name ?? 'N/A' }}</div>
                                <div class="text-muted small">KSh {{ number_format($feeStructure->total_amount, 2) }}</div>
                            </div>
                            <span class="badge bg-{{ $feeStructure->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($feeStructure->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(courseId) {
    if (confirm('Are you sure you want to change the course status?')) {
        fetch(`/admin/courses/${courseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating course status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating course status');
        });
    }
}
</script>
@endpush

@endsection
