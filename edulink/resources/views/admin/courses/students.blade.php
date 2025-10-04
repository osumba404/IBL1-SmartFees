@extends('layouts.admin')

@section('title', 'Students - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Course Students</h1>
                    <p class="text-muted">{{ $course->name }} ({{ $course->course_code }})</p>
                </div>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enrolled Students ({{ $students->total() }})</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Enrollment Status</th>
                                <th>Enrollment Date</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $enrollment = $student->enrollments->first();
                                @endphp
                                <tr>
                                    <td>{{ $student->student_id }}</td>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        @if($enrollment)
                                            <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'warning') }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Enrollment</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $enrollment ? $enrollment->created_at->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($enrollment)
                                            @php
                                                $totalPaid = $enrollment->payments()->where('status', 'completed')->sum('amount');
                                                $totalFee = $course->total_fee;
                                                $percentage = $totalFee > 0 ? ($totalPaid / $totalFee) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ min($percentage, 100) }}%"></div>
                                                </div>
                                                <small>{{ number_format($percentage, 1) }}%</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.students.show', $student) }}" 
                                               class="btn btn-outline-primary" title="View Student">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($enrollment)
                                                <a href="{{ route('admin.students.enrollments', $student) }}" 
                                                   class="btn btn-outline-info" title="View Enrollments">
                                                    <i class="bi bi-book"></i>
                                                </a>
                                                <a href="{{ route('admin.students.payments', $student) }}" 
                                                   class="btn btn-outline-success" title="View Payments">
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
                
                {{ $students->links() }}
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h5 class="mt-3">No Students Enrolled</h5>
                    <p class="text-muted">No students have enrolled in this course yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Export students enrolled in <strong>{{ $course->name }}</strong> to CSV format.</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="includePayments" checked>
                    <label class="form-check-label" for="includePayments">
                        Include payment information
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="includeEnrollmentDetails" checked>
                    <label class="form-check-label" for="includeEnrollmentDetails">
                        Include enrollment details
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('admin.courses.export', ['course' => $course->id, 'type' => 'students']) }}" 
                   class="btn btn-primary">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>
    </div>
</div>
@endsection