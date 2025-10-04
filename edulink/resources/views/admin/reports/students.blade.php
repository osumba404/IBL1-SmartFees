@extends('layouts.admin')

@section('title', 'Student Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Student Reports</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Student List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Courses</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if($student->enrollments->count() > 0)
                                        @foreach($student->enrollments as $enrollment)
                                            <span class="badge bg-info">{{ $enrollment->course->name ?? 'N/A' }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No enrollments</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td>{{ $student->created_at->format('Y-m-d') }}</td>
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