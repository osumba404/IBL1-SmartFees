@extends('layouts.admin')

@section('title', 'Course Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Course Reports</h2>
            <p class="page-subtitle">View course enrollment and performance data</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Course Enrollment Statistics</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Total Enrollments</th>
                            <th>Active Enrollments</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->course_code }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->enrollments_count }}</td>
                            <td>{{ $course->active_enrollments_count }}</td>
                            <td><span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">{{ $course->is_active ? 'Active' : 'Inactive' }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No courses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection