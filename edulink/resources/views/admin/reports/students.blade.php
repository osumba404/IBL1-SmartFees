@extends('layouts.admin')

@section('title', 'Student Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Student Reports</h2>
            <p class="page-subtitle">View student enrollment and status data</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Total Students</h5>
                    <h3 class="text-primary">{{ number_format($stats['total_students']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Active</h5>
                    <h3 class="text-success">{{ number_format($stats['active_students']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Pending</h5>
                    <h3 class="text-warning">{{ number_format($stats['pending_students']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Suspended</h5>
                    <h3 class="text-danger">{{ number_format($stats['suspended_students']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Student List</h5>
            <a href="{{ route('admin.reports.export.students') }}" class="btn btn-sm btn-success">Export CSV</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Enrollments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone }}</td>
                            <td><span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($student->status) }}</span></td>
                            <td>{{ $student->enrollments->count() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No students found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection